<?php

namespace App\Service;

use DateTime;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    
    // tâche crône ['app:cron:update-user-data']
    public function updateUserStatusDone(): array
    {
        $users = $this->entityManager->getRepository(User::class)->findBy( ["done" => 0] );

        if( !empty($users) ){
            foreach( $users as $user){

                $user
                    ->setDone(true)
                    ->setCreatedAt(new \DateTimeImmutable() )
                    ->setUpdatedAt(new \DateTimeImmutable() )
                ;
            }
        }

        $this->entityManager->flush() ;

        return $users ;
    }


   

}
