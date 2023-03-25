<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    
    #[Route('/profil', name: 'user.profil')]
    public function profil()//: Response
    {
        return $this->render('user/profil/index.html.twig');
    }
    
    #[Route('/profil/media', name: 'user.media')]
    public function media()//: Response
    {
        return $this->render('user/profil/media.html.twig');
    }

    #[Route('/profil/event', name: 'user.event')]
    public function event()//: Response
    {
        return $this->render('user/profil/event.html.twig');
    }

    #[Route('/profil/info', name: 'user.info')]
    public function info()//: Response
    {
        return $this->render('user/profil/info.html.twig');
    }
    
    #[Route('/profil/contact', name: 'user.contact')]
    public function contact()//: Response
    {
        return $this->render('user/profil/contact.html.twig');
    }
}