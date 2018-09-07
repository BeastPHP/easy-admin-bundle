<?php

namespace Beast\EasyAdminBundle\Helper\Geetest;

/**
 * 极验Sdk for Symfony
 *
 * @author Xin Guo
 */
class Geetest
{
    const GT_SERVER_STATUS_KEY = 'gt_server_status';

    const GT_SDK_VERSION = 'php_3.0.0';

    const CONNECT_TIMEOUT = 1;
    const SOCKET_TIMEOUT = 1;

    private $response;

    protected $captchaId;
    protected $privateKey;

    public function __construct($captchaId, $privateKey)
    {
        $this->captchaId = $captchaId;
        $this->privateKey = $privateKey;
    }

    /**
     * 判断极验服务器是否down机
     *
     * @param array $param
     * @param int $newCaptcha
     *
     * @return int
     */
    public function proProcess($param, $newCaptcha = 1)
    {
        $data = array(
            'gt' => $this->captchaId,
            'new_captcha' => $newCaptcha
        );

        $data = array_merge($data, $param);
        $query = http_build_query($data);
        $url = 'http://api.geetest.com/register.php?' . $query;
        $challenge = $this->sendRequest($url);
        if (strlen($challenge) != 32) {
            $this->failbackProcess();
            return 0;
        }
        $this->successProcess($challenge);
        return 1;
    }

    /**
     * @param $challenge
     */
    private function successProcess($challenge)
    {
        $challenge = md5($challenge . $this->privateKey);
        $result = array(
            'success' => 1,
            'gt' => $this->captchaId,
            'challenge' => $challenge,
            'new_captcha' => 1
        );
        $this->response = $result;
    }

    private function failbackProcess()
    {
        $rnd1 = md5(mt_rand(0, 100));
        $rnd2 = md5(mt_rand(0, 100));
        $challenge = $rnd1 . substr($rnd2, 0, 2);
        $result = array(
            'success' => 0,
            'gt' => $this->captchaId,
            'challenge' => $challenge,
            'new_captcha' => 1
        );
        $this->response = $result;
    }

    /**
     * @return mixed
     */
    public function getResponseStr()
    {
        return json_encode($this->response);
    }

    /**
     * 返回数组方便扩展
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * 正常模式获取验证结果
     *
     * @param $challenge
     * @param $validate
     * @param $seccode
     * @param $param
     * @param int $jsonFormat
     *
     * @return int
     */
    public function successValidate($challenge, $validate, $seccode, $param, $jsonFormat = 1): int
    {
        if (!$this->checkValidate($challenge, $validate)) {
            return 0;
        }
        $query = array(
            'seccode' => $seccode,
            'timestamp' => time(),
            'challenge' => $challenge,
            'captchaid' => $this->captchaId,
            'json_format' => $jsonFormat,
            'sdk' => self::GT_SDK_VERSION
        );
        $query = array_merge($query, $param);
        $url = 'http://api.geetest.com/validate.php';
        $codevalidate = $this->postRequest($url, $query);
        $obj = json_decode($codevalidate, true);

        if ($obj && $obj['seccode'] == md5($seccode)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 宕机模式获取验证结果
     *
     * @param $challenge
     * @param $validate
     *
     * @return int
     */
    public function failValidate($challenge, $validate)
    {
        if (md5($challenge) == $validate) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @param $challenge
     * @param $validate
     *
     * @return bool
     */
    private function checkValidate($challenge, $validate)
    {
        if (strlen($validate) != 32) {
            return false;
        }

        if (md5($this->privateKey . 'geetest' . $challenge) != $validate) {
            return false;
        }

        return true;
    }

    /**
     * GET 请求
     *
     * @param $url
     *
     * @return mixed|string
     */
    private function sendRequest($url)
    {
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::SOCKET_TIMEOUT);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $curlError = curl_errno($ch);
            $data = curl_exec($ch);
            curl_close($ch);
            if ($curlError > 0) {
                return 0;
            } else {
                return $data;
            }
        } else {
            $opts = array(
                'http' => array(
                    'method' => 'GET',
                    'timeout' => self::CONNECT_TIMEOUT + self::SOCKET_TIMEOUT,
                )
            );
            $context = stream_context_create($opts);
            try {
                $data = @file_get_contents($url, false, $context);

                if ($data) {
                    return $data;
                } else {
                    return 0;
                }
            } catch (\Exception $e) {
                return 0;
            }
        }
    }

    /**
     *
     * @param       $url
     * @param array $postData
     *
     * @return mixed|string
     */
    private function postRequest($url, $postData = array())
    {
        if (!$postData) {
            return false;
        }

        $data = http_build_query($postData);
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::SOCKET_TIMEOUT);

            //不可能执行到的代码
            if (!$postData) {
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $err = sprintf("curl[%s] error[%s]", $url, curl_errno($ch) . ':' . curl_error($ch));
                $this->triggerError($err);
            }

            curl_close($ch);
        } else {
            if ($postData) {
                $header = array(
                    'Content-type: application/x-www-form-urlencoded',
                    sprintf("Content-Length: %s \r\n", strlen($data)),
                );
                $opts = array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => implode("\r\n", $header),
                        'content' => $data,
                        'timeout' => self::CONNECT_TIMEOUT + self::SOCKET_TIMEOUT
                    )
                );
                $context = stream_context_create($opts);
                $data = file_get_contents($url, false, $context);
            }
        }

        return $data;
    }

    /**
     * @param $err
     */
    private function triggerError($err)
    {
        trigger_error($err);
    }
}
