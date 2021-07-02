<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Variables;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
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
            "user" => $user
        ]);
    }

    /**
     * @Route("/category/display/{categoryName}", name="index_category")
     */
    public function indexCategory(Request $request, $categoryName = false): Response
    {
        $user = $this->getUser();
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
            'categories' => $categories,
            "user" => $user
        ]);
    }

    /**
     * @Route("/tag/display/{tagId}",name="index_tag")
     */
    public function indexTag(Request $request, $tagId)
    {
        $user = $this->getUser();
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
            "produits" => $produits,
            "user" => $user
        ]);
    }

    /**
     * @Route("/produit/details/{produitId}",name="fiche_produit")
     */
    public function ficheProduit(Request $request, $produitId, Variables $variables)
    {
        //* Test du service Variables
        $texte = "Ca marche également si  j'ajoute un paramètre";
        $test = $variables->test($texte);

        //* Récupération de l'utilisateur
        $user = $this->getUser();

        //* Récupération de l'EntityManager
        $entityManager = $this->getDoctrine()->getManager();

        //* Récupération des données avec le Service Variables
        $tags = $variables->getVariables($entityManager)['tags'];
        $categories = $variables->getVariables($entityManager)['categories'];
        $produit = $variables->getVariables($entityManager, $produitId)['produit'];

        //* On vérifie si le produit existe
        if (!$produit) {
            $this->redirect($this->generateUrl('index'));
        }

        //*Création du formulaire d'achat
        $buyForm = $this->createFormBuilder()
            ->add('quantity', IntegerType::class, [
                "label" => "Quantité",
                "attr" => [
                    "min" => 1,
                    "value" => 1
                ]
            ])
            ->add('valider', SubmitType::class, [
                "label" => "Valider",
                "attr" => [
                    "class" => "w3 w3-black"
                ]
            ])
            ->getForm();

        //*Transmission de la requête client au formulaire. Si elle est valide, création/update de la commande
        $buyForm->handleRequest($request);
        if ($request->isMethod('post') && $buyForm->isValid()) {
            $data = $buyForm->getData(); //* getData() renvoie un tableau associatif des données du formulaire
            $produitStock = $produit->getStock();
            //Nous vérifions si le stock existe et si l'utilisateur est connecté
            if ($produitStock > 0 && $user) {
                //Si c'est le cas nous procédons à la commande
                $variables->commandProceed($entityManager, $produit, $user, $produitStock, $data);

                return $this->redirect($this->generateUrl('fiche_produit', [
                    'produitId' => $produit->getId()
                ]));
            } else {
                return $this->redirect($this->generateUrl('index'));;
            }
        }

        return $this->render('index/fiche-produit.html.twig', [
            "produit" => $produit,
            "categories" => $categories,
            "tags" => $tags,
            "dataForm" => $buyForm->createView(),
            "user" => $user,
            "test" => $test
        ]);
    }

    // /**
    //  * @Route("/produit/buy/{produitId}",name="buy_produit")
    //  */
    // public function buyProduit(Request $request, $produitId)
    // {
    //     //Si le produit existe et si son stock est supérieur à zero, nous créons une nouvelle Reservation
    //     $produitStock = $produit->getStock();
    //     if ($produitStock > 0) {

    //         $reservation = new Reservation;
    //         $reservation->setProduit($produit);
    //         //Nous vérifions si une Commande est en cours, sinon nous la créons.
    //         $commande = $commandeRepository->findOneByStatut('Panier');
    //         if (!$commande) {
    //             $commande = new Commande('Panier', [$reservation]);
    //             $commande->setAdresse('null');
    //         } else {
    //             //Nous lui transmettons ensuite notre nouvelle Reservation
    //             $commande->addReservation($reservation);
    //         }
    //         //Nous déterminons la Quantity de notre Reservation avant de l'enregistrer dans une variable
    //         $reservationQuantity = $reservation->setQuantity(1)->getQuantity();
    //         //Notre nouveau stock pour Produit correspond à la différence du stock de Produit et de la quantity de Reservation
    //         $produit->setStock($produitStock - $reservationQuantity);
    //         $entityManager->persist($produit);
    //         $entityManager->persist($commande);
    //         $entityManager->persist($reservation);
    //         $entityManager->flush();

    //         $display = ($produitStock == 0) ? "none" : "block";
    //         return $this->render('index/fiche-produit.html.twig', [
    //             "produit" => $produit,
    //             "display" => $display
    //         ]);
    //     } else {
    //         return $this->render('index/fiche-produit.html.twig', [
    //             "display" => "none",
    //             "produit" => $produit
    //         ]);
    //     }
    // }
}
