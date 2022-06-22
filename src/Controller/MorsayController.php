<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MorsayController extends AbstractController
{
    /**
     * @Route("/morsay", name="app_morsay")
     */
    public function index(): Response
    {
        return $this->render('morsay/index.html.twig', [
            'controller_name' => 'MorsayController',
        ]);
    }
}
