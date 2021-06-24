<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $produits = $produitRepository->findAll();
        $categories = $categoryRepository->findAll();
        return $this->render('index/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
        ]);
    }
}
