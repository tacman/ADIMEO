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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class OAuth2RegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager, 
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $hasher 
    ){
    }

    /**
     * 
     * @param GoogleUser|GithubResourceOwner|FacebookUser $resourceOwner
     * 
     */
    public function saveUser(ResourceOwnerInterface $resourceOwner): User
    {
        $user = match (true){

            $resourceOwner instanceof GoogleUser => ( new User() )
                ->setNom( $resourceOwner->getLastName() )
                ->setPrenom( $resourceOwner->getFirstName() )
                ->setGoogleId( $resourceOwner->getId() ) ,
            $resourceOwner instanceof GithubResourceOwner => ( new User() )
                ->setNom( $resourceOwner->getName() )
                ->setPrenom( $resourceOwner->getNickname() )
                ->setGitHubId( $resourceOwner->getId() ) ,
            $resourceOwner instanceof FacebookUser => ( new User() )
                ->setNom( $resourceOwner->getLastName() )
                ->setPrenom( $resourceOwner->getFirstName() )
                ->setFbId( $resourceOwner->getId() ) 
        } ;

        $user
            ->setEmail( $resourceOwner->getEmail() )
            ->setDone(true)
            ->setCreatedAt(new \DateTimeImmutable() )
            ->setUpdatedAt(new \DateTimeImmutable() )
            ->setRoles(['ROLE_USER'])
            ->setPassword(
                $this->hasher->hashPassword( $user, "azerty" )
            ) ;
     
        $this->userRepository->save($user, flush: true);

        return $user ;
    }


   

}
