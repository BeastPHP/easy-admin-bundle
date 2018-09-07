<?php

namespace Beast\EasyAdminBundle\Helper\Rest;

class StatusCode
{
    const CODE_SYSTEM_ERROR = 0;
    const CODE_TRANSCATION_INVALID = 1;
    const CODE_USER_INVALID = 2;

    const MSG_SYSTEM_ERROR = '系统错误';
    const MSG_TRANSCATION_INVALID = '无效的transcation_id';
    const MSG_USER_INVALID = '您请求的信息不存在, 请确认您使用的是正确的token';

    const CODE_BASE_FOR_MEMBER = '10000';
    const CODE_BASE_FOR_CONFIG = '20000';
    const CODE_BASE_FOR_GAME = '30000';

    /**
     * @return array
     */
    protected static function getDefaultCodes()
    {
        return array(
            self::CODE_SYSTEM_ERROR        => self::MSG_SYSTEM_ERROR,
            self::CODE_TRANSCATION_INVALID => self::MSG_TRANSCATION_INVALID,
        );
    }

    /**
     * @return array
     */
    protected static function getDefaultCodesWithUser()
    {
        return self::getDefaultCodes() + array(
                self::CODE_USER_INVALID => self::MSG_USER_INVALID,
            );
    }

    /**
     * @return array
     */
    protected function getCodes()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function getMessages()
    {
        return array();
    }

    /**
     * @param $key
     * @param null $statusCode
     *
     * @return mixed|string
     */
    public static function getStatusCode($key, $statusCode = null)
    {
        if ($statusCode == null) {
            $statusCode = new StatusCode();
        }
        $codes = $statusCode->getCodes();
        return isset($codes[$key]) ? $codes[$key] : '';
    }

    /**
     * @param $key
     * @param null $statusCode
     *
     * @return mixed|string
     */
    public static function getMessage($key, $statusCode = null)
    {
        if ($statusCode == null) {
            $statusCode = new StatusCode();
        }
        $messages = $statusCode->getMessages();
        return isset($messages[$key]) ? $messages[$key] : '';
    }
}
