<?php 

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{

    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry2)
    {
        //Redirect to google
        return $clientRegistry2->getClient('google_main')->redirect([], []);
    }

    #[Route('/register/google', name: 'register_google')]
    public function registerAction(Request $request, ClientRegistry $clientRegistry)
    {
        $session = $request->getSession();
        $session->set('register', '1');
        //Redirect to google
        return $clientRegistry->getClient('google_main')->redirect([], []);
    }

    #[Route('/associate/google', name: 'associate_google')]
    public function associateAction(Request $request, ClientRegistry $clientRegistry)
    {
        
        $session = $request->getSession();
        //$this->addFlash('success', 'Votre comtpe est asocié avec google');
        $session->set('associate', '1');
        //Redirect to google
        return $clientRegistry->getClient('google_main')->redirect([], []);
    }

    #[Route('/requestAssociate', name: 'associate_google_page')]
    public function index(): Response
    {
        return $this->render('security/associate_Google.html.twig');
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(): RedirectResponse
    {
        // ** if you want to *authenticate* the user, then
        // leave this method blank and create a Guard authenticator
        return $this->redirectToRoute('app_login');
    }
}