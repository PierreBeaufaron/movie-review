<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Director;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher)
  {
  }

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
        'Susan Sarandon',
        'Daniel Day_Lewis',
        'Anouk Aimée',
        'Jean-Paul Belmondo',
        'Anna Karina',
        'Brigite Bardot'
    ];
    private const IMG_LINK = [
        'pierrot-le-fou-1.jpeg',
        'phpdEKrWP.jpg',
        'Scarface.jpg',
        'killbill.webp',
        'le-seigneur-des-anneaux-le-retour-du-roi-de-peter-jackson.jpg',
        'leparrain.webp',
        'TaxiDriver.webp',
        'rocky-horror-picture-show.jpg',
        'sincity.webp',
        'lotr.jpg',
        'orange.webp',
        'diehard.jpg',
        'pulpfiction.jpg',
        'blowup.jpg',
        'twbb.jpg',
        'uheuf.webp',
        'tgtbtu.jpg',
        'serpico',
        'seven.webp',
        'inthemood.webp'
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $genres = [];
        $actors = [];
        $directors = [];

        // --- GENRES ---------------------------------------------------
        foreach (self::GENRES_NAMES as $genreName) {
            $genre = new Genre();
            $genre->setName($genreName);

            $manager->persist($genre);
            $genres[] = $genre;
        }

        // --- ACTORS ---------------------------------------------------
        foreach (self::ACTORS_NAMES as $actorName) {
            $actor = new Actor();
            $actor->setName($actorName);

            $manager->persist($actor);
            $actors[] = $actor;
        }

        // --- DEATH DATE ---------------------------------------------------
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

        // --- DIRECTORS ---------------------------------------------------
        for ($i = 0; $i < self::NBR_DIRECTORS; $i++) {
            $director = new Director();
            $director
            ->setName($faker->name())
            ->setBirthDate($faker->dateTimeBetween('-170 years', '-20 years'))
            ->setDeathDate(generateDeathDate($faker));

            $manager->persist($director);
            $directors[] = $director;
        }

        // --- MOVIES ---------------------------------------------------
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

        // --- USERS ---------------------------------------------------
        $admin = new User();
        $admin
            ->setEmail("admin@sf-news.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->hasher->hashPassword($admin, "admin1234"))
            ->setUsername("Jésus Christ");

        $manager->persist($admin);

        $user = new User();
        $user
            ->setEmail("user@test.com")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->hasher->hashPassword($user, "test4567"))
            ->setUsername("Ponce Pilate");

        $manager->persist($user);


        $manager->flush(); // pousse tout le contenu de $manager dans le BDD

    }
}
