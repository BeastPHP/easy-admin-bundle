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

use Beast\EasyAdminBundle\Helper\Geetest\Geetest;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class GeetestListener implements EventSubscriberInterface
{
    const GEETEST_CHALLENGE = 'geetest_challenge';
    const GEETEST_VALIDATE = 'geetest_validate';
    const GEETEST_SECCODE = 'geetest_seccode';

    protected $parameterBag;

    private $geetestConfig;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->geetestConfig = $parameterBag->get('geetest');
    }

    public static function getSubscribedEvents()
    {
        return array(
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'
        );
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

            $geetest = new Geetest($this->geetestConfig['captcha_id'], $this->geetestConfig['private_key']);
            $data = array(
                'user_id' => $request->getSession()->getId(), # 网站用户id
                'client_type' => 'web',
                'ip_address' => $request->getClientIp(),
            );

            if ($request->getSession()->get(Geetest::GT_SERVER_STATUS_KEY, false)) {
                $result = $geetest->successValidate($geetestChallenge, $geetestValidate, $geetestSeccode, $data);
            } else {
                $result = $geetest->failValidate($geetestChallenge, $geetestValidate);
            }

            if (!$result) {
                throw new CustomUserMessageAuthenticationException('验证失败，请重新登陆');
            }
        }
    }
}
