<?php 

namespace App\Security;

use App\Entity\User;
use App\Services\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    //private RouterInterface $router;
    
    public function __construct(private MailerService $mailer,  private Security $security, ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, private RouterInterface $router, private UserPasswordHasherInterface $hasher)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $session = $request->getSession();
        $register = $session->get('register');
        $associate = $session->get('associate');
        $session->remove('associate');
        $session->remove('associatePage');
        
        $client = $this->clientRegistry->getClient('google_main');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $register, $associate, $session) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);
                
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]); //le compte existe en Google donc en normal aussi avec mdp generé envoyé
                $existingUserNotGoogle = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $googleUser->getEmail()]); //le compte existe mais pas en Google on peu lassocier apres
                
                if ($register === '1' && empty($existingUser) && empty($existingUserNotGoogle)) {
                    $existingUser = new User();
                    $email = $googleUser->getEmail();
                    $existingUser->setEmail($email);
                    $passBeta = random_bytes(13);
                    $pass = bin2hex($passBeta);
                    $existingUser->setPassword($this->hasher->hashPassword($existingUser, $pass));
                    $lastname = $googleUser->getFirstName();
                    $existingUser->setName($lastname);
                    $firstname = $googleUser->getLastName();
                    $existingUser->setFirstname($firstname);
                    $existingUser->setRoles(["ROLE_USER"]);
                    $existingUser->setStatus('1');
                    $existingUser->setIsVerified(true);
                    $existingUser->setGoogleId($googleUser->getId());
                    $this->entityManager->persist($existingUser);
                    $this->entityManager->flush();

                    /** @var Session $session */
                    $session->getFlashBag()->set('success', 'Votre inscription a bien été prise en compte');
                    $session->remove('register');//supprime en cas de reussite
                    return $existingUser;
                } 
                
                if ($register === '1' && !empty($existingUser)) {
                    /** @var Session $session */
                    $session->getFlashBag()->set('error', 'Ce compte existe deja');
                    return false;
                }
                
                if($associate === '1' ) {
                    $existingUser = $existingUserNotGoogle;
                    $existingUser->setGoogleId($googleUser->getId());  //return $existingUser permet detre connecté
                    $this->entityManager->persist($existingUser);
                    $this->entityManager->flush();
                    /** @var Session $session */
                    $session->getFlashBag()->set('success', 'Votre comtpe est associé avec google');
                }
                
                if ($associate != '1' && $existingUserNotGoogle && !$existingUser) {
                    $session->set('associatePage', '1');
                }
                return $existingUser;     //log direct
            })
        );
        
    }
      
    
        
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $id = $user->getId();
        return new RedirectResponse(
            $this->router->generate('user_profil', ['id' => $id])
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $session = $request->getSession();
        if($session->get('associatePage') === '1') {    //echec mais reconnais un compte classique avec le meme Email 
            $session->remove('associatePage');
            return new RedirectResponse(
                $this->router->generate('associate_google_page')
            );
        }
        if($session->get('register') === '1') {
            $session->remove('register');//garde la session jusqua maintenant si echec flash message : ($register === '1' && !empty($existingUser))
            return new RedirectResponse(
                $this->router->generate('app_login') 
            );
        }
        $session->remove('register');//supprime en cas dechec
        /** @var Session $session */
        $session->getFlashBag()->set('error', "Aucun compte n'est enregistre veuillez vous inscrire");
        return new RedirectResponse(        //echec global rien de lié
            $this->router->generate('app_register')   
        );
    }
}