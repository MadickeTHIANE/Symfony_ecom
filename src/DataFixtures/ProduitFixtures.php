<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProduitFixtures extends Fixture
{
        public function load(ObjectManager $manager)
        {
                $productInfos = [
                        ["name" => "kit dressing", "price" => 300, "stock" => 30, "description" => "tout confort"],
                        ["name" => "sensea remix", "price" => 250, "stock" => 48, "description" => "agréable"],
                        ["name" => "armoire de jardin", "price" => 190, "stock" => 21, "description" => "pratique et durable"],
                        ["name" => "cocooner", "price" => 500, "stock" => 5, "description" => "ergonomique et feng-shui"],
                        ["name" => "sand deliver", "price" => 350, "stock" => 45, "description" => "approuvé par Marie Kondo"],
                        ["name" => "soporificator", "price" => 290, "stock" => 12, "description" => "également disponible en or massif"],
                        ["name" => "desklub", "price" => 250, "stock" => 2, "description" => "résistera à vos ébats les plus tumultueux"],
                        ["name" => "micke", "price" => 200, "stock" => 48, "description" => "respecte les normes écologiques européennes"],
                        ["name" => "lagkapten", "price" => 150, "stock" => 18, "description" => "permet d'optimiser votre espace"],
                        ["name" => "adde", "price" => 150, "stock" => 89, "description" => "excellent rapport qualité prix"],
                        ["name" => "fanbyn", "price" => 90, "stock" => 47, "description" => "assemblage extrêmement facile"],
                        ["name" => "henriksdal", "price" => 70, "stock" => 54, "description" => "Intemporels, nos chers canapés et fauteuils EKTORP disposent de merveilleux coussins épais et confortables. Les housses sont faciles à changer alors achetez-en une ou deux de plus pour pouvoir alterner en fonction de vos envies ou de la saison."],
                        ["name" => "ektorp", "price" => 900, "stock" => 62, "description" => "Après une bonne nuit de sommeil, votre chambre redevient facilement un salon. Le rangement sous l'assise est facile d'accès et large ce qui permet de ranger de nombreux draps, coussins ou livres."],
                        ["name" => "friheten", "price" => 750, "stock" => 2, "description" => "Si vous aimez son look, n'hésitez pas à l'essayer ! La profondeur d'assise, les coussins amovibles et la suspension en tissu élastique en font un canapé particulièrement confortable. Créez votre propre combinaison, installez-vous et détendez-vous !"]
                ];

                foreach ($productInfos as $productInfo) {
                        $product = new Produit;
                        $product->setName($productInfo["name"]);
                        $product->setPrice($productInfo["price"]);
                        $product->setStock($productInfo["stock"]);
                        $product->setDescription($productInfo["description"]);
                        $manager->persist($product);
                }
                $manager->flush();
        }
}
