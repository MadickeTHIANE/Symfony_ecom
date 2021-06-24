<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TagFixtures extends Fixture
{
        public function load(ObjectManager $manager)
        {
                $tagNames = ["Pas cher", "Promotion", "Bois", "Neuf", "Nouveau"];
                foreach ($tagNames as $tagName) {
                        $tag = new Tag;
                        $tag->setName($tagName);
                        $manager->persist($tag);
                }
                $manager->flush();
        }
}
