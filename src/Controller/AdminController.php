<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Entity\Produit;
use App\Entity\Category;
use App\Form\ProduitType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 *@Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function adminDashboard(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $categoryRepository = $entityManager->getRepository(Category::class);
        $tagRepository = $entityManager->getRepository(Tag::class);
        $produits = $produitRepository->findAll();
        $categories = $categoryRepository->findAll();
        $tags = $tagRepository->findAll();
        return $this->render('admin/admin-dashboard.html.twig', [
            "produits" => array_reverse($produits),
            "categories" => array_reverse($categories),
            "tags" => array_reverse($tags),
        ]);
    }

    /**
     * @Route("/delete/product/{produitId}",name="delete_product")
     */
    public function deleteProduct(Request $request, $produitId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        $produit = $produitRepository->find($produitId);
        if (!$produit) {
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        $entityManager->remove($produit);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_dashboard'));
    }



    /**
     * @Route("/create/product",name="create_product")
     */
    public function createProduct(Request $request)
    {
        //Nous récupérons l'Entity Manager afin de préparer l'envoi du Produit à créer
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);

        //Nous créons un Produit vide prêt à l'emploi et nous le lions à notre ProduitType
        $produit = new Produit;
        $produitForm = $this->createForm(ProduitType::class, $produit);

        //Nous récupérons les informations de la requête utilisateur pour notre formulaire
        $produitForm->handleRequest($request);

        //Une fois le Produit validé, nous procédons à sa mise en ligne dans la BDD avant de revenir à l'index
        if ($request->isMethod('post') && $produitForm->isValid()) {
            //Si le nom choisi pour notre Entity est déjà existant, nous ne persistons pas la requête et quittons immédiatement la fonction
            $produitName = $produitRepository->findOneByName($produit->getName());
            if ($produitName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requête
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }

        //Nous envoyons notre produit dans le fichier Twig approprié
        return $this->render('index/dataform.html.twig', [
            'dataForm' => $produitForm->createView(),
            'formName' => 'Création de Produit',
        ]);
    }

    /**
     * @Route("/edit/product/{produitId}",name="edit_product")
     */
    public function updateProduit(Request $request, $produitId = false)
    {
        //Cette fonction a pour but de modifier un produit enregistré dans notre BDD
        //Elle récupère pour ce but le produit dont l'ID est indiqué et l'intègre à un formulaire
        //Nous faisons donc appel à l'Entity Manager et au Repository pertinent
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        //Nous recherchons le Produit dont l'ID est indiqué. Le cas échéant, nous revenons vers l'index
        $produit = $produitRepository->find($produitId);
        if (!$produit) {
            return $this->redirect($this->generateUrl('index'));
        }
        //Une fois le produit récupéré, nous créons un nouveau formulaire ProduitType auquel il sera lié
        $produitForm = $this->createForm(ProduitType::class, $produit);
        //Nous transmettons les valeurs de $request à notre produit
        //Si le formulaire est rempli et validé, nous le transmettons à notre base de données
        $produitForm->handleRequest($request);
        if ($request->isMethod('post') && $produitForm->isValid()) {
            //Si le nom choisi pour notre Entity est déjà existant, nous ne persistons pas la requête et quittons immédiatement la fonction
            $produitName = $produitRepository->findByName($produit->getName());
            if (count($produitName) > 1) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requête
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }
        //Nous requérons un render du template dataform.html.twig
        return $this->render('index/dataform.html.twig', [
            'formName' => "Modification de Produit",
            'dataForm' => $produitForm->createView(),
        ]);
    }

    /**
     * @Route("/create/category",name="create_category")
     */
    public function createCategory(Request $request)
    {
        //Nous récupérons l'Entity Manager afin de préparer l'envoi du Category à créer
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);

        //Nous créons une Category vide prêt à l'emploi et nous le lions à notre CategoryType
        $category = new Category;
        $categoryForm = $this->createForm(CategoryType::class, $category);

        //Nous récupérons les informations de la requête utilisateur pour notre formulaire
        $categoryForm->handleRequest($request);

        //Une fois le Category validé, nous procédons à sa mise en ligne dans la BDD avant de revenir à l'index
        if ($request->isMethod('post') && $categoryForm->isValid()) {
            //Si le nom choisi pour notre Entity est déjà existant, nous ne persistons pas la requête et quittons immédiatement la fonction
            $categoryName = $categoryRepository->findOneByName($category->getName());
            if ($categoryName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requête
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }

        //Nous envoyons notre category dans le fichier Twig approprié
        return $this->render('index/dataform.html.twig', [
            'dataForm' => $categoryForm->createView(),
            'formName' => 'Création de Category',
        ]);
    }

    /**
     * @Route("/edit/category/{categoryId}",name="edit_category")
     */
    public function editCategory(Request $request, $categoryId)
    {
        //Cette fonction a pour but de modifier une category enregistré dans notre BDD
        //Elle récupère pour ce but le category dont l'ID est indiqué et l'intègre à une formulaire
        //Nous faisons donc appel à l'Entity Manager et au Repository pertinent
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        //Nous recherchons le Category dont l'ID est indiqué. Le cas échéant, nous revenons vers l'index
        $category = $categoryRepository->find($categoryId);
        if (!$category) {
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Une fois le category récupéré, nous créons une nouveau formulaire CategoryType auquel il sera lié
        $categoryForm = $this->createForm(CategoryType::class, $category);
        //Nous transmettons les valeurs de $request à notre category
        //Si le formulaire est rempli et validé, nous le transmettons à notre base de données
        $categoryForm->handleRequest($request);
        if ($request->isMethod('post') && $categoryForm->isValid()) {
            //Si le nom choisi pour notre Entity est déjà existant, nous ne persistons pas la requête et quittons immédiatement la fonction
            $categoryName = $categoryRepository->findOneByName($category->getName());
            if ($categoryName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requête
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Nous requérons une render du template dataform.html.twig
        return $this->render('index/dataform.html.twig', [
            'formName' => "Modification de Category",
            'dataForm' => $categoryForm->createView(),
        ]);
    }

    /**
     * @Route("/delete/category/{categoryId}",name="delete_category")
     */
    public function deleteCategory(Request $request, $categoryId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        $category = $categoryRepository->find($categoryId);
        if (!$category) {
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Si nous trouvons une category, nous procédons à sa demande de suppression via l'Entity Ma,ager
        //Nous retirons au préalable tous les Produit associés afin de retirer les contraintes de clef étrangère
        foreach ($category->getProduits() as $produit) {
            $category->removeProduit($produit);
        }
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_dashboard'));
    }

    /**
     * @Route("/create/tag",name="create_tag")
     */
    public function createTag(Request $request)
    {
        //Cette fonction a pour but d'afficher le formulaire de création de Tags
        //Pour communiquer avec notre BDD, nous avons besoin de l'Entity Manager
        $entityManager = $this->getDoctrine()->getManager();
        $tagRepository = $entityManager->getRepository(Tag::class);
        //Nous créons un nouveau Tag et nous le lions au formulaire à créer
        $tag = new Tag;
        $tagForm = $this->createForm(TagType::class, $tag);
        //Nous transmettons la requête au formulaire avant de vérifier sa validité
        //Nous renvoyons ensuite l'utilisateur vers la liste des Tags afin qu'il puisse constater l'ajout
        $tagForm->handleRequest($request);
        if ($request->isMethod('post') && $tagForm->isValid()) {
            //Si le nom choisi pour notre Entity est déjà existant, nous ne persistons pas la requête et quittons immédiatement la fonction
            $tagName = $tagRepository->findOneByName($tag->getName());
            if ($tagName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requête
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Nous transmettons notre nouveau formulaire à dataform.html.twig
        return $this->render('index/dataform.html.twig', [
            'dataForm' => $tagForm->createView(),
            'formName' => 'Création de Tags'
        ]);
    }

    /**
     * @Route("/edit/tag/{tagId}",name="edit_tag")
     */
    public function edittag(Request $request, $tagId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productRepository = $entityManager->getRepository(Tag::class);
        $tag = $productRepository->find($tagId);
        $tagForm = $this->createForm(TagType::class, $tag);
        $tagForm->handleRequest($request);
        if ($request->isMethod('post') && $tagForm->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        return $this->render('admin/dataform.html.twig', [
            "dataForm" => $tagForm->createView(),
            "formName" => "Modification d'une categorie"
        ]);
    }

    /**
     * @Route("/delete/tag/{tagId}",name="delete_tag")
     */
    public function deletetag(Request $request, $tagId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tagRepository = $entityManager->getRepository(Tag::class);
        $tag = $tagRepository->find($tagId);
        if (!$tag) {
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        $entityManager->remove($tag);
        $entityManager->flush();
        return $this->redirect($this->generateUrl('admin_dashboard'));
    }
}
