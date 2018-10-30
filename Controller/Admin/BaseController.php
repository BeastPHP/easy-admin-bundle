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

use Beast\EasyAdminBundle\Helper\Core\RedisTrait;
use Beast\EasyAdminBundle\Helper\Core\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BaseController
 *
 * @package App\Controller\Admin
 */
abstract class BaseController extends Controller
{
    use ControllerTrait;
    use RedisTrait;
}
