<?php

namespace App\Controller;

use App\Entity\Marche;
use App\Form\MarcheType;
use App\Repository\MarcheRepository;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MarcheController extends AbstractController
{
    /**
     * @Route("/marche", name="marche")
     */
    public function index(Request $request, ManagerRegistry $manager, MarcheRepository $repository, ProduitRepository $repository2)
    {
        $marches = $repository->findAll();


        return $this->render('marche/marche.html.twig', [
            'controller_name' => 'MarcheController',
            'marches' => $marches,
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $manager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/edit-marche/{id}", name="marche_edit")
     * @Route("/admin/create-marche", name="marche_create")
     */
    public function createMarche(Marche $marche = null, Request $request, ManagerRegistry $manager){
        if(! $marche) {
            $marche = new Marche();
        }

        $form = $this->createForm(MarcheType::class, $marche);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($_FILES['image']['name'] != ""){
                $target_dir = "uploads/marches/";
                $file = $_FILES['image']['name'];
                $path = pathinfo($file);
                $filename = md5(uniqid());
                $ext = $path['extension'];
                $temp_name = $_FILES['image']['tmp_name'];
                $path_filename_ext = $target_dir . $filename . "." . $ext;
                move_uploaded_file($temp_name, $path_filename_ext);
                $marche->setImage($target_dir . $filename . "." . $ext);
            }

            $manager->getManager()->persist($marche);
            $manager->getManager()->flush();
            $this->addFlash('success', 'bg le san t\'a le marché');
            return $this->redirectToRoute('marche_show', ['id' => $marche->getId()
            ]);
        }

        return $this->render('marche/create.html.twig', [
            'formMarche' => $form->createView(),
            'editMode' => $marche->getId() != null
        ]);

    }

    /**
     *
     * @Route("/marche/{id}", name="marche_show")
     */
    public function show(MarcheRepository $repository, ProduitRepository $produitRepository, $id){
        $marche = $repository->find($id);
        $produits = $marche->getProduit();


        return $this->render('marche/show.html.twig', [
            'marche' => $marche,
            'produits' => $produits,


        ]);
    }

}
