<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AdminDashboard(routePath: '/', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private PokemonRepository $pokemonRepository,
        private UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public function index(): Response
    {
//        return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        return $this->render('app/landing.html.twig', [
            'pokemon' => $this->pokemonRepository->findBy([], ['name' => 'ASC'], 10),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pokemon');
    }

    public function configureCrud(): Crud
    {
        return parent::configureCrud()
            ->showEntityActionsInlined();
    }


    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Pokemon', 'fas fa-list', Pokemon::class);

        yield MenuItem::section('external', icon: 'tabler:external-link');

        yield MenuItem::linkToUrl('Issues', 'tabler:brand-github', 'https://github.com/survos-sites/pokemon/issues')
            ->setLinkTarget(
                '_blank'
            );
        yield MenuItem::linkToUrl('API', 'mdi:api', $this->urlGenerator->generate('api_doc'))
            ->setLinkTarget(
                '_blank'
            );
        yield MenuItem::linkToRoute('Search', 'mdi:search',
            routeName: 'meili_insta', routeParameters: ['indexName' => 'poke_Pokemon']
        )
            ->setLinkTarget(
                '_blank'
            );

    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')

//            ->remove(Crud::PAGE_INDEX, Action::NEW)
//            ->remove(Crud::PAGE_INDEX, Action::DELETE)
//            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

}
