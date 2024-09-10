<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/genres/crud')]
class GenreCrudController extends AbstractController
{
    #[Route('/', name: 'app_genre_crud_index', methods: ['GET'])]
    public function index(GenreRepository $genreRepository): Response
    {
        return $this->render('genre_crud/index.html.twig', [
            'genres' => $genreRepository->findAll(),
            'active_menu' => 'movies_list',
            'page_title' => 'Éditer les genres',
        ]);
    }

    #[Route('/new', name: 'app_genre_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();

            return $this->redirectToRoute('app_genre_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('genre_crud/new.html.twig', [
            'genre' => $genre,
            'form' => $form,
            'active_menu' => 'movies_list',
            'page_title' => 'Ajouter un genre',
        ]);
    }

    #[Route('/{id}', name: 'app_genre_crud_show', methods: ['GET'])]
    public function show(Genre $genre): Response
    {
        return $this->render('genre_crud/show.html.twig', [
            'genre' => $genre,
            'active_menu' => 'movies_list',
            'page_title' => 'Genre ' . $genre->getName(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_genre_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Genre $genre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_genre_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('genre_crud/edit.html.twig', [
            'genre' => $genre,
            'form' => $form,
            'active_menu' => 'movies_list',
            'page_title' => 'Modifier ' . $genre->getName(),
        ]);
    }

    #[Route('/{id}', name: 'app_genre_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Genre $genre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$genre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($genre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_genre_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
