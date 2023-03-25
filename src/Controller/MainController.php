<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    
    #[Route('/acceuil', name: 'main.accueil')]
    public function index()//: Response
    { 
        return $this->render('main/index.html.twig', []);
    }

    #[Route(path: '/login', name: 'login')]     //emplacement provisoire pour permetre la vue
    public function login()//: Response
    {
        return $this->render('main/login.html.twig');
    }

    #[Route('/register', name: 'register')]     //emplacement provisoire pour permetre la vue
    public function register()//: Response
    {
        return $this->render('main/register.html.twig', []);
    }

}