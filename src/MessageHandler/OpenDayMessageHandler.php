<?php

namespace App\MessageHandler;

use App\Message\OpenDayMessage;
use App\Repository\DayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class OpenDayMessageHandler
{
    public function __construct(private DayRepository $dayRepository, private EntityManagerInterface $manager)
    {
    }

    public function __invoke(OpenDayMessage $message )
    {
        // On recupere le prochain jour à false et on le met à jour
        $nextDay = $this->dayRepository->findOneBy( ['open' => 0], ['number' => 'ASC'] ) ; 
        
        $nextDay->setOpen(true);
        // sleep(2) ;
        $this->manager->flush() ;

    }
}
