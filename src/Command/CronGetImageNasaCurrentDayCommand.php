<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use App\Repository\NasaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cron:get-image-nasa-current-day',
    description: "Ajoute l'image de la date courante de la nasa dans la BDD.",
    aliases: ['app:cron:get-image-nasa-current-day']
)]
class CronGetImageNasaCurrentDayCommand extends Command
{
    private $logger ;
    private $nasaRepository ;
    private $entityManager ;

    public function __construct(LoggerInterface $logger , NasaRepository $nasaRepository, EntityManagerInterface $entityManager)
    {
        $this->logger = $logger ;
        $this->nasaRepository = $nasaRepository ;
        $this->entityManager  = $entityManager ;

        parent::__construct();
    }


    protected function configure(): void
    {
        $this->setHelp("Cette Command récupère l'image de la date courante de la nasa et l'insére dans la BDD.") ; // the command help shown when running the command with the "--help" option
        $this->setDescription("Cette Command récupère l'image de la date courante de la nasa et l'insére dans la BDD.") ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->info("Execution de la Command : Ajout de l'image de la Nasa ") ;

        $output->writeln([
            "Ajoute l'image de la date courante de la nasa dans la BDD.",
            '============',
            '',
        ]) ;

        $io = new SymfonyStyle($input, $output);

        // On récupère le service définit dans service.yaml
        $serviceNasa = $this->getApplication()->getKernel()->getContainer()->get('app.cron.nasa');

        $nasa = $serviceNasa->getImageNasaCurrentDay() ; 
        
        $this->logger->info( "Récupèration de l'image ", [ 'nasa' => $nasa->getTitle() ] ) ;

        $output->writeln($nasa) ;

        $io->success('trigger user without update status');

        return Command::SUCCESS;
    }
}
