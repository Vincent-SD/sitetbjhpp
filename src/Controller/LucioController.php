<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LucioController extends AbstractController
{
    /**
     * @Route("/lucio", name="app_lucio")
     */
    public function index(): Response
    {

        return $this->render('lucio/index.html.twig', [
            'controller_name' => 'LucioController',
        ]);
    }

    /**
     * @Route("/lucio-parse", name="lucio_parse")
     */
    public function parse() : Response{

        return $this->render('');
    }
}
