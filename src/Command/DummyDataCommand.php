<?php

namespace App\Command;

use App\Entity\Wish;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DummyDataCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:dummy-data';

    protected function configure()
    {
        $this
            ->setDescription('Loads dummy data in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $faker = \Faker\Factory::create("fr_FR");
        $doctrine = $this->getContainer()->get('doctrine');
        $connection = $doctrine->getConnection();
        $entityManager = $doctrine->getManager();

        //vide la table avec une requête SQL brute
        $connection->query("TRUNCATE TABLE wish");
        $io->text("Table wish truncated!");

        $io->progressStart(1000);
        for($i=0; $i<1000; $i++) {
            $wish = new Wish();
            $wish->setLabel($faker->sentence);
            $wish->setDescription($faker->optional(0.5)->text(1000));
            $dateCreated = $faker->dateTimeBetween("- 2 years");
            $wish->setDateCreated($dateCreated);
            $dateUpdated = $faker->optional(0.3)->dateTimeBetween($dateCreated);
            $wish->setDateUpdated($dateUpdated);

            $entityManager->persist($wish);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $entityManager->flush();

        $io->success('1000 wishes loaded!');
    }
}
