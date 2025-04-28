<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use Knp\Menu\FactoryInterface;
use Survos\MobileBundle\Event\KnpMenuEvent;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        protected FactoryInterface $factory,
    )
    {
    }

    /**
     * @return Response
     *
     * this is the main controller, really it's a SPA at this point
     */
//    #[Route('/', name: 'app_index')]
//    public function index(): Response
//    {
//        // render the "pages"
//        $templates = [];
//        foreach (['about','gallery','home','pokemon','saved','login','share','detail','loading'] as $route) {
//            $templates[$route] = $this->renderView("app/$route.html.twig");
//        }
//        return $this->render('app/index.html.twig', [
//            'templates' => $templates,
//        ]);
//    }

    #[Route('/landing', name: 'app_homepage', options: ['expose' => true], methods: ['GET'])]
    #[Template('app/landing.html.twig')]
    public function homepage(Request $request, PokemonRepository $pokemonRepository,
    #[MapQueryParameter()] int $limit = 5
    ): Response|array
    {
        return [
            'pokemon' => $pokemonRepository->findBy([], ['name' => 'ASC'], $limit),
        ];

    }

    #[Route('/mobile', name: 'app_mobile', options: ['expose' => true], methods: ['GET'])]
    public function mobile(Request $request): Response
    {

        // iterate through the page and tab routes to create templates, which will be rendered in the main page.
        $menu = $this->factory->createItem($options['name'] ?? KnpMenuEvent::class);
        foreach ([KnpMenuEvent::MOBILE_TAB_MENU  => 'tab',
                     KnpMenuEvent::MOBILE_PAGE_MENU => 'page',
                     KnpMenuEvent::MOBILE_UNLINKED_MENU => 'page',
                     ] as $eventName=>$type) {
            $options = [];
            $options = (new OptionsResolver())
                ->setDefaults([

                ])
                ->resolve($options);
            $this->eventDispatcher->dispatch(new KnpMenuEvent($menu, $this->factory, $options), $eventName);
            foreach ($menu->getChildren() as $route=>$child) {
                try {
                $template = "app/$route.html.twig";
                $params = [
                    'type' => $type,
                    'route' => $route,
                    'template' => $template,
                    'debug' => $request->get('debug', false),
                ];
//                    $templates[$route]  = $this->twig->render($template, $params);
                    $templates[$route] = $this->renderView($template, $params);
                } catch (\Exception $e) {
                    dd($route, $template, $e->getMessage(), $e);
                }
            }
        }

        return $this->render('start.html.twig', [
            'templates' => $templates,
            'playNow' => $request->get('playNow', true),
        ]);
    }

    #[Route('/offline', name: 'app_offline', priority: 1)]
    public function offline(): Response {
        return $this->render('app/offline.html.twig');
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
