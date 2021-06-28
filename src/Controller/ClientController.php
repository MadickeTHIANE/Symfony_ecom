<?php

namespace App\Controller;

use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $entityManager = $this->getDoctrine()->getManager();
        $commandeRepository = $entityManager->getRepository(Commande::class);
        $commandes = $commandeRepository->findAll();
        return $this->render('client/client-dashboard.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/valid/{commandId}",name="confirm_command")
     */
    public function confirmCommand(Request $request, $commandId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commandeRepository = $entityManager->getRepository(Commande::class);
        $commandes = $commandeRepository->findAll();
        $selectedCommande = $commandeRepository->find($commandId);
        if (!$selectedCommande->getStatut() == "Panier") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }
        $selectedCommande->setStatut("Validée");
        return $this->render('client/client-dashboard.html.twig', ['commandes' => $commandes,]);
    }
}
