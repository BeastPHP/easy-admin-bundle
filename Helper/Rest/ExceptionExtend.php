<?php

namespace Beast\EasyAdminBundle\Helper\Rest;

use Beast\EasyAdminBundle\Helper\Rest\StatusCode;
use \Exception;

class ExceptionExtend extends Exception
{
    public function __construct($data)
    {
        $data = serialize($data);
        parent::__construct($data, StatusCode::CODE_SYSTEM_ERROR);
    }

    public static function convertData($data)
    {
        $errors = isset($data['msg']) ? unserialize($data['msg']) : array();
        if ($errors) {
            $data['errors'] = $errors;
            unset($data['msg']);
        }
        return $data;
    }

    public static function convertSystemData($data)
    {
        $errors = array(
            'code' => StatusCode::CODE_SYSTEM_ERROR,
            'msg'  => $data['msg']
        );
        if ($errors) {
            $data['errors'] = $errors;
            unset($data['msg']);
        }
        return $data;
    }
}