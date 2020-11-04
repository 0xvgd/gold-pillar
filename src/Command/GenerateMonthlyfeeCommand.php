<?php

namespace App\Command;

use App\Service\Finance\AgentPaymentsService;
use App\Service\SubscribeService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenerateMonthlyfeeCommand extends Command
{
    protected static $defaultName = 'app:generate:monthlyfee';
    private $params;
    private $agentPaymentsService;
    private $subscriptionsService;
    private $em;

    public function __construct(
        ParameterBagInterface $params,
        AgentPaymentsService $agentPaymentsService,
        SubscribeService $subscriptionsService,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->params = $params;
        $this->agentPaymentsService = $agentPaymentsService;
        $this->subscriptionsService = $subscriptionsService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate monthly fee that will be deducted from commissions.')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $itemsAdded = 0;

        $monthRef = (int) date('m');
        $yearRef = (int) date('Y');
        $subscriptions = $this->subscriptionsService->getActiveSubscriptions();

        foreach ($subscriptions as $subscription) {
            $subscriptionDay = ($subscription->getSubscribedAt()->format('d'));
            $dueDate = $this->getValidDate($yearRef, $monthRef, $subscriptionDay);

            //TODO

            ++$itemsAdded;
        }

        $io = new SymfonyStyle($input, $output);

        if (0 === $itemsAdded) {
            $block = $this->getLogPrefix().' - no items inserted';
        } else {
            $block = sprintf($this->getLogPrefix()
                .' - A total of %s items have been added.', $itemsAdded);
        }

        $io->success($block);

        return 0;
    }

    protected function getValidDate($year, $month, $day)
    {
        $data = "$year-$month-$day";
        if (checkdate($month, $day, $year)) {
            $date = new DateTime($data);
        } else {
            $date = (new DateTime("$year-$month-1"))->modify('last day of this month');
        }

        return $date;
    }

    private function getLogPrefix()
    {
        $date = (new DateTime())->format('Y-m-d H:i:s');

        return "$date [".self::$defaultName.']';
    }
}
