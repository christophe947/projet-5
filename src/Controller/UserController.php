<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

class UserController extends AbstractController
{
    
    public function __construct(private Security $security) {}
    
    #[Route('/profil/{id}', name: 'user_profil'), IsGranted("ROLE_USER")]
    public function profil(User $user = null): Response
    {
        $auth = $this->security->getUser();
        return $this->render('user/profil/index.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedProfil' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }
    
    #[Route('/profil/{id}/media', name: 'user_media'), IsGranted("ROLE_USER")]
    public function media(User $user = null): Response
    {
        $auth = $this->security->getUser();
        return $this->render('user/profil/media.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedMedia' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    #[Route('/profil/{id}/event', name: 'user_event'), IsGranted("ROLE_USER")]
    public function event(User $user = null): Response
    {
        $auth = $this->security->getUser();
        return $this->render('user/profil/event.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedEvent' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    #[Route('/profil/{id}/info', name: 'user_info'), IsGranted("ROLE_USER")]
    public function info(User $user = null): Response
    {
        $auth = $this->security->getUser();
        return $this->render('user/profil/info.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedInfo' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }
    
    #[Route('/profil/{id}/contact', name: 'user_contact'), IsGranted("ROLE_USER")]
    public function contact(User $user = null): Response
    {
        $auth = $this->security->getUser();
        return $this->render('user/profil/contact.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedContact' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }
}