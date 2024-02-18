<?php

namespace App\Command;

use DateTime;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cron:update-user-data',
    description: 'Cette Command met à jour le status des utilisateurs.',
    aliases: ['app:cron:update-user-data']
)]
class UpdateStatusUserDataCommand extends Command
{
    private $logger ;
    private $userRepository ;
    private $entityManager ;

    public function __construct(LoggerInterface $logger , UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger ;
        $this->userRepository = $userRepository ;
        $this->entityManager  = $entityManager ;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('Cette Command met à jour le status des utilisateurs.') ; // the command help shown when running the command with the "--help" option
        $this->setDescription('Cette Command met à jour le status des utilisateurs.') ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info('Execution de la command : Mise à jour des status des utilisateurs.') ;

        $output->writeln([
            'Execution de la command : Mise à jour des status des utilisateurs qui ne sont pas inscrits.',
            '============',
            '',
        ]) ;

        $io = new SymfonyStyle($input, $output);

        // On récupère le service définit dans service.yaml
        $serviceUser = $this->getApplication()->getKernel()->getContainer()->get('app.cron.task');

        $updateUsers = $serviceUser->updateUserStatusDone() ; 

        foreach ($updateUsers as $user) {
            $this->logger->info( 'Update status user', ['userId' => $user->getId()] ) ;
            // $output->writeln( $user->getNom() ) ;
        } 

        $output->writeln($updateUsers) ;

        $io->success('trigger user without update status');

        return Command::SUCCESS;
    }
}
