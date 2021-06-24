<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/category/{categoryName}", name="index_category")
     */
    public function indexCategory(Request $request, $categoryName): Response
    {
        //Cette fonction a pour but d'afficher la page d'accueil mais uniquement avec les éléments qui correspondent à la catégorie indiquée
        //Elle doit être disponible via les différentes options du menu déroulant Categories
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->findOneByName($categoryName);
        $produits = $category->getProduits();
        return $this->render('index/index.html.twig', [
            "produits" => $produits
        ]);
    }
}
