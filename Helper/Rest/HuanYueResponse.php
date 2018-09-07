<?php

namespace Beast\EasyAdminBundle\Helper\Rest;

use Beast\EasyAdminBundle\Helper\Rest\StatusCode;

class HuanYueResponse
{
    /**
     * @return array
     */
    protected static function getDefaultResponse()
    {
        return array(
            'Succeed' => array(
                'status' => 'T',
                'data'   => array(),
            ),
            'Failed'  => array(
                'status' => 'F',
                'errors' => array(
                    array(
                        'code'    => StatusCode::CODE_TRANSCATION_INVALID,
                        'message' => StatusCode::MSG_SYSTEM_ERROR,
                    ),
                ),
            ),
        );
    }
}
