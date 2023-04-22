<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;

class PageNotFoundController extends AbstractController
{
    public function __construct(private RouterInterface $router, private Security $security) {}
    
    #[Route('/not-found', name: 'app_not_found')]
    public function pageNotFoundAction()
    {  
        $auth = $this->security->getUser();
        if($auth) {
           /** @var User $auth */ 
            $id = $auth->getId();
            return new RedirectResponse(
                $this->router->generate('user_profil', ['id' => $id])
            );
        }
        return $this->redirectToRoute('app_login');
    }

}