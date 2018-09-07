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

namespace Beast\EasyAdminBundle\Controller\Admin\Authorization;

use Beast\EasyAdminBundle\Controller\Admin\BaseController;
use Beast\EasyAdminBundle\Helper\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class AuthorizationController extends BaseController
{
    /**
     * @Route("/login", name="beast_admin_authorization_login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('beast_admin_authorization_dashboard'));
        }

        $geetestConfig = $this->container->getParameter('geetest');
        $authUtils = $this->get('security.authentication_utils');
        return $this->render(
            '@BeastEasyAdmin/resources/admin/authorization/login.html.twig',
            array(
                'last_username' => $authUtils->getLastUsername(),
                'error' => $authUtils->getLastAuthenticationError(),
                'is_open_captcha' => $geetestConfig['is_open'] ?? false,
            )
        );
    }

    /**
     * @Route("/logout", name="beast_admin_authorization_logout")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/check", name="beast_admin_authorization_check")
     */
    public function checkAction()
    {
    }

    /**
     * @Route("/dashboard", name="beast_admin_authorization_dashboard")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction(): Response
    {
        return $this->render(
            '@BeastEasyAdmin/resources/admin/authorization/dashboard.html.twig',
            array(
                'user' => $this->getUser(),
                'phpVersion' => PHP_VERSION,
                'symfonyVersion' => Kernel::VERSION,
                'remainDays' => Util::getRemainDays(),
            )
        );
    }
}
