<?php

namespace App\EventListener;

use Survos\MobileBundle\Event\KnpMenuEvent;
use Survos\MobileBundle\Menu\KnpMenuHelperInterface;
use Survos\MobileBundle\Menu\KnpMenuHelperTrait;
use Survos\MobileBundle\Menu\MenuService;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsEventListener(event: KnpMenuEvent::MOBILE_TAB_MENU, method: 'navbarMenu')]
// #[AsEventListener(event: KnpMenuEvent::NAVBAR_MENU2, method: 'navbarMenu')]
#[AsEventListener(event: KnpMenuEvent::MOBILE_PAGE_MENU, method: 'pageMenu')]
final class MobileMenuEventListener implements KnpMenuHelperInterface
{
    use KnpMenuHelperTrait;

    public function __construct(
        #[Autowire('%kernel.environment%')] private string $env,
        private MenuService $menuService,
        private Security $security,
        private ?AuthorizationCheckerInterface $authorizationChecker = null
    ) {
    }

    public function appAuthMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
//        $this->menuService->addAuthMenu($menu);
    }

    public function navbarMenu(KnpMenuEvent $event): void
    {
        // items in the navbar should not be in the menu
        $menu = $event->getMenu();
        $options = $event->getOptions();
        $this->add($menu, id: 'loading', label: 'load', icon: 'fa-refresh');
        $this->add($menu, id: 'pokemon', label: 'POKE', icon: 'fa-home');
        $this->add($menu, id: 'saved', label: 'Saved', icon: 'fa-database',
            badge: '?'
        );
        $this->add($menu, id: 'gallery', label: 'Slides', icon: 'fa-images');
        $this->add($menu, id: 'share', label: 'Share', icon: 'fa-qrcode');
//        $this->add($menu, id: 'blank', label: 'blank', icon: 'fa-trash');

    }

    public function sidebarMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $options = $event->getOptions();
    }

    public function footerMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $options = $event->getOptions();
        $this->add($menu, uri: 'https://github.com');
    }

    // this could also be called the content menu, as it's below the navbar, e.g. a menu for an entity, like show, edit
    public function pageMenu(KnpMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $options = $event->getOptions();
        $this->add($menu, id: 'about', icon: 'fa-info');
        $this->add($menu, id: 'login', icon: 'fa-sign-in');
    }
}
