<?php

namespace App\Controller;

use App\Entity\Produit;
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
        $produits = $produitRepository->findAll();
        return $this->render('admin/admin-dashboard.html.twig', [
            "produits" => $produits
        ]);
    }

    /**
     * @Route("/delete/{produitId}",name="delete_product")
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


    // Formulaire : 

    // /**
    //  * @Route("/create",name="create_product")
    //  */
    // createProduct(Request $request,){}

    // /**
    //  * @Route("",name="edit_product")
    //  */
    // editProduct(Request $request,){}
}
