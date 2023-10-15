<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Picture;
use App\Form\UpdateProfilFormType;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use SebastianBergmann\Type\NullType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

use Symfony\Component\HttpFoundation\Request;


#[Route('profil/{id<\d+>}/info'), IsGranted('PUBLIC_ACCESS')]
class InfoController extends AbstractController
{
    const DIRECTORY_PICTURE = '/uploads/pictures/';
    //const DIRECTORY_PICTURE_PROFIL = '/uploads/profil_pictures/';
    
    public function __construct(private EmailVerifier $emailVerifier, private Security $security, private ManagerRegistry $doctrine) 
    {
        $this->emailVerifier = $emailVerifier;
        $this->serverName = $_SERVER['SERVER_NAME'];
    }
    private $serverName;
    
    public function getUrlPicture() {
        return 'http://'. $this->serverName . self::DIRECTORY_PICTURE;
    }

    /*public function getUrlPictureProfil() {
        return 'https://'. $this->serverName . self::DIRECTORY_PICTURE_PROFIL;
    }*/
    
    #[Route('/', name: 'info')]
    public function info(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $auth = $this->security->getUser();
        return $this->render('user/profil/info/index.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedInfo' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth
        ]);
    }

    #[Route('/update', name: 'info_update')]
    public function infoUpdate(Request $request, User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $userBefore = $user->getEmail();
        $formUpdate = $this->createForm(UpdateProfilFormType::class, $user);

        $formUpdate->handleRequest($request);
        
        if ($formUpdate->isSubmitted()) {
            if($formUpdate->isValid()) {
                //var_dump($userBefore);
                //$user->setStatus('1');
                $manager = $this->doctrine->getManager();
                $manager->flush();
                //var_dump($user->getEmail());die();
                if ($userBefore != $user->getEmail()) {
                    $user->setIsVerified('0');
                    $user->setGoogleId('');
                    $manager->flush();
                    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, (new TemplatedEmail())
                        ->from(new Address('christophe.test.dev@gmail.com', 'Christophe Projet Symfony 6'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                    );
                }
                $this->addFlash('success', "Votre profil à bien eté mise a jour");
                return $this->redirectToRoute('info',['id' => $user->getId()]);
            } 
        }  
           
        $auth = $this->security->getUser(); 
        
        return $this->render('user/profil/info/update_profil.html.twig',[
            'classLeftMenuProfilSelected' => '1',
            'classSelectedInfo' => 'menuProfilSelected',
            'user' => $user,
            'auth' => $auth,
            'form' => $formUpdate->createView()//isset($form) ? $form->createView() : false,
        ]);
    }

    #[Route('/pictures/profil', name: 'pictures_profil')]
    public function picture(User $user = null): Response
    {
        if (!$user) {
            $this->addFlash('error', "Personne inconnu");
            return $this->redirectToRoute('app_not_found');
        }
        $url = $this->getUrlPicture();

        $pictureRepository = $this->doctrine->getRepository(Picture::class);
        $mediaPicture = $pictureRepository->findBy(
            [
                'user' => $user->getId(),
                'profil' => null
            ],
            ['created_at' => 'DESC']
        );
        
        $auth = $this->security->getUser();

        return $this->render('user/profil/info/pictures_profil.html.twig',[
            'user' => $user,
            'auth' => $auth,
            'url' => $url,
            'mediaPicture' => $mediaPicture,
            //'mediaButtonPicture' => 'mediaSelected'//tester utilité
        ]);
    }



       
    

    
}