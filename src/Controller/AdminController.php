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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 *@Route("/admin")
 *@Security("is_granted('ROLE_ADMIN')")
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
        //Nous r??cup??rons l'Entity Manager afin de pr??parer l'envoi du Produit ?? cr??er
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);

        //Nous cr??ons un Produit vide pr??t ?? l'emploi et nous le lions ?? notre ProduitType
        $produit = new Produit;
        $produitForm = $this->createForm(ProduitType::class, $produit);

        //Nous r??cup??rons les informations de la requ??te utilisateur pour notre formulaire
        $produitForm->handleRequest($request);

        //Une fois le Produit valid??, nous proc??dons ?? sa mise en ligne dans la BDD avant de revenir ?? l'index
        if ($request->isMethod('post') && $produitForm->isValid()) {
            //Si le nom choisi pour notre Entity est d??j?? existant, nous ne persistons pas la requ??te et quittons imm??diatement la fonction
            $produitName = $produitRepository->findOneByName($produit->getName());
            if ($produitName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requ??te
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }

        //Nous envoyons notre produit dans le fichier Twig appropri??
        return $this->render('index/dataform.html.twig', [
            'dataForm' => $produitForm->createView(),
            'formName' => 'Cr??ation de Produit',
        ]);
    }

    /**
     * @Route("/edit/product/{produitId}",name="edit_product")
     */
    public function updateProduit(Request $request, $produitId = false)
    {
        //Cette fonction a pour but de modifier un produit enregistr?? dans notre BDD
        //Elle r??cup??re pour ce but le produit dont l'ID est indiqu?? et l'int??gre ?? un formulaire
        //Nous faisons donc appel ?? l'Entity Manager et au Repository pertinent
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);
        //Nous recherchons le Produit dont l'ID est indiqu??. Le cas ??ch??ant, nous revenons vers l'index
        $produit = $produitRepository->find($produitId);
        if (!$produit) {
            return $this->redirect($this->generateUrl('index'));
        }
        //Une fois le produit r??cup??r??, nous cr??ons un nouveau formulaire ProduitType auquel il sera li??
        $produitForm = $this->createForm(ProduitType::class, $produit);
        //Nous transmettons les valeurs de $request ?? notre produit
        //Si le formulaire est rempli et valid??, nous le transmettons ?? notre base de donn??es
        $produitForm->handleRequest($request);
        if ($request->isMethod('post') && $produitForm->isValid()) {
            //Si le nom choisi pour notre Entity est d??j?? existant, nous ne persistons pas la requ??te et quittons imm??diatement la fonction
            $produitName = $produitRepository->findByName($produit->getName());
            if (count($produitName) > 1) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requ??te
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('index'));
        }
        //Nous requ??rons un render du template dataform.html.twig
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
        //Nous r??cup??rons l'Entity Manager afin de pr??parer l'envoi du Category ?? cr??er
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);

        //Nous cr??ons une Category vide pr??t ?? l'emploi et nous le lions ?? notre CategoryType
        $category = new Category;
        $categoryForm = $this->createForm(CategoryType::class, $category);

        //Nous r??cup??rons les informations de la requ??te utilisateur pour notre formulaire
        $categoryForm->handleRequest($request);

        //Une fois le Category valid??, nous proc??dons ?? sa mise en ligne dans la BDD avant de revenir ?? l'index
        if ($request->isMethod('post') && $categoryForm->isValid()) {
            //Si le nom choisi pour notre Entity est d??j?? existant, nous ne persistons pas la requ??te et quittons imm??diatement la fonction
            $categoryName = $categoryRepository->findOneByName($category->getName());
            if ($categoryName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requ??te
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }

        //Nous envoyons notre category dans le fichier Twig appropri??
        return $this->render('index/dataform.html.twig', [
            'dataForm' => $categoryForm->createView(),
            'formName' => 'Cr??ation de Category',
        ]);
    }

    /**
     * @Route("/edit/category/{categoryId}",name="edit_category")
     */
    public function editCategory(Request $request, $categoryId)
    {
        //Cette fonction a pour but de modifier une category enregistr?? dans notre BDD
        //Elle r??cup??re pour ce but le category dont l'ID est indiqu?? et l'int??gre ?? une formulaire
        //Nous faisons donc appel ?? l'Entity Manager et au Repository pertinent
        $entityManager = $this->getDoctrine()->getManager();
        $categoryRepository = $entityManager->getRepository(Category::class);
        //Nous recherchons le Category dont l'ID est indiqu??. Le cas ??ch??ant, nous revenons vers l'index
        $category = $categoryRepository->find($categoryId);
        if (!$category) {
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Une fois le category r??cup??r??, nous cr??ons une nouveau formulaire CategoryType auquel il sera li??
        $categoryForm = $this->createForm(CategoryType::class, $category);
        //Nous transmettons les valeurs de $request ?? notre category
        //Si le formulaire est rempli et valid??, nous le transmettons ?? notre base de donn??es
        $categoryForm->handleRequest($request);
        if ($request->isMethod('post') && $categoryForm->isValid()) {
            //Si le nom choisi pour notre Entity est d??j?? existant, nous ne persistons pas la requ??te et quittons imm??diatement la fonction
            $categoryName = $categoryRepository->findOneByName($category->getName());
            if ($categoryName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requ??te
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Nous requ??rons une render du template dataform.html.twig
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
        //Si nous trouvons une category, nous proc??dons ?? sa demande de suppression via l'Entity Ma,ager
        //Nous retirons au pr??alable tous les Produit associ??s afin de retirer les contraintes de clef ??trang??re
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
        //Cette fonction a pour but d'afficher le formulaire de cr??ation de Tags
        //Pour communiquer avec notre BDD, nous avons besoin de l'Entity Manager
        $entityManager = $this->getDoctrine()->getManager();
        $tagRepository = $entityManager->getRepository(Tag::class);
        //Nous cr??ons un nouveau Tag et nous le lions au formulaire ?? cr??er
        $tag = new Tag;
        $tagForm = $this->createForm(TagType::class, $tag);
        //Nous transmettons la requ??te au formulaire avant de v??rifier sa validit??
        //Nous renvoyons ensuite l'utilisateur vers la liste des Tags afin qu'il puisse constater l'ajout
        $tagForm->handleRequest($request);
        if ($request->isMethod('post') && $tagForm->isValid()) {
            //Si le nom choisi pour notre Entity est d??j?? existant, nous ne persistons pas la requ??te et quittons imm??diatement la fonction
            $tagName = $tagRepository->findOneByName($tag->getName());
            if ($tagName) {
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
            //Persistance et application de la requ??te
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        //Nous transmettons notre nouveau formulaire ?? dataform.html.twig
        return $this->render('index/dataform.html.twig', [
            'dataForm' => $tagForm->createView(),
            'formName' => 'Cr??ation de Tags'
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
