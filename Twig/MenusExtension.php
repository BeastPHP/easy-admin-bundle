<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Twig;

use Beast\EasyAdminBundle\Helper\CoreHelper;
use Beast\EasyAdminBundle\Service\SiderbarGenerator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class MenusExtension extends \Twig_Extension
{

    public $generator;

    /**
     * MenusExtension constructor.
     *
     * @param SiderbarGenerator $generator
     */
    public function __construct(SiderbarGenerator $generator)
    {
        $this->generator = $generator;
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
