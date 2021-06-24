<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *@Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function adminDashboard(): Response
    {
        return $this->render('admin/admin-dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
