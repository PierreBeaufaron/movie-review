<?php

namespace App\DataFixtures;

use App\Entity\Actor;
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
    private const ACTORS_NAMES = [
        'Brad Pitt',
        'Al Pacino',
        'Robert De Niro',
        'Uma Thurman',
        'Ian McKellen',
        'Jean-Paul Belmondo',
        'Robert Duvall',
        'Michelle Pfieffer',
        'Diane Keaton',
        'Marlon Brando',
        'John Cazale',
        'Tim Curry',
        'Jodie Foster',
        'Christofer Lee',
        'Susan Sarandon'
    ];
    private const IMG_LINK = [
        'https://atalante-cinema.org/wp-content/uploads/2022/09/pierrot-le-fou-1.jpeg',
        'https://www.francetvinfo.fr/pictures/KleiMledusfgJdwJJsgGTN6QCAI/1200x675/2019/08/16/phpdEKrWP.jpg',
        'https://leregardlibre.com/wp-content/uploads/2020/05/Scarface-5-%C2%A9-Universal-Pictures.jpg',
        'https://media.gqmagazine.fr/photos/60dddd067498dd8ce617b911/master/pass/killbill.jpg',
        'https://bullesdeculture.com/bdc-content/uploads/2020/06/le-seigneur-des-anneaux-le-retour-du-roi-de-peter-jackson.jpg',
        'https://i.f1g.fr/media/eidos/orig/2023/07/31/XVMfc3e47b6-2eee-11ee-8876-e7eed8ec0437.jpg',
        'https://www.rayonvertcinema.org/wp-content/uploads/2023/10/Taxi-Driver-Martin-Scorsese.jpg',
        'https://www.radiofrance.fr/s3/cruiser-production/2023/07/647ee1e6-d9aa-456c-ae6f-8a4b242acf1c/1200x680_sc_rocky-horror-picture-show.jpg'
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $genres = [];
        $actors = [];
        $directors = [];

        foreach (self::GENRES_NAMES as $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);

            $manager->persist($genre);
            $genres[] = $genre;
        }

        foreach (self::ACTORS_NAMES as $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);

            $manager->persist($actor);
            $actors[] = $actor;
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
                ->setTitle($faker->words($faker->numberBetween(1,4), true))
                ->setReleaseDate($faker->dateTimeBetween('-100 years', 'now'))
                ->setDuration($faker->numberBetween(80, 240))
                ->setSynopsis($faker->realTextBetween(150, 600))
                ->setImgSrc($faker->randomElement(self::IMG_LINK))
                ->setGenre($faker->randomElement($genres))
                ->setDirector($faker->randomElement($directors));

            // Attribuer de 1 à 4 acteurs aléatoires au film
            $randomActors = $faker->randomElements($actors, $faker->numberBetween(1, 4));
            foreach ($randomActors as $actor) {
                $movie->addActor($actor);  // Utilise la méthode addActor() pour ajouter l'acteur
            }

            $manager->persist($movie); // Mise en mémoire de $article dans manager à chaque tour
        }

        $manager->flush(); // pousse tout le contenu de $manager dans le BDD

    }
}
