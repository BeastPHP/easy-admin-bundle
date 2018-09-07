<?php

declare(strict_types=1);

namespace Beast\EasyAdminBundle\Twig;

class QueryExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return array(
            new \Twig_SimpleFilter('queryString', array($this, 'queryStringFilter')),
        );
    }

    /**
     * @param array $array
     *
     * @return string
     */
    public function queryStringFilter(array $array): string
    {
        if (count($array)) {
            return '?' . http_build_query($array);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'beast_query_extension';
    }
}