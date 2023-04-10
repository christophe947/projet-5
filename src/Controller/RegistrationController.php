<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mime\Address;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
//use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    //private RouterInterface $router;

    public function __construct(EmailVerifier $emailVerifier, private Security $security/*, RouterInterface $router*/)
    {
        $this->emailVerifier = $emailVerifier;
        //$this->router = $router;
    }
    
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, Authenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //$user->setRoles(["ROLE_USER"]);
            $role = $user->getRoles();
            $user->setRoles($role);
            $user->setStatus('1');

            $entityManager->persist($user);
            $entityManager->flush();
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('christophe.test.dev@gmail.com', 'Christophe Projet Symfony 6'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        
            $this->addFlash('info', "Votre enregistrement est bien pris en compte, reste a confirmer votre email");//code perso
  
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $auth = $this->security->getUser();
        if ($auth) {    //nessecite detre connecter pour entamer verif email
            try {
                $this->emailVerifier->handleEmailConfirmation($request, $this->getUser()); //tente la confirmationo
            
            } catch (VerifyEmailExceptionInterface $exception) { // lien plus valide ERROR
                
                $this->addFlash('info', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
                return $this->redirectToRoute('app_newlink_email'); //suggere nouveau lien de confirmation
            }
            //sinon bon lien redirection sur profil
            $this->addFlash('info', 'Bravo compte verifié');
            $auth = $this->security->getUser();
            $id = $auth->getId();
            return  $this->redirectToRoute('user_profil', ['id' => $id]);
        } else {
            $this->addFlash('info', "Pour verifier votre email vous devez etre connecté! Connectez vous :");
            return $this->redirectToRoute('app_login');
        }    
    }

    #[Route('/newlink', name: 'app_newlink_email')]  //template permetant le choix du renvois
    public function displayNewlink(): Response {
        
        $auth = $this->security->getUser();
        return $this->render('registration/newlink_email.html.twig',[
            'auth' => $auth
        ]);
    }

    #[Route('/newlink/send/{id}', name: 'send_newlink')]    //action de renvoiyer lemail et retourner au profil
    public function sendNewlink(): Response {
        $user = $this->security->getUser();
        
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('christophe.test.dev@gmail.com', 'Christophe Projet Symfony 6'))
                    ->to($user->getUserIdentifier())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $this->addFlash('info', "Un nouveau lien a été envoyer");
            $auth = $this->security->getUser();
            $id = $auth->getId();      
            
            return  $this->redirectToRoute('user_profil', ['id' => $id]);    
    }


    

}