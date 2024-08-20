<?php

namespace App\DataFixtures;

use App\Entity\Director;
use App\Entity\Genre;
use App\Entity\Movie;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    private const NBR_MOVIES = 200;
    private const NBR_DIRECTORS = 50;
    private const GENRES_NAMES = ['Action', 'Aventure', 'Comédie', 'Drame', 'Fantastique', 'Horreur', 'Romance', 'Science-fiction', 'Thriller', 'Western'];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $genres = [];
        $directors = [];

        foreach (self::GENRES_NAMES as $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);

            $manager->persist($genre);
            $genres[] = $genre;
        }

        function generateDeathDate($faker) {
            // Génère un nombre aléatoire entre 0 et 1
            $random = $faker->randomFloat(2, 0, 1);
            
            // Si le nombre est inférieur à 0.5, retourne null, sinon génère une date
            if ($random < 0.5) {
                return null;
            } else {
                // Retourne une date aléatoire dans le passé
                return $faker->dateTimeBetween('-100 years', 'now');
            }
        }


        for ($i = 0; $i < self::NBR_DIRECTORS; $i++) {
            $director = new Director();
            $director
            ->setName($faker->name())
            ->setBirthDate($faker->dateTimeBetween('-170 years', '-20 years'))
            ->setDeathDate(generateDeathDate($faker));

            $manager->persist($director);
            $directors[] = $director;
        }

        for ($i = 0; $i < self::NBR_MOVIES; $i++) {
            $movie = new Movie();
            $movie
                ->setTitle($faker->words($faker->numberBetween(1,9), true))
                ->setReleaseDate($faker->dateTimeBetween('-100 years', 'now'))
                ->setDuration($faker->numberBetween(80, 240))
                ->setSynopsis($faker->realTextBetween(150, 600))
                ->setGenre($faker->randomElement($genres))
                ->setDirector($faker->randomElement($directors));

                $manager->persist($movie); // Mise en mémoire de $article dans manager à chaque tour
        }

        $manager->flush(); // pousse tout le contenu de $manager dans le BDD

    }
}
