<?php
/*
 * This file is part of the Beast package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beast\EasyAdminBundle\Helper;

use Symfony\Component\HttpKernel\Kernel;

/**
 * Class Util
 *
 * @package Beast\EasyAdminBundle\Helper
 */
class Util
{

    const GT_SERVER_STATUS_KEY = 'gt_server_status';

    const ID_ACTIVE = '1';

    const ID_INACTIVE = '0';

    /**
     * @return array
     */
    public static function getIsActiveChoices(): array
    {
        return array(
            '是' => self::ID_ACTIVE,
            '否' => self::ID_INACTIVE,
        );
    }

    /**
     * @return string
     */
    public static function getRemainDays(): string
    {
        $result = '';
        $endDay = sprintf('%s/%s', '01', Kernel::END_OF_MAINTENANCE);
        $endDay = new \DateTime($endDay);
        $today = new \DateTime();
        $result = $today->diff($endDay)->format('%a');

        if ((int)$result < 0) {
            $result = '0';
        }

        return $result;
    }
}
