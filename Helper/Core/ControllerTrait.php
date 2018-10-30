<?php
/*
 * This file is part of the EasyAdmin package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Beast\EasyAdminBundle\Helper\Core;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Common features needed in controllers.
 *
 * @property ContainerInterface $container
 */
trait ControllerTrait
{
    /**
     * @param $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository($entityName): ObjectRepository
    {
        return $this->container->get('doctrine')->getRepository($entityName);
    }

    /**
     * @param null $name
     *
     * @return ObjectManager
     */
    public function getObjectManager($name = null): ObjectManager
    {
        return $this->container->get('doctrine')->getManager($name);
    }

    /**
     * @param string $entityManagerName
     *
     * @return EntityManager
     */
    public function getEntityManager(string $entityManagerName = 'default'): EntityManager
    {
        $result = [];
        $entityManagerName = sprintf('doctrine.orm.%s_entity_manager', $entityManagerName);
        if ($this->container->has($entityManagerName)) {
            $result = $this->container->get($entityManagerName);
        } else {
            $result = $this->container->get('doctrine.orm.entity_manager');
        }

        return $result;
    }

    public function getRequest()
    {
        global $kernel;

        return $kernel->getContainer()->get('request_stack')->getCurrentRequest();
    }
}
