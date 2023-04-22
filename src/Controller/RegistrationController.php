<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mime\Address;
use App\Form\RegistrationFormType;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class RegistrationController extends AbstractController
{
    
    //private EmailVerifier $emailVerifier;

    
    public function __construct(private EmailVerifier $emailVerifier, private Security $security)
    {
        $this->emailVerifier = $emailVerifier;
    }
    
    
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, Authenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // encode the plain password 
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $role = $user->getRoles();
            $user->setRoles($role);
            $user->setStatus('1');

            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('christophe.test.dev@gmail.com', 'Christophe Projet Symfony 6'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $this->addFlash('success', "Votre enregistrement est bien pris en compte, reste a confirmer votre email");
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
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {   
        $idEmail = $request->query->get('id');
        
        if ($userRepository->find($idEmail)) {    //nessecite detre reconnu
            $auth = $userRepository->find($request->query->get('id'));
            try {
                $this->emailVerifier->handleEmailConfirmation($request, $auth); //verifie 
            } catch (VerifyEmailExceptionInterface $exception) { // lien plus valide ERROR perimé/ invalide
                
                $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
                return $this->redirectToRoute('app_newlink_email',['id' => $idEmail]); //suggere nouveau lien de confirmation
            }
            //sinon bon lien redirection sur profil
            $roles[] = 'ROLE_USER';
            $auth->setRoles($roles);
            $this->addFlash('success', 'Bravo compte verifié');
            if ($idEmail && ($this->security->getUser() !== null)) {
                return $this->redirectToRoute('user_profil', ['id' => $idEmail]);
            } else {
                return $this->redirectToRoute('app_login');
            }
        } 
        $this->addFlash('error', "Compte inexistant");
        return $this->redirectToRoute('app_login');
    } 
    
    
    #[Route('/newlink/{id}', name: 'app_newlink_email')]  //template permetant le choix du renvois
    public function displayNewlink(Request $request): Response
    {
        $idCreate = $request->attributes;
        $id = $idCreate->get('id');
        return $this->render('registration/newlink_email.html.twig',[
            'id' => $id
        ]);
    }

    
    #[Route('/newlink/send/{id}', name: 'send_newlink')]    //action de renvoiyer lemail et retourner au profil
    public function sendNewlink(Request $request, UserRepository $userRepository): Response
    {
        $idCreate = $request->attributes;
        $id = $idCreate->get('id');
        $user = $userRepository->find($id);
        
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('christophe.test.dev@gmail.com', 'Christophe Projet Symfony 6'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        $this->addFlash('info', "Un nouveau lien a été envoyer");
        return  $this->redirectToRoute('user_profil', ['id' => $id]);    
    }


}