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

namespace Beast\EasyAdminBundle\Twig;

use Beast\EasyAdminBundle\Entity\Authorization\Administrator;
use Beast\EasyAdminBundle\Helper\CoreHelper;
use Beast\EasyAdminBundle\Service\SiderbarGenerator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MenusExtension extends \Twig_Extension
{
    public $generator;
    public $token;
    public $user;

    /**
     * MenusExtension constructor.
     *
     * @param SiderbarGenerator $generator
     * @param TokenStorageInterface $token
     */
    public function __construct(SiderbarGenerator $generator, TokenStorageInterface $token)
    {
        $this->generator = $generator;
        $this->token = $token->getToken();
        if ($this->token instanceof UsernamePasswordToken) {
            $this->user = $this->token->getUser();
        }
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            'getMenus' => new \Twig_Function('getMenus', array($this, 'getMenus')),
            'generatePath' => new \Twig_Function('generatePath', array($this, 'generatePath')),
        );
    }

    /**
     * @return array
     */
    public function getMenus(): array
    {
        return $this->generator->generate();
    }

    /**
     * @param $route
     *
     * @return string
     */
    public function generatePath($route): string
    {
        $container = CoreHelper::getCoreKernel()->getContainer();
        try {
            $result = $container->get('router')->generate($route);
        } catch (RouteNotFoundException $e) {
            $result = '#';
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'beast_menus_extension';
    }
}
