<?php

namespace App\Service;

use DateTime;
use App\Entity\Nasa;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class NasaService
{
    private $entityManager;
    private $client;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $client,){
        $this->entityManager = $entityManager;
        $this->client = $client;
    }
    
    // tâche crône ['app:cron:get-image-nasa-current-day']
    public function getImageNasaCurrentDay(): Nasa
    {
        $apiKey = $this->getParameter('app.api_key_nasa');

        $data = $this->client->request(
            'GET', 
            "https://api.nasa.gov/planetary/apod?api_key=$apiKey",
        )->toArray();

        $nasa = ( new Nasa() )
            ->setTitle( $data['title'] )
            ->setDescription( $data['explanation'] )
            ->setDateTime( $data['date'] )
            ->setImage( $data['url'] )
        ;

        $this->entityManager->persist($nasa) ;
        $this->entityManager->flush() ;

        return $nasa ;
    }


   

}
