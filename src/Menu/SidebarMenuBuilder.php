<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Symfony\Contracts\Translation\TranslatorInterface;

class SidebarMenuBuilder
{
    /**
     * @param FactoryInterface $factory
     */
    public function __construct(
        FactoryInterface $factory,
        Security $security,
        Environment $twig,
        RequestStack $requestStack,
        TranslatorInterface $translator,
    ) {
        $this->factory = $factory;
        $this->security = $security;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function createSidebarMenu(): ?ItemInterface
    {
        $menuClassName = 'nav nav-pills nav-sidebar flex-column nav-child-indent nav-flat';
        $linkClassName = 'nav-link';
        $navItem = 'nav-item';
        $navItem = 'nav-item';
        $icon = 'nav-icon material-icons';
        $subTitleClass = 'nav-header text-uppercase';

        $menu = $this->factory->createItem('root', [
            'childrenAttributes' => [
                'class' => $menuClassName,
                'data-widget' => 'treeview',
                'data-accordion' => 'false',
                'data-xyz' => 'abc',
                'data-abc' => 'false',
                'role' => 'menu',
            ],
        ]);

        $menu->addChild('menu.main_header', [])->setAttribute('class', $subTitleClass);

        $menu->addChild('menu.home', [
            'route' => 'home',
            'linkAttributes' => ['class' => $linkClassName],
            'extras' => [
                'icon' => $icon,
                'icon_content' => 'home',
                'label_wrapper' => 'p',
            ],
        ])->setAttributes(['class' => $navItem]);

        if ($this->security->isGranted('ROLE_USER_VIEW')) {
            $menu->addChild('menu.users', [
                'route' => 'user.index',
                'linkAttributes' => ['class' => $linkClassName],
                'extras' => [
                    'icon' => $icon,
                    'icon_content' => 'people',
                    'label_wrapper' => 'p',
                    // 'badge' => 'UP',
                    // 'badge_attr' => ['class' => 'right badge badge-info'],
                ],
            ])->setAttributes(['class' => $navItem]);
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $menu->addChild('menu.roles', [
                'route' => 'roles.management',
                'linkAttributes' => ['class' => $linkClassName],
                'extras' => [
                    'icon' => $icon,
                    'icon_content' => 'security',
                    'label_wrapper' => 'p',
                ],
            ])->setAttributes(['class' => $navItem]);
        }
        
        $menu->addChild('menu.client_header', [])->setAttribute('class', $subTitleClass);

        if ($this->security->isGranted('ROLE_CLIENT_VIEW')) {
            $menu->addChild('menu.prospect_management', [
                'route' => 'client.list',
                'linkAttributes' => ['class' => $linkClassName],
                'extras' => [
                    'icon' => $icon,
                    'icon_content' => 'inventory',
                    'label_wrapper' => 'p',
                ],
            ])->setAttributes(['class' => $navItem]);
        }
        
        $menu->addChild('menu.project_management', [
            'route' => 'project.index',
            'linkAttributes' => ['class' => $linkClassName],
            'extras' => [
                'icon' => $icon,
                'icon_content' => 'engineering',
                'label_wrapper' => 'p',
            ],
        ])->setAttributes(['class' => $navItem]);

        // add text
        $menu->addChild('menu.offers_header', [])->setAttribute('class', $subTitleClass);

        // Offers submenu
        $offersMenu = $this->factory->createItem('menu.offers_menu', [
            'uri' => '#',
            'linkAttributes' => ['class' => $linkClassName],
            'childrenAttributes' => [
                'class' => 'nav nav-treeview',
            ],
            'extras' => [
                'icon' => $icon,
                'icon_content' => 'work',
                'label_wrapper' => 'p',
            ],
        ])->setAttributes(['class' => $navItem]);

        $offersMenu->addChild('menu.offers', [
            'linkAttributes' => ['class' => $linkClassName],
            'uri' => '#',
            'extras' => [
                'icon' => $icon,
                'icon_content' => 'list',
                'label_wrapper' => 'p',
            ],
        ])->setAttributes(['class' => $navItem]);

        $offersMenu->addChild('menu.offers_new', [
            'linkAttributes' => ['class' => $linkClassName],
            'uri' => '#',
            'extras' => [
                'icon' => $icon,
                'icon_content' => 'add',
                'label_wrapper' => 'p',
            ],
        ])->setAttributes(['class' => $navItem]);

        $menu->addChild($offersMenu);

        return $menu;
    }
}
