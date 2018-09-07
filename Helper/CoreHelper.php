<?php

namespace Beast\EasyAdminBundle\Helper;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Cache\Simple\RedisCache;

/**
 * description content
 *
 * @abstract
 * @access    public
 * @author    Guz
 */
class CoreHelper extends HelperAbstract
{

    /**
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    public static function getCoreKernel()
    {
        $coreHelper = new CoreHelper();
        return $coreHelper->getKernel();
    }

    /**
     * @param string $entityManagerName
     *
     * @return EntityManager|object
     */
    public static function getDoctrineEntityManager(string $entityManagerName = 'default')
    {
        $kernel = (new CoreHelper())->getKernel();
        $entityManagerName = sprintf('doctrine.orm.%s_entity_manager', $entityManagerName);
        if ($kernel->getContainer()->has($entityManagerName)) {
            return $kernel->getContainer()->get($entityManagerName);
        } else {
            return $kernel->getContainer()->get('doctrine.orm.entity_manager');
        }
    }

    /**
     * @param string $connection
     *
     * @return EntityManager
     */
    public static function getDoctrineConnection(string $connection = 'default'): EntityManager
    {
        $em = self::getDoctrineEntityManager($connection);
        return $em->getConnection();
    }

    /**
     * @return string
     */
    public static function getDataBaseName(): string
    {
        $dataBaseName = 'default';
        return $dataBaseName;
    }

    /**
     * @param $requestAttributes
     *
     * @return string
     */
    public static function getControllerName($requestAttributes): string
    {
        $controllerNames = explode('::', $requestAttributes->get('_controller'));
        $controllers = explode("\\", $controllerNames['0']);

        $controllerString = implode('_', $controllers);
        return $controllerString;
    }

    /**
     * @param string $connection
     *
     * @return bool|\Redis
     */
    public static function getRedisConnection(string $connection = 'default')
    {
        $container = self::getCoreKernel()->getContainer();

        $redisConfig = $container->getParameter('redis');
        if (!isset($redisConfig['connections'][$connection])) {
            return false;
        }

        $host = $redisConfig['connections'][$connection]['host'];
        $port = $redisConfig['connections'][$connection]['port'];
        $database = $redisConfig['connections'][$connection]['database'];
        $password = $redisConfig['connections'][$connection]['password'];

        $redisClient = new \Redis();
        $redisClient->connect($host, $port);
        if ($password) {
            $redisClient->auth($password);
        }

        $redisClient->select($database);

        return $redisClient;
    }


    /**
     * @param string $connection
     *
     * @return RedisCache
     */
    public static function getSimpleRedisCache(string $connection = 'default'): RedisCache
    {
        return new RedisCache(self::getRedisConnection($connection));
    }
}
