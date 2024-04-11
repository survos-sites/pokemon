<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    /**
     * @return Response
     *
     * this is the main controller, really it's a SPA at this point
     */
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
        ]);
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('app/home.html.twig', [
        ]);
    }
    #[Route('/share', name: 'app_share')]
    public function share(): Response
    {
        return $this->render('app/share.html.twig', [
        ]);
    }

    #[Route('/pokemon', name: 'app_pokemon')]
    public function pokemon(): Response
    {
        return $this->render('app/pokemon.html.twig', [
        ]);
    }

    #[Route('/saved', name: 'app_saved')]
    public function saved(): Response
    {
        return $this->render('app/saved.html.twig', [
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('app/login.html.twig', [
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('app/about.html.twig', [
        ]);
    }
}
