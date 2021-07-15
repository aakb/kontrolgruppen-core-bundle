<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use http\Exception\RuntimeException;
use Kontrolgruppen\CoreBundle\CPR\CprException;
use Kontrolgruppen\CoreBundle\CPR\CprServiceInterface;
use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientCompany;
use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;
use Kontrolgruppen\CoreBundle\Form\ProcessClientCompanyType;
use Kontrolgruppen\CoreBundle\Form\ProcessClientPersonType;
use Kontrolgruppen\CoreBundle\Service\MenuService;
use Kontrolgruppen\CoreBundle\Service\ProcessClientManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/process/{process}/client")
 */
class ProcessClientController extends BaseController
{
    /**
     * @var ProcessClientManager
     */
    private $processClientManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ProcessClientController constructor.
     *
     * @param RequestStack        $requestStack
     * @param MenuService         $menuService
     * @param CprServiceInterface $processClientManager
     * @param LoggerInterface     $logger
     */
    public function __construct(RequestStack $requestStack, MenuService $menuService, ProcessClientManager $processClientManager, LoggerInterface $logger)
    {
        parent::__construct($requestStack, $menuService);
        $this->processClientManager = $processClientManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="client_show", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function show(Request $request, Process $process): Response
    {
        $client = $process->getProcessClient();

        if (null === $client) {
            throw new \RuntimeException('@todo create client');
        }

        $changeProcessStatusForm = $this->createChangeProcessStatusForm($process);
        $this->handleChangeProcessStatusForm($request, $changeProcessStatusForm);

        $newInfoAvailable = $this->isGranted('edit', $process) && $this->isNewClientInfoAvailable($client);

        $view = $this->getView($client, 'show');

        return $this->render($view, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'client' => $client,
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'changeProcessStatusForm' => $changeProcessStatusForm->createView(),
            'process' => $process,
            'newClientInfoAvailable' => $newInfoAvailable,
        ]);
    }

    /**
     * @Route("/edit", name="client_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Process $process
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function edit(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        // Redirect to show if process is completed.
        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        $client = $process->getProcessClient();

        $form = $this->createClientForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_show', [
                'process' => $process->getId(),
            ]);
        }

        $view = $this->getView($client, 'edit');

        return $this->render($view, [
            'menuItems' => $this->menuService->getProcessMenu($request->getPathInfo(), $process),
            'canEdit' => $this->isGranted('edit', $process) && null === $process->getCompletedAt(),
            'client' => $client,
            'form' => $form->createView(),
            'process' => $process,
        ]);
    }

    /**
     * @Route("/update", name="client_update", methods={"GET"})
     *
     * @param Request             $request
     * @param Process             $process
     * @param TranslatorInterface $translator
     *
     * @return Response
     */
    public function update(Request $request, Process $process, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $client = $process->getProcessClient();

        try {
            $client = $this->processClientManager->populateClient($client);
            $this->addFlash('success', $translator->trans('client.show.client_updated'));
        } catch (CprException $e) {
            $this->addFlash('danger', $translator->trans('client.show.client_not_updated'));
            $this->logger->error($e);
        }

        $this->getDoctrine()->getManager()->persist($client);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('client_show', ['process' => $process]);
    }

    /**
     * Check if new info is available for a client.
     *
     * @param AbstractProcessClient $client
     *
     * @return bool True if new info is available
     */
    private function isNewClientInfoAvailable(AbstractProcessClient $client): bool
    {
        try {
            return $this->processClientManager->isNewClientInfoAvailable($client);
        } catch (CprException $e) {
            $this->logger->error($e);
        }

        return false;
    }

    /**
     * Get view for a client action.
     *
     * @param AbstractProcessClient $client
     * @param string                $action
     *
     * @return string The view
     */
    private function getView(AbstractProcessClient $client, string $action): string
    {
        return '@KontrolgruppenCore/client/'.$client->getType().'/'.$action.'.html.twig';
    }

    /**
     * Create client form.
     *
     * @param AbstractProcessClient $client
     *
     * @return FormInterface The client form
     */
    private function createClientForm(AbstractProcessClient $client): FormInterface
    {
        if ($client instanceof ProcessClientCompany) {
            return $this->createForm(ProcessClientCompanyType::class, $client);
        }
        if ($client instanceof ProcessClientPerson) {
            return $this->createForm(ProcessClientPersonType::class, $client);
        }

        throw new RuntimeException(sprintf('Unknown client type: %s', \get_class($client)));
    }
}
