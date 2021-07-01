<?php

namespace App\Service;

use App\Entity\Tag;
use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Commande;

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

        //* Récupérer le bloc à partir de la ligne 151
        public function commandProceed($manager)
        {
        }

        //* Externaliser le formulaire
}
