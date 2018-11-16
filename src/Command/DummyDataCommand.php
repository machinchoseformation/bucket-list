<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Wish;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DummyDataCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'app:dummy-data';
    protected $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct();
        $this->passwordEncoder = $passwordEncoder;
    }

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

        //vide les tables avec une requête SQL brute
        $connection->query("SET FOREIGN_KEY_CHECKS = 0");
        $connection->query("TRUNCATE TABLE wish");
        $connection->query("TRUNCATE TABLE category");
        $connection->query("SET FOREIGN_KEY_CHECKS = 1");
        $io->text("Tables truncated!");

        $categories = ["Voyage", "Sport", "Folie", "Développement"];

        foreach($categories as $cat){
            $category = new Category();
            $category->setName($cat);
            $entityManager->persist($category);
        }
        $entityManager->flush();
        $io->text("Categories added!");

        //récupère tous les objets Category qu'on vient de créer
        $categoryRepository = $doctrine->getRepository(Category::class);
        $allCategories = $categoryRepository->findAll();

        /*
         * les users
         */
        $user = new User();
        $user->setUsername("yo");
        $user->setEmail("yo@gmail.com");
        //php bin/console app:dummy-data

        //hash le mdp
        $hash = $this->passwordEncoder->encodePassword($user, "yo");
        $user->setPassword($hash);

        $user->setDateRegistered(new \DateTime("- 3 years"));
        $user->setRoles(["ROLE_USER"]);

        $entityManager->persist($user);
        $entityManager->flush();

        //ici, créer plusieurs users bidons...

        $wishNum = 400;
        $io->text("Adding $wishNum wishes...");
        $io->progressStart($wishNum);
        for($i=0; $i<$wishNum; $i++) {
            $wish = new Wish();

            //prend une catégorie au hasard et l'affecte à notre wish
            $randomCategory = $allCategories[array_rand($allCategories)];
            $wish->setCategory($randomCategory);

            $wish->setLabel($faker->sentence);
            $wish->setDescription($faker->optional(0.5)->text(1000));
            $dateCreated = $faker->dateTimeBetween("- 2 years");
            $wish->setDateCreated($dateCreated);
            $dateUpdated = $faker->optional(0.3)->dateTimeBetween($dateCreated);
            $wish->setDateUpdated($dateUpdated);
            //affecte un auteur à l'idée
            $wish->setAuthor($user);
            $entityManager->persist($wish);
            $io->progressAdvance();

            //commentaires
            $commentsNum = mt_rand(0,12);
            for($c=0; $c<$commentsNum; $c++){
                $comment = new Comment();
                $comment->setWish($wish);
                $comment->setContent($faker->realText(300));
                $comment->setEmail($faker->email);
                $comment->setRating(mt_rand(0,5));
                $comment->setDateCreated($faker->dateTimeBetween($dateCreated));
                $wish->addComment($comment);
                $entityManager->persist($comment);
            }

            $wish->updateAverageRating();
            $entityManager->flush();
        }

        $io->progressFinish();
        $entityManager->flush();
        $io->text("$wishNum wishes added!");

        $io->success('Done!');
    }
}
