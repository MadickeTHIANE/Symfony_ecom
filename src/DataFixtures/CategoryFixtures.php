<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CategoryFixtures extends Fixture
{
        public function load(ObjectManager $manager)
        {

                $categoryInfos = [
                        ["name" => "Armoire", "description" => "Une armoire est un meuble fermé, généralement en bois, toujours adossé à un mur, que l'on trouve dans une habitation."],
                        ["name" => "Lit", "description" => "Meuble sur lequel on se couche pour dormir, généralement composé d’un cadre de bois ou de métal, qu’on garnit d’un sommier ou d’une paillasse, d’un ou plusieurs matelas, d’un traversin, d’un ou plusieurs oreillers, de draps et de couvertures."],
                        ["name" => "Bureau", "description" => "Meuble à tiroirs et à tablettes où l’on enferme des papiers et sur lequel on écrit."],
                        ["name" => "Chaise", "description" => "Siège avec dossier, sans accoudoirs."],
                        ["name" => "Canapé", "description" => "Sorte de siège long à dossier, où plusieurs personnes peuvent être assises ensemble et qui peut servir aussi de lit de repos."],
                ];

                foreach ($categoryInfos as $categoryInfo) {
                        $category = new Category();
                        $category->setName($categoryInfo['name']);
                        $category->setDescription($categoryInfo['description']);
                        $manager->persist($category);
                }
                $manager->flush();
        }
}
