<?php

namespace App\Controller;

use App\Entity\Nasa;
use App\Entity\User;
use App\Repository\NasaRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdimeoController extends AbstractController
{
    private $entityManager;
    private $client;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $client,){
        $this->entityManager = $entityManager;
        $this->client = $client;
    }
    

    #[Route('/', name: 'app_home', methods:['GET']) ]
    public function index(NasaRepository $nasaRepository): Response
    {
        $data = $nasaRepository->findAll() ;

        return $this->render('adimeo/nasa.index.html.twig',[
            'data' => $data
        ]);
    }

    #[Route('/addData', name: 'add_data', methods:['GET'] )]
    public function addData(UserPasswordHasherInterface $passwordHasher): Response
    {
        $apiKey = $this->getParameter('app.api_key_nasa');
        // https://api.nasa.gov/planetary/apod?api_key=SkwCHZbUTWH86w1Nt8F8AZHsssjAysg2zPuPq5S2&start_date=2024-01-01&end_date=2024-01-15
        
        $data = $this->client->request(
            'GET', 
            "https://api.nasa.gov/planetary/apod?api_key=$apiKey&start_date=2024-01-01&end_date=2024-01-15",
        )->toArray();

        
        for ( $i=0; $i < count($data) ; $i++ ) { 

            $date = new  \DateTime($data[$i]['date']) ;
            $date->format('Y-m-d');

            $nasa = ( new Nasa() )
                ->setTitle( $data[$i]['title'] )
                ->setDescription( $data[$i]['explanation'] )
                ->setDateTime( $date )
                ->setImage( $data[$i]['url'] )
            ;

            $this->entityManager->persist($nasa) ;
        }

        $this->entityManager->flush() ;

        return $this->json("good");

        //return $this->redirectToRoute( 'nasa_show', ["id" => $nasa] );
    }
    

    #[Route('/addNasa', name: 'add_nasa', methods:['GET']) ]
    public function nasa(): Response
    {
        $apiKey = $this->getParameter('app.api_key_nasa');

        $data = $this->client->request(
            'GET', 
            "https://api.nasa.gov/planetary/apod?api_key=$apiKey",
        )->toArray();

        // $statusCode = $response->getStatusCode();
        // $content = $response->getContent();

        $nasa = ( new Nasa() )
            ->setTitle( $data['title'] )
            ->setDescription( $data['explanation'] )
            ->setDateTime( $data['date'] )
            ->setImage( $data['url'] )
        ;

        $this->entityManager->persist($nasa) ;
        $this->entityManager->flush() ;

        return $this->redirectToRoute( 'nasa_show', ["id" => $nasa] );
    }

    #[Route('/nasa/{id}', name: 'nasa_show_id', methods:['GET']) ]
    public function showNasa(Nasa $nasa, NasaRepository $nasaRepository): Response
    {
        if ($nasa === null) {
            throw $this->createNotFoundException();
        }

        $image =  $nasa->getImage() ;

        if ( empty( $nasa->getImage() ) ) {
            $id_current = $nasa->getId() ;
            $lastInd = (int) ( $id_current - 1 ) ;

            $lastNasa   = $nasaRepository->find($lastInd) ;
            $image = $lastNasa->getImage() ;
        }

        return $this->render('adimeo/nasa.jour.html.twig',[
            'nasa'  => $nasa,
            'image' => $image
        ]);
    }

    

}
