<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Reservation;
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
        $tagRepository = $entityManager->getRepository(Tag::class);
        $produits = $produitRepository->findAll();
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        return $this->render('index/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            "tags" => $tags,
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
        $tagRepository = $entityManager->getRepository(Tag::class);
        //Nous récupérons la liste des catégories via le Repository afin d'afficher la liste dans la navbar
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
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
            "tags" => $tags,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/tag/display/{tagId}",name="index_tag")
     */
    public function indexTag(Request $request, $tagId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tagRepository = $entityManager->getRepository(Tag::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $tags = $tagRepository->findAll();
        $categories = $categoryRepository->findAll();
        $selectedTag = $tagRepository->find($tagId);
        if (!$selectedTag) {
            return $this->redirect($this->generateUrl('index'));
        }
        $produits = $selectedTag->getProduits();
        return $this->render('index/index.html.twig', [
            "tags" => $tags,
            "categories" => $categories,
            "produits" => $produits
        ]);
    }

    /**
     * @Route("/produit/details/{produitId}",name="fiche_produit")
     */
    public function ficheProduit(Request $request, $produitId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $selectedProduit = $produitRepository->find($produitId);
        $categories = $categoryRepository->findAll();
        if (!$selectedProduit) {
            $this->redirect($this->generateUrl('index'));
        }
        $produit = $selectedProduit;
        if ($produit->getStock() == 0) {
            return $this->render('index/fiche-produit.html.twig', [
                "display" => "none",
                "produit" => $produit
            ]);
        }
        return $this->render('index/fiche-produit.html.twig', [
            "produit" => $produit,
            "categories" => $categories,
            "display" => "block"
        ]);
    }

    /**
     * @Route("/produit/buy/{produitId}",name="buy_produit")
     */
    public function buyProduit(Request $request, $produitId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $commandeRepository = $entityManager->getRepository(Commande::class);
        $produit = $produitRepository->find($produitId);
        //Si le produit n'est pas trouvé, nous retournons vers la page d'accueil
        if (!$produit) {
            return $this->redirect($this->generateUrl('index'));
        }
        //Si le produit existe et si son stock est supérieur à zero, nous créons une nouvelle Reservation
        $produitStock = $produit->getStock();
        if ($produitStock > 0) {

            $reservation = new Reservation;
            $reservation->setProduit($produit);
            //Nous vérifions si une Commande est en cours, sinon nous la créons.
            $commande = $commandeRepository->findOneByStatut('Panier');
            if (!$commande) {
                $commande = new Commande('Panier', [$reservation]);
                $commande->setAdresse('null');
            } else {
                //Nous lui transmettons ensuite notre nouvelle Reservation
                $commande->addReservation($reservation);
            }
            //Nous déterminons la Quantity de notre Reservation avant de l'enregistrer dans une variable
            $reservationQuantity = $reservation->setQuantity(1)->getQuantity();
            //Notre nouveau stock pour Produit correspond à la différence du stock de Produit et de la quantity de Reservation
            $produit->setStock($produitStock - $reservationQuantity);
            $entityManager->persist($produit);
            $entityManager->persist($commande);
            $entityManager->persist($reservation);
            $entityManager->flush();

            $display = ($produitStock == 0) ? "none" : "block";
            return $this->render('index/fiche-produit.html.twig', [
                "produit" => $produit,
                "display" => $display
            ]);
        } else {
            return $this->render('index/fiche-produit.html.twig', [
                "display" => "none",
                "produit" => $produit
            ]);
        }
    }
}
