<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Reservation;

class Variables
{
        public function test($arg)
        {
                $test = "Ceci est un test afin de voir si le service Variables fonctionne. $arg";
                return $test;
        }

        public function getVariables($manager, $statusOrId = null): array
        {
                $tagRepository = $manager->getRepository(Tag::class);
                $categoryRepository = $manager->getRepository(Category::class);
                $produitRepository = $manager->getRepository(Produit::class);
                $commandeRepository = $manager->getRepository(Commande::class);

                $categories = $categoryRepository->findAll();
                $tags = $tagRepository->findAll();
                $produits = $produitRepository->findAll();
                $commandes = $commandeRepository->findAll();

                $produit = null;
                $commande = null;
                if ($statusOrId != null) {
                        $produit = $produitRepository->find($statusOrId);
                        $commande = $commandeRepository->findByStatut($statusOrId);
                }

                $navbarDataArray = [
                        "tags" => $tags,
                        "categories" => $categories,
                        "produits" => $produits,
                        "commandes" => $commandes,
                        "produit" => $produit,
                        "commande" => $commande,
                ];
                return $navbarDataArray;
        }

        public function commandProceed($manager, $produit, $user, $produitStock, $data)
        {
                $reservation = new Reservation;
                $reservation->setProduit($produit);

                //On récupére toutes les commandes actives
                $activeCommandes = $this->getVariables($manager, "Panier")['commande'];

                //On récupère la commande du user connecté
                foreach ($activeCommandes as $activeCommande) {
                        if ($activeCommande->getUser() == $user) {
                                $userCommande = $activeCommande;
                        }
                }

                //Nous vérifions si une Commande est en cours, sinon nous la créons.
                if (!isset($userCommande)) {
                        $userCommande = new Commande('Panier', [$reservation]);
                        $userCommande->setUser($user);
                        $userCommande->setAdresse('null');
                } else {
                        //Nous lui transmettons ensuite notre nouvelle Reservation
                        $userCommande->addReservation($reservation);
                }

                //On récupère la quantité commandée
                $quantity = $data["quantity"];
                //Nous déterminons la Quantity de notre Reservation avant de l'enregistrer dans une variable
                $reservationQuantity = $reservation->setQuantity($quantity)->getQuantity();
                //Notre nouveau stock pour Produit correspond à la différence du stock de Produit et de la quantity de Reservation
                //Si la quantité commandée est supérieure à la quantité disponible, on fait en sorte de mettre le reste du stock dans la Reservation
                $produitRequis = $produitStock - $reservationQuantity;
                if ($produitRequis < 0) {
                        $reservation->setQuantity($produitStock);
                        $produit->setStock(0);
                } else {
                        $produit->setStock($produitStock - $reservationQuantity);
                }

                $manager->persist($produit);
                $manager->persist($userCommande);
                $manager->persist($reservation);
                $manager->flush();
        }

        //* Externaliser le formulaire
}
