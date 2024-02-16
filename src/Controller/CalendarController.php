<?php

namespace App\Controller;

use App\Message\OpenDayMessage;
use App\Repository\DayRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(DayRepository $dayRepository): Response
    {
        $days = $dayRepository->findBy([], ['number' => 'ASC']);

        return $this->render('calendar/index.html.twig', [
            'days' => $days,
        ]);
    }

    #[Route('/open', name: 'open')]
    public function open(MessageBusInterface $bus): Response
    {
       
        // will cause the SmsNotificationHandler to be called
        $bus->dispatch( new OpenDayMessage() );

        return $this->redirectToRoute('app_calendar') ;
    }
}
