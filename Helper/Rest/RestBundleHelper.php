<?php

namespace Beast\EasyAdminBundle\Helper\Rest;

use Beast\EasyAdminBundle\Entity\Member\User;
use Beast\EasyAdminBundle\Helper\Log;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * description content
 *
 * @abstract
 * @access    public
 * @author    windy
 */
class RestBundleHelper
{

    const API_MODULE_TYPE = "api";
    const SERVICE_MODULE_TYPE = "game_service";

    const RESPONSE_STATUS_TRUE = 'T';
    const RESPONSE_STATUS_FALSE = 'F';

    /**
     * need to process all the api data if this come from api
     *
     * @param Request $request
     * @param string $requestType the api router name
     *
     * @return Request
     * @throws \Exception
     */
    public static function processRequest(Request $request, $requestType)
    {
        $content = $requestType . " | " . $request->getRequestUri();
        if ($request->getMethod() == "POST") {
            $content .= var_export($request->request->all(), true);
        }
        Log::writeLog($content, RestBundleHelper::API_MODULE_TYPE, "request");
        return $request;
    }

    /**
     * get the form errors for the rest api
     *
     * @param Form $form
     *
     * @return array
     *
     * @author john.mao <johh.mao@expacta.com.cn>
     */
    public static function getFormErrorForRest(Form $form)
    {
        $errorData = array();

        foreach ($form->getErrors() as $key => $error) {
            $messages = explode('|', $error->getMessage());
            if (count($messages) == 2) {
                $errorData[] = array(
                    'code'    => $messages['0'],
                    'message' => $messages['1'],
                );
            } else {
                $errorData[] = array(
                    'code'    => $messages['0'],
                    'message' => $messages['0'],
                );
            }
        }

        foreach ($form->getIterator() as $key => $child) {
            foreach ($child->getErrors() as $formError) {
                $messages = explode('|', $formError->getMessageTemplate());
                if (count($messages) == 2) {
                    $errorData[] = array(
                        'code'    => $messages['0'],
                        'message' => $messages['1'],
                    );
                } else {
                    $errorData[] = array(
                        'code'    => $messages['0'],
                        'message' => $messages['0'],
                    );
                }
            }
            if ($child->count()) {
                foreach ($child as $grandchild) {
                    foreach ($grandchild->getErrors() as $formError) {
                        $messages = explode('|', $formError->getMessageTemplate());
                        if (count($messages) == 2) {
                            $errorData[] = array(
                                'code'    => $messages['0'],
                                'message' => $messages['1'],
                            );
                        } else {
                            $errorData[] = array(
                                'code'    => $messages['0'],
                                'message' => $messages['0'],
                            );
                        }
                    }
                }
            }
        }

        return $errorData;
    }


    /**
     * validateUserIsCorrect
     *
     * @param $container
     *
     * @return null|User
     * @throws ExceptionExtend
     */
    public static function validateUserIsCorrect($container)
    {
        $token = $container->get('security.token_storage')->getToken();
        $user = null;
        if ($token) {
            $user = $token->getUser();
        }

        if (!($user instanceof User)) {
            $errorCodes[] = array(
                'code'    => StatusCode::CODE_USER_INVALID,
                'message' => StatusCode::MSG_USER_INVALID,
            );
            throw new ExceptionExtend($errorCodes);
        }

        return $user;
    }

    public static function arrayFilterRecursive($input)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = self::arrayFilterRecursive($value);
            }
        }
        return array_filter($input, array(__CLASS__, 'dataFilter'));
    }

    protected static function dataFilter(& $v)
    {
        if (is_null($v)) {
            $v = '';
        }
        return true;
    }
}
