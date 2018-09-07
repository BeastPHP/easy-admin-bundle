<?php
/*
 * This file is part of the Beast package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Beast\EasyAdminBundle\Controller\Api;

use Beast\EasyAdminBundle\Helper\Rest\RestBundleHelper;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;

class BaseController extends FOSRestController
{
    /**
     * Creates a view.
     *
     * Convenience method to allow for a fluent interface.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return View
     */
    protected function view($data = null, $statusCode = null, array $headers = []): View
    {
        $responseData['status'] = RestBundleHelper::RESPONSE_STATUS_TRUE;
        $responseData['data'] = $data;
        return View::create($responseData, $statusCode, $headers);
    }
}
