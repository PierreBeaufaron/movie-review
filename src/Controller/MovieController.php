<?php

namespace App\Controller;

use App\Entity\Director;
use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movies', name: 'movies_list')]
    public function list(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();
        return $this->render('movie/list.html.twig', [
            'active_menu' => 'movies_list',
            'page_title' => 'Liste des films',
            'movies' => $movies,
        ]);
    }

    #[Route('/movie/{id}', name: "movie_item")]
    public function item(Movie $movie): Response
    {
        return $this->render('movie/item.html.twig', [
            'active_menu' => 'movies_list',
            'page_title' => $movie->getTitle(),
            'movie' => $movie,
        ]);
    }

    #[Route('movie/add',  name: "add_movie", methods: ["GET", "POST"])]
    public function add(
        Request $request,
        EntityManagerInterface $em
        ): Response
    {

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ICI GÉRER L'AJOUT DU RÉAL !!!
            $directorName = $form->get('director')->getData(); // Récupération du contenu du champs 'director'
            // On le test en faisant un findOneBy dans la BDD
            $director = $em->getRepository(Director::class)->findOneBy(['name' => $directorName]);

            // Si le réalisateur n'existe pas, on le crée
            if (!$director) {
                $director = new Director();
                $director->setName($directorName);
                $em->persist($director);
            }

            // Associer l'objet réaliasteur au film
            $movie->setDirector($director);

            // Si tout va bien, alors on peut persister l'entité et valider les modifications en BDD
            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('movie_add_success');
        }

        return $this->render('movie/movie_add.html.twig', [
            'active_menu' => 'movie',
            'page_title' => 'Ajouter un film',
            'movie_add_form' => $form->createView(),
        ]);

    }

    #[Route('/movie/addsuccess', name: "movie_add_success")]
    public function newsletterConfirm(): Response
    {
        return $this->render('index/movie_add_success.html.twig', [
            'active_menu' => 'movie_list',
            'page_title' => 'Félicitations !' 
        ]);
    }

}
