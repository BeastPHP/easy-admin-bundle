<?php
/*
 * This file is part of the EasyAdmin package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beast\EasyAdminBundle\Service;

use Beast\EasyAdminBundle\Helper\Core\ServiceTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseService
 *
 * @package Beast\EasyAdminBundle\Service
 */
abstract class BaseService
{
    use ServiceTrait;

    /**
     * BaseService constructor.
     *
     * @param ContainerInterface $container
     * @param null $entityClass
     */
    public function __construct(
        ContainerInterface $container,
        $entityClass = null
    ) {
        $this->managerRegistry = $container->get('doctrine');
        $this->request = $container->get('request_stack')->getCurrentRequest();
        $this->paginator = $container->get('knp_paginator');

        if (!is_null($entityClass)) {
            $this->em = $this->managerRegistry->getManagerForClass($entityClass);
            $this->repository = $this->managerRegistry->getRepository($entityClass);
            $this->entityClass = $entityClass;
        }
    }
}
