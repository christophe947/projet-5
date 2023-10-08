<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('search/{id<\d+>}/research'), IsGranted('PUBLIC_ACCESS')]
class ResearchController extends AbstractController
{
    
    public function __construct(private Security $security) {}
    
    #[Route('/', name: 'search')]
    public function info(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/search/research/index.html.twig',[
            'classLeftMenuProfiSelected' => '1',
            'classSelectedInfo' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    
}