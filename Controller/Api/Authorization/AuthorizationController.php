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

namespace Beast\EasyAdminBundle\Controller\Api\Authorization;

use Beast\EasyAdminBundle\Controller\Api\BaseController;
use Beast\EasyAdminBundle\Helper\Rest\RestBundleHelper;
use Beast\EasyAdminBundle\Helper\Util;
use Beast\EasyGeetest\EasyGeetest;
use FOS\RestBundle\Controller\Annotations as Annotations;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Annotations\NamePrefix("beast_api_authorization")
 */
class AuthorizationController extends BaseController
{
    /**
     * 极验第一步.
     *
     * @Route("/geetest/code", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the rewards of an user",
     * )
     *
     * @SWG\Parameter(
     *     name="order",
     *     in="query",
     *     type="string",
     *     description="The field used to order rewards"
     * )
     * @SWG\Tag(name="极验")
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function getGeetestCodeAction(Request $request): Response
    {
        $request = RestBundleHelper::processRequest($request, 'beast_api_authorization_get_geetest_code');

        $config = $this->getParameter('geetest');
        $parameter = array(
            'captcha_id' => $config['captcha_id'],
            'private_key' => $config['private_key'],
        );
        $easyGeetest = new EasyGeetest($parameter);
        $data = array(
            'user_id' => $request->getSession()->getId(), # 网站用户id
            'client_type' => 'web',
            'ip_address' => $request->getClientIp(),
        );

        $status = $easyGeetest->proProcess($data, 1);
        $request->getSession()->set(Util::GT_SERVER_STATUS_KEY, $status);

        return $this->response($easyGeetest->getResponse());
    }
}
