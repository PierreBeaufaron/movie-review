<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/actor')]
class ActorController extends AbstractController
{
    #[Route('/{id}', name: 'movie_by_actor')]
    public function byActor(Actor $actor, MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findByActor($actor);

        return $this->render('movie/list.html.twig', [
            'active_menu' => 'movie_list',
            'page_title' => "Films avec " . $actor->getName(),
            'movies' => $movies
        ]);
    }
}
