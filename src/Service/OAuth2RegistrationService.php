<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;


class OAuth2RegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager, 
        private UserRepository $userRepository
    ){
    }

    /**
     * 
     * @param GoogleUser|GithubResourceOwner|FacebookUser $resourceOwner
     * 
     */
    public function saveUser(ResourceOwnerInterface $resourceOwner): User
    {
        dd('dd');
        $user = match (true){

            $resourceOwner instanceof GoogleUser => ( new User() )
                ->setEmail( $resourceOwner->getEmail() )
                ->setGoogleId( $resourceOwner->getId() )
                ->setDone(true)
                ->setCreatedAt(new \DateTimeImmutable() )
                ->setUpdatedAt(new \DateTimeImmutable() )
                ->setRoles(['ROLE_USER']) ,
            $resourceOwner instanceof GithubResourceOwner => ( new User() )
                ->setEmail( $resourceOwner->getEmail() )
                ->setGitHubId( $resourceOwner->getId() )
                ->setDone(true)
                ->setCreatedAt(new \DateTimeImmutable() )
                ->setUpdatedAt(new \DateTimeImmutable() )
                ->setRoles(['ROLE_USER']) ,
            $resourceOwner instanceof FacebookUser => ( new User() )
                ->setEmail( $resourceOwner->getEmail() )
                ->setFbId( $resourceOwner->getId() )
                ->setDone(true)
                ->setCreatedAt(new \DateTimeImmutable() )
                ->setUpdatedAt(new \DateTimeImmutable() )
                ->setRoles(['ROLE_USER']) 
        } ;
     
        $this->userRepository->save($user, flush: true);
   
        return $user ;
    }


   

}