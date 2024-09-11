<?php

namespace App\Controller;

use App\Entity\NewsletterMail;
use App\Form\NewsletterMailType;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findAll();

        return $this->render('index/home.html.twig', [
            'active_menu' => 'home',
            'page_title' => 'Bienvenue sur MovieReview ! ',
            'genres' =>$genres
        ]);
    }


}
