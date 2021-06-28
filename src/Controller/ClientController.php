<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/", name="client_dashboard")
     */
    public function clientDashboard(): Response
    {
        return $this->render('client/client-dashboard.html.twig', [
            'controller_name' => 'ClientController',
        ]);
    }
}
