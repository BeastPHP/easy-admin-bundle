<?php
/*
 * This file is part of the beast package.
 *
 * (c) Maxwell Guo <beastmaxwell.guo@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Beast\EasyAdminBundle;

use Beast\EasyAdminBundle\DependencyInjection\BeastEasyAdminExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BeastEasyAdminBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BeastEasyAdminExtension();
    }
}
