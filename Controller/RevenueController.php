<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Service\EconomyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EconomyController.
 *
 * @Route("/process/{process}/revenue")
 */
class RevenueController extends BaseController
{
    /**
     * @Route("/", name="economy_revenue")
     */
    public function revenue(Request $request, Process $process, EconomyService $economyService)
    {
        $parameters = [];

        $parameters['revenue'] = $economyService->calculateRevenue($process);

        $parameters['menuItems'] = $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;

        return $this->render(
            '@KontrolgruppenCore/revenue/revenue.html.twig',
            $parameters
        );
    }
}
