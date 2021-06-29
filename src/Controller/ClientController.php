<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\Reservation;
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
        $commandeAttente = $commandeRepository->findOneByStatut("Panier");
        $commandesValid = $commandeRepository->findByStatut("Validée");

        return $this->render('client/client-dashboard.html.twig', [
            'commandeAttente' => $commandeAttente,
            'commandesValid' => $commandesValid
        ]);
    }

    /**
     * @Route("/valid/{commandId}",name="confirm_command")
     */
    public function confirmCommand(Request $request, $commandId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commandeRepository = $entityManager->getRepository(Commande::class);

        $commandeAttente = $commandeRepository->findOneByStatut("Panier");
        $commandesValid = $commandeRepository->findByStatut("Validée");
        $selectedCommande = $commandeRepository->find($commandId);

        if (!$selectedCommande->getStatut() == "Panier") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $selectedCommande->setStatut("Validée");
        $entityManager->persist($selectedCommande);
        $entityManager->flush();


        return $this->render('client/client-dashboard.html.twig', [
            'commandeAttente' => $commandeAttente,
            'commandesValid' => $commandesValid
        ]);
    }

    /**
     * @Route("/cancel/{commandId}",name="cancel_command")
     */
    public function cancelCommand(Request $request, $commandId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commandeRepository = $entityManager->getRepository(Commande::class);

        $commandeAttente = $commandeRepository->findOneByStatut("Panier");
        $commandesValid = $commandeRepository->findByStatut("Validée");
        $selectedCommande = $commandeRepository->find($commandId);

        if (!$selectedCommande->getStatut() == "Panier") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $reservations = $selectedCommande->getReservations();
        foreach ($reservations as $reservation) {
            $entityManager->remove($reservation);
        }

        $entityManager->remove($selectedCommande);
        $entityManager->flush();


        return $this->render('client/client-dashboard.html.twig', [
            'commandeAttente' => $commandeAttente,
            'commandesValid' => $commandesValid
        ]);
    }

    /**
     * @Route("/delete/{reservationId}",name="delete_reservation")
     */
    public function deleteReservation(Request $request, $reservationId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reservationRepository = $entityManager->getRepository(Reservation::class);
        $produitRepository = $entityManager->getRepository(Produit::class);
        $commandeRepository = $entityManager->getRepository(Commande::class);

        $commandeAttente = $commandeRepository->findOneByStatut("Panier");
        $commandesValid = $commandeRepository->findByStatut("Validée");
        $selectedReservation = $reservationRepository->find($reservationId);
        $commande = $selectedReservation->getCommande();
        $statut = $commande->getStatut();

        if (!$selectedReservation || $statut == "Validée") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }


        $entityManager->remove($selectedReservation);
        $entityManager->flush();

        //La commande est supprimée si la dernière réservation a été supprimée, soit si la table Reservation est nulle
        $reservations = $commande->getReservations();
        if (count($reservations) == 0) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->render('client/client-dashboard.html.twig', [
            'commandeAttente' => $commandeAttente,
            'commandesValid' => $commandesValid
        ]);
    }
}
