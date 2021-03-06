<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\UserProduit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\UserProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
//    /**
//     * @Route("/produit", name="produit")
//     */
//    public function index()
//    {
//        return $this->render('produit/index.html.twig', [
//            'controller_name' => 'ProduitController',
//        ]);
//    }

    /**
     * @param Request $request
     * @param ManagerRegistry $managerRegistry
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/admin/edit-produit/{id}", name="produit_edit")
     * @Route("/admin/create-produit",name="produit_create")
     */
    public function create(Produit $produit = null, Request $request, ManagerRegistry $managerRegistry){
        if(! $produit) {
            $produit = new Produit();
        }

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            if($_FILES['image']['name'] != ""){
                $target_dir = "uploads/produits/";
                $file = $_FILES['image']['name'];
                $path = pathinfo($file);
                $filename = md5(uniqid());
                $ext = $path['extension'];
                $temp_name = $_FILES['image']['tmp_name'];
                $path_filename_ext = $target_dir . $filename . "." . $ext;
                move_uploaded_file($temp_name, $path_filename_ext);

                $produit->setImage($target_dir . $filename . "." . $ext);
            }
            $managerRegistry->getManager()->persist($produit);
            $managerRegistry->getManager()->flush();

            return $this->redirectToRoute('marche');

        }

        return $this->render('produit/create.html.twig',[
            'formProduit' => $form->createView(),
            'editMode' => $produit->getId() != null
        ]);
    }

    /**
     * @Route("/achat-produit/{id}/{marcheId}/{quantity}", name="produit_achat")
     * @param ProduitRepository $produitRepository
     * @return Response
     */
    public function buyProduct(ProduitRepository $produitRepository /*$quantity,*/,UserProduitRepository $userProduitRepository,
                               ManagerRegistry $manager,int $id, int $marcheId, int $quantity){
        $produit = $produitRepository->findOneBy(['id' => $id]);
        $obj = new Stdclass();
        $obj->success = true;
        $obj->price = $produit->getPrix();

//        echo "produit :" . $id . "\n marche : " . $marcheId;
//        exit();
        if($this->getUser() == null){
            $this->addFlash("error", "Inscris-toi pour acheter sale arnaqueur");
            return $this->redirectToRoute('marche_show', ['id' => $marcheId]);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $total_price = $produit->getPrix() * $quantity/*$quantity*/;

        if($user->getCouronnes() - $total_price < 0){
            $obj->success = false;
            echo json_encode($obj);
            return new Response();
//            $this->addFlash("error", "Pas assez de cash sale clochard");
//            return $this->redirectToRoute('marche_show', ['id' => $marcheId]);
        }
        $userProduct = $userProduitRepository->findOneBy(['user'=>$user,'produit'=>$produit]);

        if (isset($userProduct) == 0){
            $newProduct = new UserProduit();
            $newProduct->setUser($user);
            $newProduct->setProduit($produit);
            $user->addproduit($newProduct,$quantity);
        }
        else{
            $oldQuantity = $userProduitRepository->getProductsNumber($user,$produit);
            $userProduct->setQuantity($oldQuantity + $quantity);
        }
        $user->setCouronnes($user->getCouronnes() - $total_price);

        $manager->getManager()->persist($user);
        $manager->getManager()->flush();
        $obj->currentCouronnes = $user->getCouronnes();
        //$user->achat($produit->getPrix()/*, $quantity*/);
        echo json_encode($obj);
        return new Response();
//        return $this->redirectToRoute('marche_show', [
//            'id' => $marcheId,
//        ]);
    }

}
