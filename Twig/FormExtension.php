<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Twig;

class FormExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return array(
            'form_label_for_radio_element' => new \Twig_Function(
                'form_label_for_radio_element',
                null,
                array('node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', 'is_safe' => array('html'))
            ),
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'beast_form_extension';
    }
}
