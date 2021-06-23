<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/band",name="index_band")
     */
    public function indexBand(): Response
    {
        return $this->render('index/band.html.twig');
    }
    /**
     * @Route("/tour",name="index_tour")
     */
    public function indexTour(): Response
    {
        return $this->render('index/tour.html.twig');
    }
    /**
     * @Route("/contact",name="index_contact")
     */
    public function indexContact(): Response
    {
        return $this->render('index/contact.html.twig');
    }
}
