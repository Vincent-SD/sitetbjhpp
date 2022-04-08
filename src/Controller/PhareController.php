<?php

namespace App\Controller;

use App\Entity\Phare;
use App\Form\PhareType;
use App\Repository\PhareRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/phare")
 */
class PhareController extends AbstractController
{
    /**
     * @Route("/", name="phare_index", methods={"GET"})
     */
    public function index(PhareRepository $phareRepository): Response
    {
        return $this->render('phare/index.html.twig', [
            'phares' => $phareRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="phare_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $phare = new Phare();
        $form = $this->createForm(PhareType::class, $phare);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($phare);
            $entityManager->flush();

            return $this->redirectToRoute('phare_index');
        }

        return $this->render('phare/new.html.twig', [
            'phare' => $phare,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="phare_show", methods={"GET"})
     */
    public function show(Phare $phare): Response
    {
        return $this->render('phare/show.html.twig', [
            'phare' => $phare,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="phare_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Phare $phare): Response
    {
        $form = $this->createForm(PhareType::class, $phare);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('phare_index');
        }

        return $this->render('phare/edit.html.twig', [
            'phare' => $phare,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="phare_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Phare $phare): Response
    {
        if ($this->isCsrfTokenValid('delete'.$phare->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($phare);
            $entityManager->flush();
        }

        return $this->redirectToRoute('phare_index');
    }
}
