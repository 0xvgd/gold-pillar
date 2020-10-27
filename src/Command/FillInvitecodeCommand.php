<?php

namespace App\Command;

use App\Entity\Security\User;
use App\Service\UserService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FillInvitecodeCommand extends Command
{
    protected static $defaultName = 'app:fill-invitecode';

    private $em;
    private $userService;

    public function __construct(EntityManagerInterface $em, UserService $userService)
    {
        parent::__construct();
        $this->em = $em;
        $this->userService = $userService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->em->getRepository(User::class)->findBy([
            'inviteCode' => null,
        ]);

        foreach ($users as $user) {
            $this->em->transactional(function (EntityManager $em) use ($user) {
                $em->lock($user, LockMode::PESSIMISTIC_WRITE);

                do {
                    $inviteCode = $this->userService->generateInviteCode($user);
                    $exists = $this->userService->existsInviteCode($em, $inviteCode);
                } while ($exists);

                $user->setInviteCode($inviteCode);
                $em->persist($user);
                $em->flush();
            });
        }

        $io->success('successful');

        return 0;
    }
}
