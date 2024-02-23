<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AbstractOAuth2Authenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

class GithubAuthenticator extends AbstractOAuth2Authenticator
{
    protected string $serviceName = "github" ;

    protected function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient( $this->serviceName ) ;
    }


    protected function getUserFromRessourceProvider(ResourceOwnerInterface $resourceOwner): ?User
    {
        if( !($resourceOwner instanceof GithubResourceOwner ) ){
            throw new \RuntimeException("expecting github user", 1);
        }

        // if( true ==! ($resourceOwner->toArray()['email_verify']) ?? null ){
        //     throw new AuthenticationException(" L'email n'a pas été confirmé.") ;
        // }

        $existingUser = $this->userRepository->findOneBy([
            'gitHubId' => $resourceOwner->getId() ,
            'email'    => $resourceOwner->getEmail()
        ]);

        return $existingUser ;
    }

}
