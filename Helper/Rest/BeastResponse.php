<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Helper\Rest;

class BeastResponse
{
    /**
     * [__invoke description]
     *
     * @param array $data [需要传输的结果]
     *
     * @return array
     */
    public function __invoke(array $data)
    {
        return [
            'status' => RestBundleHelper::RESPONSE_STATUS_TRUE,
            'data'   => $data,
        ];
    }
}
