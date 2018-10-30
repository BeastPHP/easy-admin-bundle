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

use Beast\EasyAdminBundle\Helper\CoreHelper;
use Redis;
use Symfony\Component\Cache\Simple\RedisCache;

trait RedisTrait
{
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
}
