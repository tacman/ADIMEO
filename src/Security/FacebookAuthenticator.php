<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AbstractOAuth2Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\OAuth2\Client\Provider\FacebookUser;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

class FacebookAuthenticator extends AbstractOAuth2Authenticator
{   
    protected string $serviceName = "facebook" ;

    public function __construct(
        private readonly ClientRegistry $clientRegistry ,
        private readonly UserRepository $userRepository
    )
    {}

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient( $this->serviceName ) ;
    }

    /**
     * Cette fonction obtient les identifiants d'authentification de la requête 
     * et renvoie un user, 
     * 
     * @param ResourceOwnerInterface $resourceOwner
     * @param UserRepository $userRepository
     * 
     * @return User
     */
    protected function getUserFromRessourceProvider(ResourceOwnerInterface $resourceOwner): ?User
    {
        if( !($resourceOwner instanceof FacebookUser ) ){
            throw new \RuntimeException("expecting github user", 1);
        }

        dd($resourceOwner);

        $existingUser = $this->userRepository->findOneBy([
            'fbId' => $resourceOwner->getId() ,
            'email'    => $resourceOwner->getEmail()
        ]);

        dd("existingUser") ;

        return $existingUser ;
    }

}
