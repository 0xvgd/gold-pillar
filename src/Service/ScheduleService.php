<?php

namespace App\Service;

use App\Entity\Calendar\Event;
use App\Entity\Person\Agent;
use App\Entity\Schedule\Day;
use App\Entity\Schedule\Schedule;
use App\Entity\Schedule\Shift;
use App\Repository\Calendar\EventRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class ScheduleService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUniqueDaysAsString(array $dates): array
    {
        $days = array_values(array_unique(array_map(function (DateTime $dt) {
            return $dt->format('Y-m-d');
        }, $dates)));

        return $days;
    }

    public function getUniqueTimesAsString(array $dates): array
    {
        $hours = array_values(array_unique(array_map(function (DateTime $dt) {
            return $dt->format('H:i');
        }, $dates)));

        return $hours;
    }

    public function getNext30AvailableDaysFromNow(Agent $agent): array
    {
        $start = new DateTime();

        return $this->getNext30AvailableDays($agent, $start);
    }

    public function getNext30AvailableDays(Agent $agent, DateTime $start): array
    {
        $max = 30;

        return $this->getAvailableDays($agent, $start, null, $max);
    }

    /**
     * Returns the available days limited by endtime or maximum results.
     */
    public function getAvailableDays(Agent $agent, DateTime $start, DateTime $end = null, int $max = null): array
    {
        if (null === $end && null === $max) {
            throw new Exception('You must need to fill end date or max results');
        }

        $schedule = $this->getSchedule($agent);

        if (!$schedule) {
            return [];
        }

        $dt = clone $start;
        $days = [];
        $shifts = $schedule->getShifts();
        $interval = new DateInterval('P1D');

        while (true) {
            $weekDay = (int) ($dt->format('N') - 1);
            /** @var Shift $shift */
            foreach ($shifts as $shift) {
                /** @var Day $day */
                $day = $shift->getDays()->get($weekDay);
                if ($day->isEnabled()) {
                    $days[] = clone $dt;
                }
            }
            $dt->add($interval);
            if (null !== $end && $dt > $end) {
                break;
            }
            if (null !== $max && count($days) >= $max) {
                break;
            }
        }

        return $days;
    }

    /**
     * Returns the available times limited by endtime or maximum results.
     */
    public function getAvailableTimesFromAgent(Agent $agent, DateTime $date): array
    {
        $schedule = $this->getSchedule($agent);

        return $this->getAvailableTimes($schedule, $date);
    }

    /**
     * Returns the available times limited by endtime or maximum results.
     */
    public function getAvailableTimes(Schedule $schedule, DateTime $date): array
    {
        $shifts = $schedule->getShifts();
        $weekDay = (int) ($date->format('N') - 1);
        $times = [];
        $events = $this->getBookedEvents($schedule, $date);

        /** @var Shift $shift */
        foreach ($shifts as $shift) {
            $duration = $shift->getDuration();
            $interval = new DateInterval("PT{$duration}M");
            /** @var Day $day */
            $day = $shift->getDays()->get($weekDay);
            if ($day->isEnabled()) {
                /** @var DateTime $startTime */
                $startTime = clone $day->getStartTime();
                /** @var DateTime $endTime */
                $endTime = clone $day->getEndTime();
                while ($startTime < $endTime) {
                    $free = true;
                    /** @var Event $event */
                    foreach ($events as $event) {
                        $str = $startTime->format('H:i');
                        if ($str >= $event->getStart()->format('H:i') && $str < $event->getEnd()->format('H:i')) {
                            $free = false;
                            break;
                        }
                    }
                    if ($free) {
                        $times[] = clone $startTime;
                    }
                    $startTime->add($interval);
                }
            }
        }

        return $times;
    }

    /**
     * @param Schedule $schedule
     * @param DateTime $date
     * @return array
     */
    public function getAvailableTimesFixed(Schedule $schedule, DateTime $date): array
    {
        $shifts = $schedule->getShifts();
        $weekDay = (int) ($date->format('N') - 1);
        $times = [];
        $events = $this->getBookedEvents($schedule, $date);
        $times_str = [];
        $interval = null;
        /** @var Shift $shift */
        foreach ($shifts as $shift) {
            $duration = $shift->getDuration();
            $interval = new DateInterval("PT{$duration}M");
            /** @var Day $day */
            $day = $shift->getDays()->get($weekDay);
            if ($day->isEnabled()) {
                /** @var DateTime $startTime */
                $startTime = clone $day->getStartTime();
                /** @var DateTime $endTime */
                $endTime = clone $day->getEndTime();
                while ($startTime < $endTime) {
                    $free = true;
                    /** @var Event $event */
                    foreach ($events as $event) {
                        $str = $startTime->format('H:i');
                        if ($str >= $event->getStart()->format('H:i') && $str < $event->getEnd()->format('H:i')) {
                            $free = false;
                            break;
                        }
                    }
                    $timeStr = clone $startTime;
                    $timeStr = $timeStr->format('H:i');
                    if(in_array($timeStr,$times_str) === false){
                        $times_str[] = $timeStr;
                        $times[] = ['val' => $timeStr,'free' => $free?1:0 ];
                    }
                    $startTime->add($interval);
                }
            }


        }
        if(count($times) >0 ){
            $times = array_chunk($times,5);
            $len = count($times);
            if(count($times[$len-1]) < 5 && $interval != null){
                $len_item = count($times[$len-1]);
                $value = $times[$len-1][$len_item-1]['val'];
                $started  = new DateTime($value);
                for($i = 0;$i<5-$len_item;$i++){
                    $started->add($interval);
                    $temp = clone $started;
                    $times[$len-1][] = array('val' =>$temp->format('H:i'),'free' =>0);
                }
            }

        }
        return $times;
    }


    public function getBookedEvents(Schedule $schedule, DateTime $date): array
    {
        $user = $schedule->getAgent()->getUser();
        /** @var EventRepository $repository */
        $repository = $this->em->getRepository(Event::class);

        $start = clone $date;
        $start->setTime(0, 0, 0);
        $end = clone $date;
        $end->setTime(23, 59, 59);

        $events = $repository->findByUser($user, $start, $end);

        return $events;
    }

    public function getSchedule(Agent $agent): ?Schedule
    {
        $schedule = $this
            ->em
            ->getRepository(Schedule::class)
            ->findOneBy(['agent' => $agent]);

        return $schedule;
    }

    public function addMonth($date, $month = 1)
    {
        $cal = new DateTime($date);
        $count = 'P' . $month . 'M';
        $interval = new DateInterval($count);
        $cal->add($interval);
        return $cal->format('Y-m-d');
    }

    public function subMonth($date, $month = 1)
    {
        $cal = new DateTime($date);
        $count = 'P' . $month . 'M';
        $interval = new DateInterval($count);
        $cal->sub($interval);
        return $cal->format('Y-m-d');
    }

    public function addDays($date, $day = 1)
    {
        $cal = new DateTime($date);
        if($day < 0) {
            $day = abs($day);
        }
        $count = 'P' . $day . 'D';
        $interval = new DateInterval($count);
        $cal->add($interval);
        return $cal->format('Y-m-d');
    }

    public function subDays($date, $day = 1)
    {
        $cal = new DateTime($date);
        $count = 'P' . $day . 'D';
        $interval = new DateInterval($count);
        $cal->sub($interval);
        return $cal->format('Y-m-d');
    }

    public function get35DayPeriod($start){

        $date_at = $start;
        $result = [];
        for($i = 0;$i<5;$i++){
            $days = [];
            for($j=0;$j<7;$j++){
                $days[] = ['text' => intval(date('d',strtotime($date_at))),'full' => date('F j, Y',strtotime($date_at)),'value' => $date_at,'week' => substr(date('D',strtotime($date_at)),0,1)];
                $date_at = $this->addDays($date_at);
            }
            $result[] = ['name'=>date('F Y',strtotime($date_at)),'days' => $days];
        }

        return $result;

    }
}
