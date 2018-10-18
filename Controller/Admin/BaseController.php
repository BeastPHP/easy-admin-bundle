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

namespace Beast\EasyAdminBundle\Controller\Admin;

use Beast\EasyAdminBundle\Helper\CoreHelper;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Redis;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 *
 * @package App\Controller\Admin
 */
class BaseController extends Controller
{
    /**
     * @param $entityName
     *
     * @return ObjectRepository
     */
    public function getRepository($entityName): ObjectRepository
    {
        return $this->getDoctrine()->getRepository($entityName);
    }

    /**
     * @param null $name
     *
     * @return ObjectManager
     */
    public function getObjectManager($name = null): ObjectManager
    {
        return $this->getDoctrine()->getManager($name);
    }

    /**
     * @param string $entityManagerName
     *
     * @return EntityManager
     */
    public function getEntityManager(string $entityManagerName = 'default'): EntityManager
    {
        $result = array();
        $entityManagerName = sprintf('doctrine.orm.%s_entity_manager', $entityManagerName);
        if ($this->container->has($entityManagerName)) {
            $result = $this->container->get($entityManagerName);
        } else {
            $result = $this->container->get('doctrine.orm.entity_manager');
        }

        return $result;
    }

    /**
     * @param string $connection
     *
     * @return null|Redis
     */
    public function getRedisConnection(string $connection = 'default'): ?Redis
    {
        return CoreHelper::getRedisConnection($connection);
    }

    /**
     * @param string $connection
     *
     * @return null|RedisCache
     */
    public function getSimpleRedisCache(string $connection = 'default'): ?RedisCache
    {
        return CoreHelper::getSimpleRedisCache($connection);
    }

    public function response(array $data, string $view = null, bool $redirect = false, string $url = ''): Response
    {

    }
}
