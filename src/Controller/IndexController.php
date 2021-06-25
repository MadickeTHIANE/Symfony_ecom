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
     * @Route("/category/display/{categoryName}", name="index_category")
     */
    public function indexCategory(Request $request, $categoryName = false): Response
    {
        //Cette fonction a pour but d'afficher la page d'accueil mais uniquement avec les éléments qui correspondent à la catégorie indiquée
        //Elle doit être disponible via les différentes options du menu déroulant Categories
        //Nous récupérons le Repository de l'Entity dont nous avions besoin, laquelle est Category
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        //Nous récupérons la liste des catégories via le Repository afin d'afficher la liste dans la navbar
        $categories = $categoryRepository->findAll();
        //Nous récupérons la Category dont le nom correspond à la valeur de la variable $categoryName
        //La fonction findOneByName nous permet de récupérer UNE entrée dont le nom correspond à la valeur de l'attribut désigné: ici "name"
        $selectedCategory = $categoryRepository->findOneByName($categoryName);
        //Si la catégori recherchée n'existe pas, nous revenons vers l'index
        if (!$selectedCategory) {
            return $this->redirect($this->generateUrl('index'));
        }
        //Nous récupérons la liste des produits liés à notre Category grâce à getProduit()
        //Etant donné que nos deux Entity sont liées, nous récupérons la liste entière des produits liés à Category via son tableau $produits. Il suffit alors de récupérer et de transmettre ce tableau à Twig

        $produits = $selectedCategory->getProduits();
        //Nous transmettons nos variables à Twig
        return $this->render('index/index.html.twig', [
            "produits" => $produits,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/produit/details/{produitId}",name="fiche_produit")
     */
    public function ficheProduit(Request $request, $produitId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $selectedProduit = $produitRepository->find($produitId);
        if (!$selectedProduit) {
            $this->redirect($this->generateUrl('index'));
        }
        $produit = $selectedProduit;
        return $this->render('index/fiche-produit.html.twig', [
            "produit" => $produit
        ]);
    }
}
