<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Twig;

use Beast\EasyAdminBundle\Helper\CoreHelper;

class ControllerActionExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            'getControllerName' => new \Twig_Function('getControllerName', array($this, 'getControllerName')),
            'getActionName' => new \Twig_Function('getActionName', array($this, 'getActionName')),
            'getRouteName' => new \Twig_Function('getRouteName', array($this, 'getRouteName')),
        );
    }

    /**
     * @param $requestAttributes
     *
     * @return string
     */
    public function getControllerName($requestAttributes): string
    {
        return CoreHelper::getControllerName($requestAttributes);
    }

    /**
     * @param $requestAttributes
     *
     * @return string
     */
    public function getRouteName($requestAttributes): string
    {
        return $requestAttributes->get('_route');
    }

    /**
     * @param $requestAttributes
     *
     * @return string
     */
    public function getActionName($requestAttributes): string
    {
        $pattern = "#::([a-zA-Z]*)Action#";
        $matches = array();
        preg_match($pattern, $requestAttributes->get('_controller'), $matches);

        return $matches[1];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'beast_controller_action_extension';
    }
}
