<?php

namespace App\Security;

use App\Entity\User; 
use App\Repository\UserRepository;
use App\Service\MessageGeneratorService;
use App\Service\OAuth2RegistrationService;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;


abstract class AbstractOAuth2Authenticator extends OAuth2Authenticator {

    use TargetPathTrait;
    
    protected string $serviceName = "";
    protected $clientRegistry ;
    protected $router ;
    protected $userRepository ;
    protected $oAuth2RegistrationService ;
    protected $testService ;

    public function __construct(
            ClientRegistry $clientRegistry,
            RouterInterface $router,
            UserRepository $userRepository ,
            OAuth2RegistrationService $oAuth2RegistrationService,
            MessageGeneratorService $testService
        )
    {
        $this->clientRegistry = $clientRegistry ;
        $this->router = $router ;
        $this->userRepository = $userRepository ;
        $this->oAuth2RegistrationService = $oAuth2RegistrationService ;
        $this->testService = $testService ;
    }

    abstract protected function getUserFromRessourceProvider(ResourceOwnerInterface $resourceOwner): ?User ;
    abstract protected function getClient(): OAuth2ClientInterface ;

    
    /**
     * Cette fonction indique si l'authentificateur prend en charge la requête donnée
     * Elle doit renvoyer une valeur booléenne
     * @param Request $request
     */
    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'auth_oauth_check' 
            &&  $request->get('service') == $this->serviceName
            &&  $request->isMethod('GET') ;
    }

    /**
     * Cette fonction est appelée lorsque l'authentification a été exécutée avec succès.
     * Elle doit renvoyer un objet Response ou null.
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * providerKey (google, github, facebook)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        // Si le user n'est pas connecté on récupère la page à laquelle il souhtait accèder avec la fonction "getTargetPath()" 
        // Après la connexion il est redirigé sur la page qu'il voulait
        // redirection vers la page souhaité, exemple site e-commerce (process) achat
        if ( $targetPath = $this->getTargetPath($request->getSession(), $providerKey) ) {
            return new RedirectResponse($targetPath);
        }

        // Redirection sur la page home
        return new RedirectResponse( $this->router->generate('app_home') );
    }

    /**
     * Cette fonction est appelée lorsque l'authentification a été exécutée, 
     * mais a échouée (par exemple mauvaise apikey).
     * Il doit renvoyer un objet Response ou null
     * 
     * @param Request $request
     * @param AuthenticationException $exception
     *
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        
        return new RedirectResponse( $this->router->generate('app_login') );
    }

    public function getResourceOwnerFromAccessToken(AccessToken $accessToken): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($accessToken) ;
    }

    /**
     * Cette fonction obtient les identifiants d'authentification de la requête 
     * et les renvoie dans un tableau ou une variable, 
     * 
     * @param Request $request
     */
    public function authenticate(Request $request): SelfValidatingPassport
    {
        $accessToken = $this->fetchAccessToken( $this->getClient() );
        $resourceOwnerProvider = $this->getResourceOwnerFromAccessToken($accessToken) ;
      
       // dd($resourceOwnerProvider) ;
        $user = $this->getUserFromRessourceProvider($resourceOwnerProvider) ; // google, github, facebook
        
        if( null === $user){
            // $user = $this->oAuth2RegistrationService->saveUser($resourceOwnerProvider);
            $toto = $this->testService->getHappyMessage() ;
            dd('dd');
        }
        dd('dd');
        return new SelfValidatingPassport(
            userBadge:  new UserBadge( $user->getUserIdentifier(), fn () => $user)  ,
            badges : [
                new RememberMeBadge()
            ]
        );
    }



   

    
    


    
}