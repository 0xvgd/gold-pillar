<?php

namespace App\Command;

use App\Entity\Finance\ExpensePayment;
use App\Entity\Finance\IncomePayment;
use App\Entity\Finance\Payment;
use App\Entity\Finance\Recurring;
use App\Entity\Finance\RecurringExpense;
use App\Entity\Finance\RecurringIncome;
use App\Enum\PeriodUnit;
use App\Service\Finance\PaymentsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RecurringPaymentCommand extends Command
{
    protected static $defaultName = 'app:recurring-payment';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PaymentsService
     */
    private $paymentService;

    private $queryCache = [];

    public function __construct(
        EntityManagerInterface $em,
        PaymentsService $paymentService
    ) {
        parent::__construct();
        $this->em = $em;
        $this->paymentService = $paymentService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate recurring payment for all resources')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'Custom date (Y-m-d) to process instead of current time')
            ->addOption('time', null, InputOption::VALUE_REQUIRED, 'Custom time (H:i) to process instead of current time')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $customDate = $input->getOption('date');
        $customTime = $input->getOption('time');
        $date = null;
        $time = null;

        if ($customDate) {
            $date = DateTime::createFromFormat('Y-m-d', $customDate);
        }

        if (!$date) {
            $date = new DateTime();
        }

        if ($customTime) {
            $time = DateTime::createFromFormat('H:i', $customTime);
        }

        if (!$time) {
            $time = new DateTime();
        }

        $date->setTime(
            (int) $time->format('H'),
            (int) $time->format('i'),
            0
        );

        $offset = 0;
        $maxResults = 100;
        $recurringQuery = $this
            ->em
            ->createQueryBuilder()
            ->select('e')
            ->from(Recurring::class, 'e')
            ->getQuery()
            ->setMaxResults($maxResults);

        do {
            /** @var Recurring[] $recurrings */
            $recurrings = $recurringQuery
                ->setFirstResult($offset)
                ->getResult();

            foreach ($recurrings as $recurring) {
                if ($this->canProcess($recurring, $date) && !$this->isProcessed($recurring, $date)) {
                    $payment = $this->createPayment($recurring);
                    $io->note(sprintf(
                        'Payment generated for resource %s. Amount %s, type: %s',
                        $recurring->getResource()->getId(),
                        $payment->getAmount(),
                        get_class($payment)
                    ));
                }
            }

            $count = count($recurrings);
            $offset += $count;
        } while ($count >= $maxResults);

        $io->success('Recurring processed to date '.$date->format('Y-m-d H:i'));

        return 0;
    }

    private function canProcess(Recurring $recurring, DateTime $atDate): bool
    {
        $period = $recurring->getInterval();
        $t1 = $recurring->getTime()->format('H:i');
        $t2 = $atDate->format('H:i');

        if ($t2 >= $t1) {
            if (PeriodUnit::DAY()->equals($period)) {
                // daily, check only the time
                return true;
            }

            if (PeriodUnit::WEEK()->equals($period)) {
                $dow1 = $recurring->getDayOrMonth();
                $dow2 = (int) $atDate->format('N');

                return $dow1 === $dow2;
            }

            if (PeriodUnit::MONTH()->equals($period)) {
                $dom1 = $recurring->getDayOrMonth();
                $dom2 = (int) $atDate->format('d');

                return $dom1 === $dom2;
            }

            if (PeriodUnit::YEAR()->equals($period)) {
                $m1 = $recurring->getDayOrMonth();
                $m2 = (int) $atDate->format('m');

                return $m1 === $m2;
            }
        }

        return false;
    }

    private function isProcessed(Recurring $recurring, DateTime $atDate): bool
    {
        $period = $recurring->getInterval();
        $lastPayment = $this->getLastPayment($recurring);

        if ($lastPayment) {
            // do not process the past
            if ($lastPayment->getCreatedAt() > $atDate) {
                return true;
            }

            if (PeriodUnit::DAY()->equals($period)) {
                $interval = $lastPayment->getCreatedAt()->diff($atDate);

                return 0 === $interval->d;
            }

            if (PeriodUnit::WEEK()->equals($period)) {
                $interval = $lastPayment->getCreatedAt()->diff($atDate);

                return 7 >= $interval->d;
            }

            if (PeriodUnit::MONTH()->equals($period)) {
                $d1 = $lastPayment->getCreatedAt()->format('Y-m');
                $d2 = $atDate->format('Y-m');

                return $d1 === $d2;
            }

            if (PeriodUnit::YEAR()->equals($period)) {
                $y1 = $lastPayment->getCreatedAt()->format('Y');
                $y2 = $atDate->format('Y');

                return $y1 === $y2;
            }
        }

        return false;
    }

    private function getLastPayment(Recurring $recurring): ?Payment
    {
        $lastPayment = null;
        $className = null;
        if ($recurring instanceof RecurringIncome) {
            $className = IncomePayment::class;
        } elseif ($recurring instanceof RecurringExpense) {
            $className = ExpensePayment::class;
        }

        if ($className) {
            // save the query into a variable to improve performance
            if (!isset($this->queryCache[$className])) {
                $query = $this
                    ->em
                    ->createQueryBuilder()
                    ->select('e')
                    ->from($className, 'e')
                    ->where('e.recurring = :recurring')
                    ->orderBy('e.createdAt', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery();

                $this->queryCache[$className] = $query;
            }

            $lastPayment = $this
                ->queryCache[$className]
                ->setParameter('recurring', $recurring)
                ->getOneOrNullResult();
        }

        return $lastPayment;
    }

    private function createPayment(Recurring $recurring): ?Payment
    {
        $resource = $recurring->getResource();
        $user = $resource->getOwner();
        $payment = null;

        if ($recurring instanceof RecurringIncome) {
            $payment = new IncomePayment($resource);
        } elseif ($recurring instanceof RecurringExpense) {
            $payment = new ExpensePayment($resource);
        }

        if ($payment) {
            $payment
                ->setAmount($recurring->getAmount())
                ->setNote($recurring->getDescription())
                ->setRecurring($recurring);

            if ($payment instanceof IncomePayment) {
                $this->paymentService->addIncome($user, $payment, true);
            } elseif ($payment instanceof ExpensePayment) {
                $this->paymentService->addExpense($user, $payment, true);
            }
        }

        return $payment;
    }
}
