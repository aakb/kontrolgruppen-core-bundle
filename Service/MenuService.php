<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Service;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Twig\TwigExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MenuService.
 */
class MenuService
{
    protected $translator;
    protected $router;
    protected $twigExtension;
    protected $authorizationChecker;
    protected $processManager;

    /**
     * MenuService constructor.
     *
     * @param TranslatorInterface           $translator
     * @param RouterInterface               $router
     * @param TwigExtension                 $twigExtension
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ProcessManager                $processManager
     */
    public function __construct(TranslatorInterface $translator, RouterInterface $router, TwigExtension $twigExtension, AuthorizationCheckerInterface $authorizationChecker, ProcessManager $processManager)
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->twigExtension = $twigExtension;
        $this->authorizationChecker = $authorizationChecker;
        $this->processManager = $processManager;
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
        $menu = [];

        if ($this->authorizationChecker->isGranted('ROLE_SAGSBEHANDLER')) {
            $menu['dashboard'] = $this->createGlobalNavItem(
                'dashboard',
                'dashboard',
                ('/' === $path),
                'dashboard_index'
            );

            $menu['process'] = $this->createGlobalNavItem(
                'process',
                'process',
                false !== $this->startsWith($path, '/process/'),
                'process_index'
            );
        } elseif ($this->authorizationChecker->isGranted('ROLE_PROCESS_VIEW')) {
            $menu['search_external'] = $this->createGlobalNavItem(
                'search_external',
                'search-external',
                false !== $this->startsWith($path, '/process/'),
                'search_external'
            );
        }

        if ($this->authorizationChecker->isGranted('ROLE_BI')) {
            $menu['bi'] = $this->createGlobalNavItem(
                'bi',
                'bi',
                false !== $this->startsWith($path, '/bi/'),
                'bi_index'
            );
        }

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $menu['admin'] = $this->createGlobalNavItem(
                'admin',
                'admin',
                false !== $this->startsWith($path, '/admin/'),
                'admin_index'
            );
        }

        return $menu;
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
        $items = [];
        if (isset($process) && null !== $process->getId()) {
            $items[] = [
                'name' => $this->translator->trans('menu.menu_title.process_number', [
                    '%processNumber%' => $process->getCaseNumber(),
                ]),
                'disabled' => true,
                'active' => false,
                'path' => '#',
                'hide_from_mobile_menu' => true,
            ];

            $items[] = $this->createMenuItem(
                'process_show',
                1 === preg_match(
                    '/^\/process\/[0-9]+$/',
                    $path
                ),
                'process_show',
                ['id' => $process->getId()]
            );

            $items[] = $this->createMenuItem(
                'client',
                1 === preg_match(
                    '/^\/process\/[0-9]+\/client.*$/',
                    $path
                ),
                'client_show',
                ['process' => $process]
            );

            if ($this->authorizationChecker->isGranted('edit', $process)) {
                $items[] = $this->createMenuItem(
                    'reminder',
                    1 === preg_match(
                        '/^\/process\/[0-9]+\/reminder\/.*$/',
                        $path
                    ),
                    'reminder_index',
                    ['process' => $process]
                );
            }

            $items[] = $this->createMenuItem(
                'journal',
                1 === preg_match(
                    '/^\/process\/[0-9]+\/journal\/.*$/',
                    $path
                ),
                'journal_entry_index',
                ['process' => $process]
            );

            $items[] = $this->createMenuItem(
                'economy',
                1 === preg_match(
                    '/^\/process\/[0-9]+\/economy.*$/',
                    $path
                ),
                'economy_show',
                ['process' => $process]
            );

            if ($this->processManager->isRevenueAvailable($process)) {
                $items[] = $this->createMenuItem(
                    'revenue',
                    1 === preg_match(
                        '/^\/process\/[0-9]+\/revenue.*$/',
                        $path
                    ),
                    'economy_revenue',
                    ['process' => $process]
                );
            }

            $items[] = $this->createMenuItem(
                'conclusion',
                1 === preg_match(
                    '/^\/process\/[0-9]+\/conclusion.*$/',
                    $path
                ),
                'conclusion_show',
                ['process' => $process]
            );

            $items[] = $this->createMenuItem(
                'group',
                1 === preg_match(
                    '/^\/process\/[0-9]+\/group.*$/',
                    $path
                ),
                'process_group_index',
                ['process' => $process]
            );

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $items[] = $this->createMenuItem(
                    'log',
                    1 === preg_match(
                        '/^\/process\/[0-9]+\/log.*$/',
                        $path
                    ),
                    'process_log_index',
                    ['process' => $process]
                );
            }
        }

        return $items;
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
                'export',
                1 === preg_match(
                    '/^\/admin\/export\/.*$/',
                    $path
                ),
                'export_index'
            ),
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
                'reason',
                1 === preg_match(
                    '/^\/admin\/reason\/.*$/',
                    $path
                ),
                'reason_index'
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
            $this->createMenuItem(
                'account',
                1 === preg_match(
                    '/^\/admin\/account\/.*$/',
                    $path
                ),
                'account_index'
            ),
            $this->createMenuItem(
                'income_type',
                1 === preg_match(
                    '/^\/admin\/income_type\/.*$/',
                    $path
                ),
                'income_type_index'
            ),
            $this->createMenuItem(
                'forwarded_to_authority',
                1 === preg_match(
                    '/^\/admin\/forwarded_to_authority\/.*$/',
                    $path
                ),
                'forwarded_to_authority_index'
            ),
            $this->createMenuItem(
                'change_net_default_value',
                1 === preg_match(
                    '/^\/admin\/change_net_default_value\/.*$/',
                    $path
                ),
                'change_net_default_value_index'
            ),
            $this->createMenuItem(
                'change_status_on_completed_processes',
                1 === preg_match(
                    '/^\/admin\/change_status_on_completed_processes\/.*$/',
                    $path
                ),
                'change_status_on_completed_processes'
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
     * @param       $itemName
     * @param bool  $active
     * @param       $pathName
     * @param array $pathParameters
     * @param bool  $disabled
     *
     * @return array
     */
    protected function createMenuItem($itemName, $active = false, $pathName = null, $pathParameters = [], $disabled = false)
    {
        return [
            'name' => $this->translator->trans('menu.menu_title.'.$itemName),
            'active' => $active,
            'path' => null !== $pathName ? $this->router->generate($pathName, $pathParameters, UrlGeneratorInterface::RELATIVE_PATH) : '#',
            'disabled' => $disabled,
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
