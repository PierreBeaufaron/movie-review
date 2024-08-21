<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
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

    // Technique de base qui ne gère pas les erreurs 404
    // #[Route('/movie/{id}', name: "movie_item")]
    // public function item(MovieRepository $movieRepository, int $id = 0): Response
    // {
    //     //$id = $request->query->getInt('id');
    //     $movie = $movieRepository->find($id);

    //     return $this->render('movie/item.html.twig', [
    //         'active_menu' => 'movies_list',
    //         'movie' => $movie
    //     ]);
    // }
}
