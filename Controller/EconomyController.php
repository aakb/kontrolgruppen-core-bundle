<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Kontrolgruppen\CoreBundle\Entity\ServiceEconomyEntry;
use Kontrolgruppen\CoreBundle\Form\ServiceEconomyEntryType;
use Kontrolgruppen\CoreBundle\Repository\EconomyEntryRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\EconomyEntry;
use Kontrolgruppen\CoreBundle\Form\EconomyEntryType;
use Kontrolgruppen\CoreBundle\DBAL\Types\EconomyEntryEnumType;
use Kontrolgruppen\CoreBundle\Entity\BaseEconomyEntry;
use Kontrolgruppen\CoreBundle\Form\BaseEconomyEntryType;
use Kontrolgruppen\CoreBundle\Service\EconomyService;

/**
 * Class EconomyController.
 *
 * @Route("/process/{process}/economy")
 */
class EconomyController extends BaseController
{
    /**
     * @Route("/revenue", name="economy_revenue")
     */
    public function revenue(Request $request, Process $process, EconomyService $economyService)
    {
        $parameters = [];

        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'economy.revenue.calculate_button',
        ]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parameters['revenue'] = $economyService->calculateRevenue($process);
        }

        $parameters['form'] = $form->createView();

        return $this->render(
            '@KontrolgruppenCore/economy/revenue.html.twig',
            $parameters
        );
    }

    /**
     * @Route("/", name="economy_show")
     */
    public function show(Request $request, Process $process, EconomyEntryRepository $economyEntryRepository)
    {
        $parameters = [];

        // Check for result of type form.
        $formResult = $this->handleEconomyEntryFormRequest($request, $process);
        $parameters['economy_entry_form'] = $formResult['form'];

        // Handle entity form.
        $chosenType = isset($formResult['chosenType']) ? $formResult['chosenType'] : null;
        $entityFormResult = $this->handleEntityForm($request, $process, $chosenType, $parameters);

        if ($entityFormResult instanceof RedirectResponse) {
            return $entityFormResult;
        }

        $parameters['collapse_economy_entry_form'] = !isset($formResult['submitted']) || !$formResult['submitted'];
        $parameters['menuItems'] = $this->menuService->getProcessMenu($request->getPathInfo(), $process);
        $parameters['process'] = $process;

        $parameters['economyEntriesService'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::SERVICE]);
        $parameters['economyEntriesAccount'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::ACCOUNT]);
        $parameters['economyEntriesArrear'] = $economyEntryRepository->findBy(['process' => $process, 'type' => EconomyEntryEnumType::ARREAR]);

        return $this->render(
            '@KontrolgruppenCore/economy/show.html.twig',
            $parameters
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     * @param $chosenType
     * @param $parameters
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function handleEntityForm(Request $request, Process $process, $chosenType, &$parameters)
    {
        // Decide if a type has been chosen.
        if (!$chosenType && $request->request->has('base_economy_entry')) {
            $chosenType = $request->request->get('base_economy_entry')['type'];
        } elseif (!$chosenType && $request->request->has('service_economy_entry')) {
            $chosenType = $request->request->get('service_economy_entry')['type'];
        }

        // Add given form if a type has been chosen.
        if ($chosenType) {
            if (EconomyEntryEnumType::SERVICE === $chosenType) {
                $economyEntry = new ServiceEconomyEntry();
                $economyEntry->setType($chosenType);
                $form = $this->createForm(ServiceEconomyEntryType::class, $economyEntry);
            } else {
                $economyEntry = new BaseEconomyEntry();
                $economyEntry->setType($chosenType);
                $form = $this->createForm(BaseEconomyEntryType::class, $economyEntry);
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $economyEntry->setProcess($process);
                $entityManager->persist($economyEntry);
                $entityManager->flush();

                return $this->redirectToRoute('economy_show', ['process' => $process]);
            }

            $parameters['form'] = $form->createView();
        }
    }

    /**
     * Handles the economy entry form.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Kontrolgruppen\CoreBundle\Entity\Process $process
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse parameters for the form, or redirects on success
     */
    public function handleEconomyEntryFormRequest(Request $request, Process $process)
    {
        $economyEntry = new EconomyEntry();
        $economyEntry->setProcess($process);
        $form = $this->createForm(EconomyEntryType::class, $economyEntry);
        $form->handleRequest($request);

        $result = [
            'economy_entry' => $economyEntry,
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $result['chosenType'] = $economyEntry->getType();
            $result['submitted'] = true;
        }

        return $result;
    }
}