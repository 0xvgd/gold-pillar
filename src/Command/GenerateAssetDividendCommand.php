<?php

namespace App\Command;

use App\Entity\Resource\Asset;
use App\Service\Payments\AssetPaymentsService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GenerateAssetDividendCommand extends Command
{
    protected static $defaultName = 'app:generate:asset:dividend';
    private $em;
    private $params;

    public function __construct(
        ParameterBagInterface $params,
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate monthly dividend from asset.')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $itemsAdded = 0;

        $assets = $this->em->getRepository(Asset::class)->findAll();

        foreach ($assets as $asset) {
            // $this->assetPaymentsService->paymentsGenerate($asset);
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
