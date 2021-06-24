<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
        public function load(ObjectManager $manager)
        {
                //Cette fonction sera chargée suite à la commande php bin/console doctrine:fixtures:load

                // $product = new Product();
                // $manager->persist($product);

                //Nous allons créer un tableau tagArray de plusieurs données représentant plusieurs instances de l'Entity Tag à envoyer à notre base de données. tagArray est un tableau de tableaux associatifs, chacun contenant les valeurs à remplir pour chaque champ de l'instance d'Entity

                $tagArray = [
                        ["name" => "Neuf"],
                        ["name" => "Bois"],
                        ["name" => "Lit"],
                        ["name" => "Qualité"],
                        ["name" => "Bon marché"],
                ];

                //La boucle foreach parcourt notre tableau produitArray, et à chaque tour, crée une instance de l'Entity Tag, la remplit avec les valeurs du tableau associatif actuellement parcouru, et effectue une demande de persistance. Nous aurons donc autant de demandes de persistences qu'il existe d'entrées dans notre tableau tagArray.
                foreach ($tagArray as $tagData) {
                        $tag = new \App\Entity\Tag;
                        $tag->setName($tagData['name']);
                        $manager->persist($tag);
                }

                $manager->flush();
        }
}
