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

namespace Beast\EasyAdminBundle\Listener;

use Beast\EasyAdminBundle\Entity\Core\BaseUser;
use Beast\EasyAdminBundle\Helper\Util;
use Beast\EasyGeetest\EasyGeetest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class LoginListener
 *
 * @package Beast\EasyAdminBundle\Listener
 */
class LoginListener implements EventSubscriberInterface
{
    const GEETEST_CHALLENGE = 'geetest_challenge';
    const GEETEST_VALIDATE = 'geetest_validate';
    const GEETEST_SECCODE = 'geetest_seccode';

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var mixed
     */
    private $geetestConfig;

    /**
     * LoginListener constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param EntityManagerInterface $em
     * @param RequestStack $requestStack
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $em,
        RequestStack $requestStack
    ) {
        $this->parameterBag = $parameterBag;
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->geetestConfig = $parameterBag->get('geetest');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'
        ];
    }

    /**
     * Count failed login attempts and save to database on existing username
     *
     * @param AuthenticationEvent $event
     */
    public function onAuthenticationFailure(AuthenticationEvent $event)
    {
    }

    /**
     * @param AuthenticationEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof BaseUser) {
            $user->setLastLoginIp($user->getLoginIp());
            $user->setLoginIp($this->request->getClientIp());

            if ($user->getCurrentLogin()) {
                $user->setLastLogin($user->getCurrentLogin());
            }
            $user->setCurrentLogin(new \DateTime());

            $this->em->persist($user);
            $this->em->flush();
        }
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->validGeetestFlow($event);
    }

    /**
     * 极验验证流程
     *
     * @param InteractiveLoginEvent $event
     */
    protected function validGeetestFlow(InteractiveLoginEvent $event)
    {
        $isOpen = $this->geetestConfig['is_open'] ?? false;
        if ($isOpen) {
            $request = $event->getRequest();

            $geetestChallenge = $request->get(self::GEETEST_CHALLENGE);
            $geetestValidate = $request->get(self::GEETEST_VALIDATE);
            $geetestSeccode = $request->get(self::GEETEST_SECCODE);

            $config = [
                'captcha_id' => $this->geetestConfig['captcha_id'],
                'private_key' => $this->geetestConfig['private_key'],
            ];
            $easyGeetest = new EasyGeetest($config);
            $data = [
                'user_id' => $request->getSession()->getId(), # 网站用户id
                'client_type' => 'web',
                'ip_address' => $request->getClientIp(),
            ];

            if ($request->getSession()->get(Util::GT_SERVER_STATUS_KEY, false)) {
                $result = $easyGeetest->successValidate($geetestChallenge, $geetestValidate, $geetestSeccode, $data);
            } else {
                $result = $easyGeetest->failValidate($geetestChallenge, $geetestValidate);
            }

            if (!$result) {
                throw new CustomUserMessageAuthenticationException('验证失败，请重新登陆');
            }
        }
    }
}
