<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('profil/{id<\d+>}/contact'), IsGranted('PUBLIC_ACCESS')]
class ContactController extends AbstractController
{
    public function __construct(private Security $security, private ManagerRegistry $doctrine) {}
    
    #[Route('/', name: 'contact')]
    public function contact(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/contact/index.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedContact' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

}