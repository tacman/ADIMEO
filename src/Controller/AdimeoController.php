<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdimeoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {  
        return $this->json( "An action will be trigger every two minutes", 200 ) ;
    }

    #[Route('/users', name: 'all_users')]
    public function allUsers(UserRepository $userrepository): Response
    {
        $users = $userrepository->findAll() ;

        return $this->render('adimeo/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/addData', name: 'add_data', methods:['POST'] )]
    public function addData(UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User() ;
        $plaintextPassword = "azerty";

        $user->setNom("Mory") ;
        $user->setPrenom('Robert') ;
        $user->setPhone("0147859635");
        $user->setEmail(\str_shuffle('robert@miguel.com')) ;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setDone(0);
        $user->setRoles(array('ROLE_USER')) ;
        
        $this->entityManager->persist($user) ;
        $this->entityManager->flush() ;

        return $this->json("Un nouvel utilisateur a bien été créé") ;
    }
}
