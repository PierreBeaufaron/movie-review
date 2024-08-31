<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GenreController extends AbstractController
{
    #[Route('/genre/{id}', name: 'genre_item')]
    public function item(Genre $genre, MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findBy(['genre' => $genre], ['id' => 'DESC']);

        return $this->render('genre/item.html.twig', [
            'active_menu' => 'movie_list',
            'page_title' => $genre->getName(),
            'genre' => $genre,
            'movies' => $movies
        ]);
    }
}
