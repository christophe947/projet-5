<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('profil/{id<\d+>}/event'), IsGranted('PUBLIC_ACCESS')]
class EventController extends AbstractController
{
    public function __construct(private Security $security, private ManagerRegistry $doctrine) {}
    
    #[Route('/', name: 'event')]
    public function event(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/event/index.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedEvent' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

}