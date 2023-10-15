<?php

namespace App\Controller;

use App\Entity\Friend;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('search/{id<\d+>}/friends'), IsGranted('PUBLIC_ACCESS')]
class FriendsController extends AbstractController
{
    
    public function __construct(private Security $security, private ManagerRegistry $doctrine) {}
    
    #[Route('/', name: 'friends')]
    public function friends(User $user = null, Friend $friend = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $friendsRepository = $this->doctrine->getRepository(Friend::class);
        $friendsResult = $friendsRepository->findBy([
            'user' => $user->getId(),
            'friend_status' => '1'
        ],
        [
            'created_at' => 'DESC' 
        ]);
        /*$profilRepository = $this->doctrine->getRepository(User::class);
        $friendsResult = $profilRepository->findBy([
            'friends' => $user->getfriends(),
            
        ]);*/
        //var_dump($user->getfriends());die();
        $auth = $this->security->getUser();
        
        return $this->render('user/search/friends/index.html.twig',[
            'classLeftMenuResearchSelected' => '1',
            'friendResult' => $friendsResult,
            'user' => $user,
            'auth' => $auth
        ]);
    }

}