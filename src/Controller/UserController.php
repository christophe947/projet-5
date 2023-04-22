<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('profil/{id<\d+>}'), IsGranted('PUBLIC_ACCESS')]
class UserController extends AbstractController
{
    
    public function __construct(private Security $security) {}
    
    
    #[Route('/', name: 'user_profil')]
    public function profil(User $user = null): Response
    {    
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/index.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedProfil' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }
    
    
    #[Route('/media', name: 'user_media')]
    public function media(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/media.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    
    #[Route('/event', name: 'user_event')]
    public function event(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/event.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedEvent' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    
    #[Route('/info', name: 'user_info')]
    public function info(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/info.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedInfo' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }
    
    
    #[Route('/contact', name: 'user_contact')]
    public function contact(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/contact.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedContact' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }
    
}