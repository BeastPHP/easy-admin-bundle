<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Twig;

use Beast\EasyAdminBundle\Helper\CoreHelper;

class PaginationExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            'pagination_render' => new \Twig_Function(
                'pagination_render',
                array($this, 'render'),
                array('is_safe' => array('html'))
            ),
        );
    }

    /**
     * @param $pagination
     * @param array $queryParams
     * @param array $viewParams
     * @param null $template
     *
     * @return string
     * @throws \Twig\Error\Error
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($pagination, array $queryParams = array(), array $viewParams = array(), $template = null)
    {
        $container = CoreHelper::getCoreKernel()->getContainer();
        if ($container->has('templating')) {
            $engine = $container->get('templating');
        } elseif ($container->has('twig')) {
            $engine = $container->get('twig');
        } else {
            throw new \LogicException(
                'You can not use the "render" method if the Templating Component or the Twig Bundle are not available.
                 Try running "composer require symfony/twig-bundle".'
            );
        }

        return $engine->render(
            '@BeastEasyAdmin/common/pagination/sliding.html.twig',
            array(
                'pagination' => $pagination,
                'queryParams' => $queryParams,
                'viewParams' => $viewParams
            )
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'beast_pagination_extension';
    }
}
