<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

class EnergieController extends AbstractController
{
    /**
     * @Route("/energie", name="energie")
     */
    public function index(RequestStack $requestStack)
    {
        if($this->getUser() != null) {
            $rand = rand(0, 10);
            $id = uniqid();
            $session = $requestStack->getCurrentRequest()->getSession();
            $session->set('energie-token', $id);

            if ($rand <= 5) {
                return $this->render('energie/index.html.twig', [
                    'controller_name' => 'EnergieController',
                    'energietoken' =>  $id
                ]);
            }
            return $this->render('energie/igni.html.twig', [
                'controller_name' => 'EnergieController',
                'energietoken' =>  $id
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/success-energie", name="energie_success_fake")
     * @Route("/success-energie/{energie_token}", name="energie_success")
     */
    public function after(ManagerRegistry $manager, $energie_token=null, RequestStack $requestStack){
        if($this->getUser() != null) {
            $bonusCouronnes = 20;

            /** @var User $user */
            $user = $this->getUser();
            $session = $requestStack->getCurrentRequest()->getSession();
            if(isset($energie_token) && $energie_token === $session->get('energie-token')){
                $user->setCouronnes($user->getCouronnes() + $bonusCouronnes);
                $this->addFlash("success", "Tu as gagné " . $bonusCouronnes . " couronnes, bravo tu vas pouvoir t'acheter......rien");
            } else {
                $user->setCouronnes($user->getCouronnes() - $bonusCouronnes);
                $this->addFlash("error", "Tu as gagné -" . $bonusCouronnes*2004561 . " couronnes, bravo pd de tricheur si tu refais ça t'a plus de conmpte alors dégage moi de là");
            }

            $manager->getManager()->persist($user);
            $manager->getManager()->flush();
        }
           return $this->redirectToRoute('tb');

    }
}
