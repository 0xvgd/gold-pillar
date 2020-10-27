<?php

namespace App\Service;

use App\Entity\Calendar\Event;
use App\Entity\Resource\Resource;
use App\Entity\Resource\View;
use App\Entity\Schedule\Day;
use App\Entity\Security\User;
use App\Enum\CalendarEventType;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

final class BookingService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function add(User $user, Resource $resource, string $date, string $time): View
    {
        $em = $this->em;

        $startTime = DateTime::createFromFormat('Y-m-d H:i', "{$date} {$time}");
        $endTime = clone $startTime;
        $agent = $resource->getAgent();

        $count = (int) $em
            ->createQueryBuilder()
            ->select('COUNT(v)')
            ->from(View::class, 'v')
            ->join('v.event', 'e')
            ->where('e.date = :date')
            ->andWhere('v.resource = :resource')
            ->andWhere('v.customer = :user')
            ->setParameters([
                'date' => $startTime->format('Y-m-d'),
                'resource' => $resource,
                'user' => $user,
            ])
            ->getQuery()
            ->getSingleScalarResult();

        if ($count > 0) {
            throw new Exception('You cannot book more then one view/meeting to the same resource at the same day');
        }

        /** @var Day $day */
        $day = $em
            ->createQueryBuilder()
            ->select('d')
            ->from(Day::class, 'd')
            ->join('d.shift', 's')
            ->where('s.schedule = :schedule')
            ->andWhere('d.weekDay = :weekDay')
            ->andWhere('d.startTime <= :time')
            ->andWhere('d.endTime >= :time')
            ->setParameters([
                'time' => $startTime->format('H:i:s'),
                'weekDay' => $startTime->format('N'),
                'schedule' => $agent->getSchedule(),
            ])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        if (!$day) {
            throw new Exception('There is no workday to choosed date');
        }

        $duration = $day->getShift()->getDuration();
        $endTime->add(new DateInterval("PT{$duration}M"));

        $event = new Event();
        $event
            ->setType(CalendarEventType::VIEW())
            ->setTitle('Resource View')
            ->setDescription('')
            ->setOwner($user)
            ->setDate($startTime)
            ->setStartTime($startTime)
            ->setEndTime($endTime)
            ->addParticipant($user)
            ->addParticipant($agent->getUser());

        $view = new View();
        $view
            ->setCustomer($user)
            ->setResource($resource)
            ->setAgent($agent)
            ->setEvent($event)
        ;

        $em->persist($view);
        $em->flush();

        return $view;
    }
}
