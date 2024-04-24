<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        // render the "pages"
        $templates = [];
        foreach (['about','gallery','home','pokemon','saved','login','share','detail'] as $route) {
            $templates[$route] = $this->renderView("app/$route.html.twig");
        }
        return $this->render('app/index.html.twig', [
            'templates' => $templates,
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
    #[Route('/detail', name: 'app_detail')]
    #[Template('app/detail.html.twig')]
    public function detail(): array
    {
        return [];
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('app/login.html.twig', [
        ]);
    }

    #[Route('/about', name: 'app_about')]
    #[Template('app/about.html.twig')]
    public function about(): array
    {
        return [];
    }

    #[Route('/layout', name: 'app_layout')]
    #[Template('@SurvosMobile/initial-layout.html.twig')]
    public function layout(): array
    {
        return [];
    }

    #[Route('/{pageCode}', name: 'app_page')]
    public function page(Request $request, string $pageCode): Response
    {
        return $this->render("app/{$pageCode}.html.twig", $request->query->all());
    }

}
