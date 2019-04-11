<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Kontrolgruppen\CoreBundle\Twig\TwigExtension;
use Kontrolgruppen\CoreBundle\Entity\Process;

/**
 * Class MenuService.
 */
class MenuService
{
    protected $translator;
    protected $router;
    protected $twigExtension;

    /**
     * MenuService constructor.
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        TwigExtension $twigExtension
    ) {
        $this->translator = $translator;
        $this->router = $router;
        $this->twigExtension = $twigExtension;
    }

    /**
     * Get the global nav menu.
     *
     * @param string $path current path
     *
     * @return array
     */
    public function getGlobalNavMenu($path)
    {
        return [
            'dashboard' => $this->createGlobalNavItem(
                'dashboard',
                'dashboard',
                ('/' === $path),
                'dashboard_index'
            ),
            'process' => $this->createGlobalNavItem(
                'process',
                'process',
                (false !== $this->startsWith($path, '/process/')),
                'process_index'
            ),
            // @TODO: Implement these.
            /*
            'profile' => $this->createGlobalNavItem(
                'profile',
                '/profile/',
                'profile',
                (false !== $this->startsWith($path, '/profile/'))
            ),
            'users' => $this->createGlobalNavItem(
                'users',
                '/users/',
                'users',
                (false !== $this->startsWith($path, '/profile/'))
            ),
            */
            'admin' => $this->createGlobalNavItem(
                'admin',
                'admin',
                (false !== $this->startsWith($path, '/admin/')),
                'admin_index'
            ),
        ];
    }

    /**
     * Get the process menu.
     *
     * @param string  $path
     * @param Process $process
     *
     * @return array
     */
    public function getProcessMenu(string $path, Process $process = null)
    {
        if (isset($process) && null !== $process->getId()) {
            return [
                $this->createMenuItem(
                    'process_show',
                    1 === preg_match(
                        '/^\/process\/[0-9]+$/',
                        $path
                    ),
                    'process_show',
                    ['id' => $process->getId()]
                ),
                $this->createMenuItem(
                    'client',
                    1 === preg_match(
                        '/^\/process\/[0-9]+\/client.*$/',
                        $path
                    ),
                    'client_show',
                    ['process' => $process]
                ),
                $this->createMenuItem(
                    'reminder',
                    1 === preg_match(
                        '/^\/process\/[0-9]+\/reminder\/.*$/',
                        $path
                    ),
                    'reminder_index',
                    ['process' => $process]
                ),
                $this->createMenuItem(
                    'journal',
                    1 === preg_match(
                        '/^\/process\/[0-9]+\/journal\/.*$/',
                        $path
                    ),
                    'journal_entry_index',
                    ['process' => $process]
                ),
            ];
        }

        return [];
    }

    /**
     * Get the admin menu.
     *
     * @param string $path current path
     *
     * @return array
     */
    public function getAdminMenu($path)
    {
        return [
            $this->createMenuItem(
                'process_type',
                1 === preg_match(
                    '/^\/admin\/process_type\/.*$/',
                    $path
                ),
                'process_type_index'
            ),
            $this->createMenuItem(
                'process_status',
                1 === preg_match(
                    '/^\/admin\/process_status\/.*$/',
                    $path
                ),
                'process_status_index'
            ),
            $this->createMenuItem(
                'channel',
                1 === preg_match(
                    '/^\/admin\/channel\/.*$/',
                    $path
                ),
                'channel_index'
            ),
            $this->createMenuItem(
                'service',
                1 === preg_match(
                    '/^\/admin\/service\/.*$/',
                    $path
                ),
                'service_index'
            ),
            $this->createMenuItem(
                'quick_link',
                1 === preg_match(
                    '/^\/admin\/quick_link\/.*$/',
                    $path
                ),
                'quick_link_index'
            ),
        ];
    }

    /**
     * Generate global_nav item.
     *
     * @param $itemName
     * @param $iconName
     * @param $active
     * @param $pathName
     * @param array $pathParameters
     *
     * @return object
     */
    protected function createGlobalNavItem($itemName, $iconName, $active, $pathName, $pathParameters = [])
    {
        return (object) [
            'name' => $this->translator->trans(
                'global_nav.menu_title.'.$itemName
            ),
            'icon' => $this->twigExtension->getIconClass($iconName),
            'tooltip' => $this->translator->trans(
                'global_nav.tooltip.'.$itemName
            ),
            'path' => $this->router->generate($pathName, $pathParameters, UrlGeneratorInterface::RELATIVE_PATH),
            'active' => $active,
        ];
    }

    /**
     * Create menu item.
     *
     * @param $itemName
     * @param $active
     * @param $pathName
     *
     * @return array
     */
    protected function createMenuItem($itemName, $active, $pathName, $pathParameters = [])
    {
        return [
            'name' => $this->translator->trans('menu.menu_title.'.$itemName),
            'active' => $active,
            'path' => $this->router->generate($pathName, $pathParameters, UrlGeneratorInterface::RELATIVE_PATH),
        ];
    }

    /**
     * Tests if the haystack starts with the needle.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    protected function startsWith($haystack, $needle)
    {
        $length = \strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }
}