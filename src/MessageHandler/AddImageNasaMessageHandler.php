<?php

namespace App\MessageHandler;

use App\Entity\Nasa;
use App\Repository\NasaRepository;
use App\Message\AddImageNasaMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class AddImageNasaMessageHandler
{
    public function __construct( private string $api_Key_nasa, private NasaRepository $nasaRepository, private EntityManagerInterface $manager, private HttpClientInterface $client)
    {
    }
    
    public function __invoke(AddImageNasaMessage $message)
    {
        $api_Key_nasa = $this->api_Key_nasa;

        $data = $this->client->request(
            'GET', 
            "https://api.nasa.gov/planetary/apod?api_key=$api_Key_nasa",
        )->toArray() ;

        $date = new  \DateTime($data['date']) ;
        $date->format('Y-m-d');

        // verifier si $data['url'] contient une vidéo youtube https://www.youtube.com/embed/x-wX-wClfig?rel=0

        $nasa = ( new Nasa() )
            ->setTitle( $data['title'] )
            ->setDescription( $data['explanation'] )
            ->setDateTime( $date )
            ->setImage( $data['url'] )
        ;

        $this->manager->persist($nasa) ;
        $this->manager->flush() ;

    }
}
