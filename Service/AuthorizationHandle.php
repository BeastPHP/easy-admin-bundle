<?php

namespace Beast\EasyAdminBundle\Service;

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
        $em = CoreHelper::getDoctrineEntityManager();

        $user->setLastLoginIp($user->getLoginIp());
        $user->setLoginIp($request->getClientIp());

        if ($user->getCurrentLogin()) {
            $user->setLastLogin($user->getCurrentLogin());
        }
        $user->setCurrentLogin(new \DateTime());

        $em->persist($user);
        $em->flush();

        return new RedirectResponse($this->container->get('router')->generate('beast_admin_authorization_dashboard'));
    }
}