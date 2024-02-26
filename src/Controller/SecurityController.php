<?php

namespace App\Controller;

use Symfony\Bridge\Twig\NodeVisitor\Scope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public const SCOPES = [
        'google' => [],
        'github' => ['user','user:email','repo'],
        'facebook' => ['public_profile', 'email'],
    ];


    #[Route(path: '/login', name: 'app_login',  methods:['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ( $this->getUser() ) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/oauth/connect/service/{service}', name: 'app_oauth_login',  methods:['GET'])]
    public function connect(Request $request, string $service, ClientRegistry $clientRegistry): RedirectResponse
    {
        if ( !in_array($service, array_keys(self::SCOPES), TRUE) )
        {
            throw $this->createNotFoundException() ;
        }

        // $clientRegistry = $this->get('knpu.oauth2.registry'); 
        return $clientRegistry
            ->getClient($service) // the name use in config/packages/knpu_oauth2_client.yaml 
            ->redirect( self::SCOPES[$service], []) ;  // 'public_profile', 'email' ,  the scopes you want to access
    }

    #[Route('/oauth/check/{service}', name: 'auth_oauth_check',  methods:['GET','POST'])]
    public function connectCheckAction( Request $request, ClientRegistry $clientRegistry): Response
    {
        $service =  $request->attributes->all()['service'];
       
        if ( !in_array($service, array_keys(self::SCOPES), TRUE) )
        {
            throw $this->createNotFoundException() ;
        }

        $client = $clientRegistry->getClient($service);
        
        if( !$client ) {
            return new JsonResponse( array('status' => false, 'message' => "User not found !"));
        }else{
            return $this->redirectToRoute('app_home');
        }
    }

}
