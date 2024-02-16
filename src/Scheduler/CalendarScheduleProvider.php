<?php

namespace App\Scheduler;

use App\Message\OpenDayMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;


#[AsSchedule('calendar')]
class CalendarScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return ( new Schedule() )->add(
            RecurringMessage::every( '5 seconds', new OpenDayMessage() )
        );
    }
}