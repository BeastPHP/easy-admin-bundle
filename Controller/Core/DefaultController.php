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

namespace Beast\EasyAdminBundle\Controller\Core;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 *
 * @package Beast\EasyAdminBundle\Controller\Core
 */
class DefaultController extends Controller
{
    /**
     * the 404 page for frontend
     * @Route("/404", name="beast_core_page_for_404")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function page404Action(): Response
    {
        return $this->render('@BeastEasyAdmin/resources/admin/default/pageFor404.html.twig');
    }
}
