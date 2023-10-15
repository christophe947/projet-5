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

#[Route('search/{id<\d+>}/research'), IsGranted('PUBLIC_ACCESS')]
class ResearchController extends AbstractController
{
    
    public function __construct(private Security $security, private ManagerRegistry $doctrine) {}
    
    #[Route('/', name: 'research')]
    public function research(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $profilRepository = $this->doctrine->getRepository(User::class);
        $profilResult = $profilRepository->findAll();
        
        $auth = $this->security->getUser();
        
        return $this->render('user/search/research/index.html.twig',[
            'classLeftMenuResearchSelected' => '1',
            'profilResult' => $profilResult,
            'user' => $user,
            'auth' => $auth
        ]);
    }

    #[Route('/add-friend/{friendAdd}', name: 'add_friend')]
    public function addFriend(User $user, User $friendAdd ): RedirectResponse
    {   
        if($friendAdd) {
            $friend = new Friend();
            
            $manager = $this->doctrine->getManager();
            $friend->setFriendStatus('1');
            
            $friendRepository = $this->doctrine->getRepository(Friend::class);
            $result = $friendRepository->findBy(
                [
                    'friend' => $friendAdd,
                    'friend_status' => '1',
                    'user' => $user->getId()
                ]
            );
            if($result == null) {
                
                $friend->setFriend($friendAdd);
                $friend->setUser($user);
                $manager->persist($friend);
                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash('success', "demande dami envoyé");
            } else {
                $this->addFlash('error', "deja ami");
            }
        }
        return $this->redirectToRoute('research',['id' => $user->getId()]);     
    }

    
}