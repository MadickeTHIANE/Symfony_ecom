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
        $reservationRepository = $entityManager->getRepository(Reservation::class);

        $commandes = $commandeRepository->findAll();
        $reservations = $reservationRepository->findAll();
        $selectedCommande = $commandeRepository->find($commandId);

        if (!$selectedCommande->getStatut() == "Panier") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $selectedCommande->setStatut("Validée");
        return $this->render('client/client-dashboard.html.twig', [
            'commandes' => $commandes,
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/delete/{commandeId}/{reservationId}",name="delete_reservation")
     */
    public function deleteReservation(Request $request, $commandeId, $reservationId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reservationRepository = $entityManager->getRepository(Reservation::class);
        $produitRepository = $entityManager->getRepository(Produit::class);
        $commandeRepository = $entityManager->getRepository(Commande::class);

        $produits = $produitRepository->findAll();
        $reservations = $reservationRepository->findAll();
        $commande = $commandeRepository->find($commandeId);
        $selectedReservation = $reservationRepository->find($reservationId);

        //La Reservation ne peut être supprimé si le produit n'existe pas ou si la commande est validée
        //! erreur
        // $produit = $selectedReservation->getProduit();
        // $statut = $produit->getStatut();

        if (!$selectedReservation /*|| $statut == "Validée"*/) {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }


        $entityManager->remove($selectedReservation);
        $entityManager->flush();

        //La commande est supprimée si la dernière réservation a été supprimée, soit si la table Reservation est nulle
        if (count($reservations) == 1) { //? pourquoi je dois le mettre à 1 ? pas actualisé ?
            $entityManager->remove($commande);
            $entityManager->flush();
            //Effacer le bouton Valider
        }

        return $this->render('index/index.html.twig', [
            "produits" => $produits,
        ]);
    }
}
