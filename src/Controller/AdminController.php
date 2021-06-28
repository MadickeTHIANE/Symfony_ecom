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
        $error = "";
        $entityManager = $this->getDoctrine()->getManager();
        $produitRepository = $entityManager->getRepository(Produit::class);

        $product = new Produit;
        $productForm = $this->createForm(ProduitType::class, $product);
        $productForm->handleRequest($request);
        $produitBDD = $produitRepository->findByName($product->getName());
        if ($request->isMethod('post') && $productForm->isValid()) {
            if ($produitBDD) {
                $error = "Ce produit est déjà existant";
            } else {
                $entityManager->persist($product);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('admin_dashboard'));
            }
        }

        return $this->render('admin/dataform.html.twig', [
            'dataForm' => $productForm->createView(),
            'erreur' => $error,
            'formName' => 'Création d\'un produit'

        ]);
    }

    /**
     * @Route("/edit/product/{produitId}",name="edit_product")
     */
    public function editProduct(Request $request, $produitId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productRepository = $entityManager->getRepository(Produit::class);
        $produit = $productRepository->find($produitId);
        $produitForm = $this->createForm(ProduitType::class, $produit);
        $produitForm->handleRequest($request);
        if ($request->isMethod('post') && $produitForm->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        return $this->render('admin/dataform.html.twig', [
            "dataForm" => $produitForm->createView(),
            "formName" => "Modification d'un produit"
        ]);
    }

    /**
     * @Route("/create/category",name="create_category")
     */
    public function createCategory(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = new Category;
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($request->isMethod('post') && $categoryForm->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        return $this->render('admin/dataform.html.twig', [
            "formName" => "Création d'une catégorie",
            "dataForm" => $categoryForm->createView(),
        ]);
    }

    /**
     * @Route("/edit/category/{categoryId}",name="edit_category")
     */
    public function editCategory(Request $request, $categoryId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productRepository = $entityManager->getRepository(category::class);
        $category = $productRepository->find($categoryId);
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($request->isMethod('post') && $categoryForm->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        return $this->render('admin/dataform.html.twig', [
            "dataForm" => $categoryForm->createView(),
            "formName" => "Modification d'une categorie"
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
        $entityManager = $this->getDoctrine()->getManager();
        $tag = new Tag;
        $tagForm = $this->createForm(TagType::class, $tag);
        $tagForm->handleRequest($request);
        if ($request->isMethod('post') && $tagForm->isValid()) {
            foreach ($tag->getProduits() as $produit) {
                $entityManager->persist($produit);
            }
            $entityManager->persist($tag);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('admin_dashboard'));
        }
        return $this->render('admin/dataform.html.twig', [
            "formName" => "Création d'un tag",
            "dataForm" => $tagForm->createView(),
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
