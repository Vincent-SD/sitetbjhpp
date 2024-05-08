<?php

namespace App\Controller;

use App\Entity\Phare;
use App\Form\PhareType;
use App\Repository\PhareRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
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

            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = $originalFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        "uploads/phares",
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                   echo $e->getMessage();
                   exit();
                }

                $phare->setImage("uploads/phares/" . $newFilename);
            }


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

    /**
     * @Route("/api/getall", name="phare_api_all", methods={"GET"})
     */
    public function getAllJson(PhareRepository $phareRepository): JsonResponse
    {
        // Retrieve all lighthouses using the repository
        $phares = $phareRepository->findAll();

        // Transform the entities into an array
        $pharesArray = [];
        foreach ($phares as $phare) {
            $pharesArray[] = [
                'id' => $phare->getId(),
                'name' => $phare->getName(),
                'description' => $phare->getDescription(),
                'longitude' => $phare->getLongitude(),
                'latitude' => $phare->getLatitude(),
                'image' => $phare->getImage(),
                'period' => $phare->getPeriod()
            ];
        }

        // Return the JSON response
        return new JsonResponse($pharesArray);
    }


}
