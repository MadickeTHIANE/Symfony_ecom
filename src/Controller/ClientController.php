<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/dashboard", name="client_dashboard")
     */
    public function clientDashboard(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        //Récupération de l'Utilisateur
        $user = $this->getUser();

        //Récupération des commandes de l'utilisateur
        $commandesUser = $user->getCommandes();

        //Nous récupérons la liste des Category et des tags pour notre navbar
        $categoryRepository = $entityManager->getRepository(Category::class);
        $tagRepository = $entityManager->getRepository(Tag::class);
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();

        //Nous récupérons dans le cadre de deux recherches, la liste des Commande validées et la commande en mode Panier du user
        $commandes = [];
        $activeCommande = null;
        if ($commandesUser) {
            foreach ($commandesUser as $commande) {
                if ($commande->getStatut() == "Validée") {
                    $commandes[] = $commande;
                } else {
                    $activeCommande = $commande;
                }
            }
        }

        return $this->render('client/client-dashboard.html.twig', [
            'categories' => $categories,
            'tags' => $tags,
            'user' => $user,
            'activeCommande' => $activeCommande,
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/commande/validate/{commandeId}",name="commande_validate")
     */
    public function confirmCommand(Request $request, $commandeId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commandeRepository = $entityManager->getRepository(Commande::class);

        $activeCommande = $commandeRepository->findOneByStatut("Panier");
        $commandes = $commandeRepository->findByStatut("Validée");

        //Nous recherchons la Commande dont l'ID correspond à la valeur transmise via l'url
        //Le cas échéant, nous retournons à notre tableau de bord
        //Si le statut de notre commande n'est pas "Panier", nous retournons également au tableau de bord
        //Nous plaçons la condition d'existence de la Commande en premier dans notre structure de contrôle afin d'éviter de faire appel à une méthode getStatus sur une variable null
        $commande = $commandeRepository->find($commandeId);
        if (!$commande || !$commande->getStatut() == "Panier") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $commande->setStatut("Validée");
        $entityManager->persist($commande);
        $entityManager->flush();


        return $this->redirect($this->generateUrl('client_dashboard'));
    }

    /**
     * @Route("/cancel/{commandeId}",name="commande_delete")
     */
    public function deleteCommand(Request $request, $commandeId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $commandeRepository = $entityManager->getRepository(Commande::class);

        $commande = $commandeRepository->find($commandeId);

        if (!$commande || (!$commande->getStatut() == "Panier")) {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        $reservations = $commande->getReservations();
        foreach ($reservations as $reservation) {
            //Avant de procéder à la suppression, nous restituon à la référence Produit la quantity réservée
            $produit = $reservation->getProduit();
            $produit->setStock($produit->getStock() + $reservation->getQuantity());
            $entityManager->persist($produit);
            $entityManager->remove($reservation);
        }

        $entityManager->remove($commande);
        $entityManager->flush();


        return $this->redirect($this->generateUrl('client_dashboard'));
    }

    /**
     * @Route("/reservation/delete/{reservationId}",name="reservation_delete")
     */
    public function deleteReservation(Request $request, $reservationId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reservationRepository = $entityManager->getRepository(Reservation::class);

        $reservation = $reservationRepository->find($reservationId);
        $commande = $reservation->getCommande();
        $statut = $commande->getStatut();

        if (!$reservation || $statut != "Panier") {
            return $this->redirect($this->generateUrl('client_dashboard'));
        }

        //Si notre Reservation est existante et valide, nous procédons à sa suppression
        //Avant de procéder à la suppression, nous restituon à la référence Produit la quantity réservée
        $produit = $reservation->getProduit();
        $produit->setStock($produit->getStock() + $reservation->getQuantity());
        $entityManager->persist($produit);

        $entityManager->remove($reservation);
        $entityManager->flush();

        //Nous vérifions à présent si la Commande possédant la Reservation est à présent vide. Si oui, nous procédons également à sa suppression
        $reservations = $commande->getReservations();
        if (count($reservations) == 0) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('client_dashboard'));
    }
}
