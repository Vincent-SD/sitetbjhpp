<?php

namespace App\Controller;

use App\Entity\Boutade;
use App\Form\BoutadeType;
use App\Repository\BoutadeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/boutade")
 */
class BoutadeController extends AbstractController
{
    /**
     * @Route("/", name="boutade_index", methods={"GET"})
     */
    public function index(ManagerRegistry $managerRegistry, BoutadeRepository $boutadeRepository): Response
    {
        //todo quand refresh empecher de redonner la meme
        //todo faire un truc pour que le mec puisse voir les boutades de la meme catégorie que celle quil a obtenu
//        $boutades = $boutadeRepository->findAll();
//        $rand_index = rand(0, sizeof($boutades)-1);
//
//        $rand_boutade = $boutades[$rand_index];
        return $this->render('boutade/index.html.twig', [
//            'boutade' => $rand_boutade,
            'nb_boutades' => $this->getNbBoutades($boutadeRepository)
        ]);
    }

    /**
     * @param BoutadeRepository $repository
     * @Route("/random-boutade", name="random_boutade", methods={"GET"})
     */
    public function randBoutade(BoutadeRepository $repository): Response{
        $boutades = $repository->findAll();
        $rand_index = rand(0, sizeof($boutades)-1);
        $rand_boutade = $boutades[$rand_index];
        $obj = new StdClass();
        $obj->title = $rand_boutade->getTitle();
        $obj->content = $rand_boutade->getContent();
        $obj->author = $rand_boutade->getAuthor();
        $obj->categoryName = $rand_boutade->getCategory()->getName();
        echo json_encode($obj);
        return new Response();

    }

    /**
     * @Route("/create", name="boutade_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $boutade = new Boutade();
        $form = $this->createForm(BoutadeType::class, $boutade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($boutade);
            $entityManager->flush();

            return $this->redirectToRoute('boutade_index');
        }

        return $this->render('boutade/create.html.twig', [
            'boutade' => $boutade,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="boutade_show", methods={"GET"})
     */
    public function show(BoutadeRepository $repository, int $id): Response
    {
        $rand_boutade = $repository->find($id);
        return $this->render('boutade/show.html.twig', [
            'boutade' => $rand_boutade,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="boutade_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Boutade $boutade): Response
    {
        $form = $this->createForm(BoutadeType::class, $boutade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('boutade_index');
        }

        return $this->render('boutade/edit.html.twig', [
            'boutade' => $boutade,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="boutade_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Boutade $boutade): Response
    {
        if ($this->isCsrfTokenValid('delete'.$boutade->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($boutade);
            $entityManager->flush();
        }

        return $this->redirectToRoute('boutade_index');
    }

    /**
     * @Route("/random", name="boutade_get",methods={"GET","POST"})
     */
    public function getRandomBoutadeId(BoutadeRepository $repository){

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $random_id = rand(1,$this->getNbBoutades($repository));
        return $random_id;

//        if ($user->getBoutadeDate() == null || time() - $user->getBoutadeDate() > (24*3600) ){
//        }
    }

    /**NB OF BOUTADES XD
     * @param BoutadeRepository $repository
     */
    public function getNbBoutades(BoutadeRepository $repository){
        try{
            return $repository->countBoutades();
        } catch(NoResultException | NonUniqueResultException $e){
            echo("ERROR : " . $e);
        }
        return 0;
    }
}
