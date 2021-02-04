<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AggroController extends AbstractController
{
    /**
     * @Route("/aggro", name="aggro")
     */
    public function aggro()
    {
        $randomPosition1 = rand(0,200);
        $randomPosition2 = rand(0,200);
        $randomPosition3 = rand(0,200);
        $randomPosition4 = rand(0,200);
        $randomPosition5 = rand(0,200);
        $randomPosition6 = rand(0,200);
        return $this->render('aggro/index.html.twig', [
            'decalmargin1' => $randomPosition1,
            'decalmargin2' => $randomPosition2,
            'decalmargin3' => $randomPosition3,
            'decalmargin4' => $randomPosition4,
            'decalmargin5' => $randomPosition5,
            'decalmargin6' => $randomPosition6,
        ]);
    }

    /**
     * @Route("/success-aggro", name="aggro_success")
     */
    public function success(ManagerRegistry $manager){
        $bonus_couronnes = 100;
        if($this->getUser() != null){
            $user = $this->getUser();

            /** @var User $user */
            $user->setCouronnes($user->getCouronnes() + $bonus_couronnes);
            $this->addFlash("success", "Tu as gagné 100 couronnes, bravo tu vas pouvoir t'acheter......rien");

            $manager->getManager()->persist($user);
            $manager->getManager()->flush();

        }
        return $this->redirectToRoute('tb');
    }

    /**
     * @Route("/fail-aggro", name="aggro_fail")
     */
    public function fail(ManagerRegistry $managerRegistry){

        if ($this->isGranted("ROLE_ADMIN")){
            echo "couillada";
            exit();
        }

        if($this->getUser() != null){
            /** @var User $user */
            $user = $this->getUser();
            $user->setPassword("putito1");
            $this->addFlash("error", "Bravo tu viens de perdre ton compte gg ^^ Fallait être faster");

            $managerRegistry->getManager()->persist($user);
            $managerRegistry->getManager()->flush();
        }

        return $this->redirectToRoute('app_logout');
    }
}
