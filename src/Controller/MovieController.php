<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Director;
use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\ActorRepository;
use App\Repository\DirectorRepository;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/movie')]
class MovieController extends AbstractController
{

    #[Route('/', name: 'movies_list')]
    public function list(MovieRepository $movieRepository, Request $request): Response
    {       
        $title = $request->query->get('title');
        $genre = $request->query->get('genre');

        if (!empty($title) && !empty($genre) && $genre == 'none') { // Champs de recherche si on arrive par là
            $movies = $movieRepository->findByExampleField($title);
        } elseif (!empty($title) && !empty($genre) && $genre !== 'none') {
            $movies = $movieRepository->findBySomeField($title, $genre);
        } else {
            $movies = $movieRepository->findBy([], ['id' => 'DESC']);
        }

        return $this->render('movie/list.html.twig', [
            'active_menu' => 'movies_list',
            'page_title' => 'Tous les films',
            'movies' => $movies,
            'query' => $title,
        ]);
    }

    #[Route('/admin', name: 'movie_crud_index', methods: ['GET'])]
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('movie_crud/index.html.twig', [
            'movies' => $movieRepository->findAll(),
            'active_menu' => 'movies_list',
            'page_title' => 'Éditer les films'
        ]);
    }

    #[Route('/admin/new', name: 'movie_crud_new', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository,
        EntityManagerInterface $em,
        SluggerInterface $slugger
        ): Response
    {

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ICI GÉRER L'AJOUT DU RÉAL !!!
            $directorName = $form->get('director')->getData(); // Récupération du contenu du champs 'director'
            // On le test en faisant un findOneBy dans la BDD
            $director = $directorRepository->findOneBy(['name' => $directorName]);

            // Si le réalisateur n'existe pas, on le crée
            if (!$director) {
                $director = new Director();
                $director->setName($directorName);
                $em->persist($director);
            }

            // Associer l'objet réalisateur au film
            $movie->setDirector($director);

            // GÉRER L'UPLOAD DE FICHIER
            /** @var UploadedFile $imgSrcUrl */
            $imgSrcUrl = $form->get('imgSrcUrl')->getData();

            if ($imgSrcUrl) { // Si une image est présente, alors on déclenche l'upload
                // On récupère le nom original du fichier, tel que nommé par le client
                $originalFilename = pathinfo($imgSrcUrl->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                // On peut alors construire le nom du fichier tel qu'il sera stocké sur le serveur
                $filename = $safeFilename . '-' . uniqid() . '.' . $imgSrcUrl->guessExtension();

                try {
                    // À la manière de move_uploaded_file en PHP, on tente ici de déplacer le fichier
                    // de sa zone de transit à sa destination
                    // Cette méthode peut lancer des exceptions : on encadre donc l'appel par un bloc
                    // try...catch
                    $imgSrcUrl->move(
                        'uploads/movie/',
                        $filename
                    );
                    // Si on n'est pas passé dans le catch, alors on peut enregistrer le nom du fichier
                    // dans la propriété profilePicFilename de l'utilisateur
                    $movie->setImgSrc($filename);
                } catch (FileException $e) {
                    $form->addError(new FormError("Erreur lors de l'upload du fichier"));
                }
            }

            $actorNames = [
                $form->get('actor1')->getData(),
                $form->get('actor2')->getData(),
                $form->get('actor3')->getData(),
                $form->get('actor4')->getData(),
            ];

            foreach ($actorNames as $actorName) {
                $actorName = trim($actorName);
                if ($actorName === '') {
                    continue;
                }

                // Vérifier si l'acteur existe déjà
                $actor = $actorRepository->findOneBy(['name' => $actorName]);

                if (!$actor) {
                    // Créer un nouvel acteur s'il n'existe pas
                    $actor = new Actor();
                    $actor->setName($actorName);
                    $em->persist($actor);
                }

                // Ajouter l'acteur au film
                $movie->addActor($actor);

            }

            // Si tout va bien, alors on peut persister l'entité et valider les modifications en BDD
            $em->persist($movie);
            $em->flush();

            return $this->redirectToRoute('movie_add_success', ['movie_name' => $movie->getTitle()]);
        }

        return $this->render('movie_crud/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
            'active_menu' => 'movies_list',
            'page_title' => 'Ajouter un film'
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'movie_crud_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Movie $movie,
        EntityManagerInterface $entityManager,
        DirectorRepository $directorRepository,
        ActorRepository $actorRepository
    ): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement du réalisateur
            $directorName = $form->get('director')->getData();
            $director = $directorRepository->findOneBy(['name' => $directorName]);
            
            if (!$director) {
                $director = new Director();
                $director->setName($directorName);
                $entityManager->persist($director);
            }
            
            $movie->setDirector($director);
            
            // Traitement des acteurs
            $actorNames = [
                $form->get('actor1')->getData(),
                $form->get('actor2')->getData(),
                $form->get('actor3')->getData(),
                $form->get('actor4')->getData(),
            ];

            // Supprimer les anciens acteurs
            foreach ($movie->getActors() as $existingActor) {
                $movie->removeActor($existingActor);
            }

            foreach ($actorNames as $actorName) {
                $actorName = trim($actorName);
                if ($actorName === '') {
                    continue;
                }

                $actor = $actorRepository->findOneBy(['name' => $actorName]);

                if (!$actor) {
                    $actor = new Actor();
                    $actor->setName($actorName);
                    $entityManager->persist($actor);
                }
                
                $movie->addActor($actor);
            }

            $entityManager->flush();

            return $this->redirectToRoute('movie_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        // Pré-remplir les champs avec les données existantes
        $form->get('director')->setData($movie->getDirector() ? $movie->getDirector()->getName() : '');

        $actors = $movie->getActors();
        $actorFields = ['actor1', 'actor2', 'actor3', 'actor4'];
        foreach ($actorFields as $index => $actorField) {
            $form->get($actorField)->setData(isset($actors[$index]) ? $actors[$index]->getName() : '');
        }

        return $this->render('movie_crud/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
            'active_menu' => 'movies_list',
            'page_title' => 'Modifier ' . $movie->getTitle()
        ]);
    }

    #[Route('/admin/{id}', name: 'movie_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('movie_crud_index', [], Response::HTTP_SEE_OTHER);
    }

    // Autocompletion du champ de recherche
    #[Route('/autocomplete', name: 'movie_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request, MovieRepository $movieRepository): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        $titles = $movieRepository->createQueryBuilder('m')
            ->select('m.title')
            ->where('m.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('m.title', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        // Extraire les titres du tableau de resultat
        $titleList = array_map(function($movie) {
            return $movie['title'];
        }, $titles);

        return new JsonResponse($titleList);
    }

    #[Route('/{id}', name: "movie_item")]
    public function item(Movie $movie): Response
    {
        return $this->render('movie/item.html.twig', [
            'active_menu' => 'movies_list',
            'page_title' => $movie->getTitle(),
            'movie' => $movie,
        ]);
    }


    #[Route('/crud/success', name: "movie_add_success")]
    public function movieAddSuccess(Request $request): Response
    {
        $movieName = $request->query->get('movie_name');

        return $this->render('movie/movie_add_success.html.twig', [
            'active_menu' => 'movie_list',
            'page_title' => 'Félicitations !',
            'movie_name' => $movieName,
        ]);
    }

}
