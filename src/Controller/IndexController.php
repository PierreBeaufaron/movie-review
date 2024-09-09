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


    #[Route('/newsletter/subscribe', name: "newsletter_subscribe", methods: ["GET", "POST"])]
    public function newsletterSubscribe(
        Request $request,
        EntityManagerInterface $em
        ): Response 
    {

        $newsletter = new NewsletterMail();
        $form = $this->createForm(NewsletterMailType::class, $newsletter);

        // Prend en charge la requête entrante
        // et s'il y a des données, les met dans $newsletter
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si tout va bien, alors on peut persister l'entité et valider les modifications en BDD
            $em->persist($newsletter);
            $em->flush();

            return $this->redirectToRoute('newsletter_confirm');
        }

        return $this->render('index/newsletter.html.twig', [
            'active_menu' => 'contact',
            'page_title' => 'Newsletter',
            'newsletter_form' => $form->createView(),
        ]);

    }

    #[Route('/newsletter/thanks', name: "newsletter_confirm")]
    public function newsletterConfirm(): Response
    {
        return $this->render('index/newsletter_confirm.html.twig', [
            'active_menu' => 'contact',
            'page_title' => 'Merci' 
        ]);
    }

}
