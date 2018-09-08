<?php

namespace Beast\EasyAdminBundle\Service;

use Beast\EasyAdminBundle\Entity\Core\BaseUser;
use Beast\EasyAdminBundle\Helper\CoreHelper;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthorizationHandle implements AuthenticationSuccessHandlerInterface
{
    use ContainerAwareTrait;

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param TokenInterface $token
     *
     * @return Response never null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        //dump($request);die;



        //return new RedirectResponse($this->container->get('router')->generate('beast_admin_authorization_dashboard'));
    }
}