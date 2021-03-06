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

namespace Beast\EasyAdminBundle\Security\Admin;

use Beast\EasyAdminBundle\Entity\Authorization\Administrator;
use Beast\EasyAdminBundle\Helper\CoreHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class RoleVoter extends Voter
{
    protected $request;
    protected $user;

    private $token;

    public function __construct(RequestStack $requestStack, TokenStorageInterface $token)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->token = $token;
        if ($token && $token instanceof UsernamePasswordToken) {
            $this->user = $token->getUser();
        }
    }

    protected function supports($attribute, $subject)
    {
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!($token instanceof UsernamePasswordToken)) {
            return false;
        }

        if (!($token->getUser() instanceof Administrator)) {
            return false;
        }

        return true;
    }

    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        if ($this->user instanceof Administrator) {
            $redis = CoreHelper::getRedisConnection();
            $hashKey = sprintf('role_%s', $this->user->getRole()->getId());
            $controllers = array();

            $roleController = $redis->hGet('beast_menus_controllers', $hashKey);
            if ($roleController) {
                $controllers = unserialize($roleController);

                $currentController = explode('::', $this->request->attributes->get('_controller'));
                if (!in_array($currentController[0], $controllers)) {
                    return VoterInterface::ACCESS_DENIED;
                }
            }
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
