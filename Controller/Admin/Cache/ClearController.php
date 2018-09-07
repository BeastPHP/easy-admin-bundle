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

namespace Beast\EasyAdminBundle\Controller\Admin\Cache;

use Beast\EasyAdminBundle\Controller\Admin\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClearController extends BaseController
{
    /**
     * @Route("/clear", name="beast_admin_cache_clear")
     * @param Request $request
     *
     * @return Response
     */
    public function clearAction(Request $request): Response
    {
        if ("POST" == $request->getMethod()) {
            $this->getSimpleRedisCache()->clear();
            $request->getSession()->getFlashBag()->add('success', '清除成功');
        }
        return $this->render('@BeastEasyAdmin/resources/admin/cache/clear.html.twig');
    }
}
