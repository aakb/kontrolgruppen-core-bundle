<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Kontrolgruppen\CoreBundle\DBAL\Types\ProcessLogEntryLevelEnumType;
use Kontrolgruppen\CoreBundle\Entity\JournalEntry;
use Kontrolgruppen\CoreBundle\Entity\LockedNetValue;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessLogEntry;
use Kontrolgruppen\CoreBundle\Filter\ProcessFilterType;
use Kontrolgruppen\CoreBundle\Form\ProcessCompleteType;
use Kontrolgruppen\CoreBundle\Form\ProcessType;
use Kontrolgruppen\CoreBundle\Repository\ProcessRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Kontrolgruppen\CoreBundle\Repository\UserRepository;
use Kontrolgruppen\CoreBundle\Service\LogManager;
use Kontrolgruppen\CoreBundle\Service\ProcessManager;
use Kontrolgruppen\CoreBundle\Service\UserSettingsService;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/process")
 */
class ProcessController extends BaseController
{
    /**
     * @Route("/", name="process_index", methods={"GET"})
     */
    public function index(
        Request $request,
        ProcessRepository $processRepository,
        FilterBuilderUpdaterInterface $lexikBuilderUpdater,
        PaginatorInterface $paginator,
        FormFactoryInterface $formFactory,
        ProcessManager $processManager,
        UserRepository $userRepository,
        UserSettingsService $userSettingsService
    ): Response {
        $filterForm = $formFactory->create(ProcessFilterType::class);

        $queryBuilder = null;

        $selectedCaseWorker = $filterForm->get('caseWorker');

        if (null === $selectedCaseWorker->getData()) {
            $filterForm->get('caseWorker')->setData($this->getUser()->getId());
        }

        if ($request->query->has($filterForm->getName())) {
            $formParameters = $request->query->get($filterForm->getName());

            if (!isset($formParameters['caseWorker'])) {
                $formParameters['caseWorker'] = $this->getUser()->getId();
            }

            // manually bind values from the request
            $filterForm->submit($formParameters);

            // initialize a query builder
            $queryBuilder = $processRepository->createQueryBuilder('e');

            // build the query from the given form object
            $lexikBuilderUpdater->addFilterConditions($filterForm, $queryBuilder);
        } else {
            $queryBuilder = $processRepository->createQueryBuilder('e');

            $queryBuilder->where('e.caseWorker = :caseWorker');
            $queryBuilder->setParameter(':caseWorker', $this->getUser());
        }

        // Add sortable fields.
        $queryBuilder->leftJoin('e.caseWorker', 'caseWorker');
        $queryBuilder->addSelect('partial caseWorker.{id}');

        $queryBuilder->leftJoin('e.channel', 'channel');
        $queryBuilder->addSelect('partial channel.{id,name}');

        $queryBuilder->leftJoin('e.reason', 'reason');
        $queryBuilder->addSelect('partial reason.{id,name}');

        $queryBuilder->leftJoin('e.service', 'service');
        $queryBuilder->addSelect('partial service.{id,name}');

        $queryBuilder->leftJoin('e.processType', 'processType');
        $queryBuilder->addSelect('partial processType.{id}');

        $queryBuilder->leftJoin('e.processStatus', 'processStatus');
        $queryBuilder->addSelect('partial processStatus.{id}');

        $formKey = 'process_index.'.$filterForm->getName();
        /* @var \Kontrolgruppen\CoreBundle\Entity\User $user */
        $user = $this->getUser();

        $paginatorOptions = [];

        // Get sort and direction from user settings.
        if (!$request->query->has('sort') || !$request->query->has('direction')) {
            $userSettings = $userSettingsService->getSettings($user, $formKey);

            $userSettingsValue = null !== $userSettings ? $userSettings->getSettingsValue() : null;
            $paginatorOptions = null === $userSettingsValue
                ? [
                    'defaultSortFieldName' => $userSettingsValue['sort'],
                    'defaultSortDirection' => $userSettingsValue['direction'],
                ]
                : [ // defaults
                    'defaultSortFieldName' => 'e.caseNumber',
                    'defaultSortDirection' => 'desc',
                ]
            ;
        } else {
            $userSettingsService->setSettings($user, $formKey, [
                'sort' => $request->query->get('sort'),
                'direction' => $request->query->get('direction'),
            ]);
        }

        $query = $queryBuilder->getQuery();

        // Get paginated result.
        $pagination = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            50,
            $paginatorOptions
        );

        // Find Processes that have not been visited by the assigned CaseWorker.
        $caseWorker = (!empty($selectedCaseWorker->getData()))
            ? $userRepository->find($selectedCaseWorker->getData())
            : $this->getUser();
        $foundEntries = array_column($query->getArrayResult(), 'id');
        $notVisitedProcessIds = $processManager->getUsersUnvisitedProcessIds($foundEntries, $caseWorker);

        return $this->render(
            '@KontrolgruppenCore/process/index.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo()
                ),
                'unvisitedProcessIds' => $notVisitedProcessIds,
                'pagination' => $pagination,
                'form' => $filterForm->createView(),
                'query' => $query,
            ]
        );
    }

    /**
     * @Route("/new", name="process_new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        ProcessManager $processManager
    ): Response {
        $process = new Process();

        $this->denyAccessUnlessGranted('edit', $process);

        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $process = $processManager->newProcess($process);

            return $this->redirectToRoute('client_show', ['process' => $process]);
        }

        // Get latest log entries
        $recentActivity = $this->getDoctrine()->getRepository(
            ProcessLogEntry::class
        )->getLatestEntriesByLevel(ProcessLogEntryLevelEnumType::NOTICE, 10);

        return $this->render(
            '@KontrolgruppenCore/process/new.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
                'recentActivity' => $recentActivity,
            ]
        );
    }

    /**
     * @Route("/{id}", name="process_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Process $process, LogManager $logManager): Response
    {
        // Latest journal entries.
        $latestJournalEntries = $this->getDoctrine()->getRepository(
            JournalEntry::class
        )->getLatestEntries($process);

        if ($this->isGranted('ROLE_ADMIN', $this->getUser())) {
            $latestJournalEntries = $logManager->attachLogEntriesToJournalEntries($latestJournalEntries);
        }

        return $this->render(
            '@KontrolgruppenCore/process/show.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'latestJournalEntries' => $latestJournalEntries,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="process_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt() && !$this->isGranted('ROLE_ADMIN')) {
            $this->redirectToRoute('process_show', ['id' => $process->getId()]);
        }

        $form = $this->createForm(ProcessType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToReferer(
                'process_show',
                [
                    'id' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/process/edit.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="process_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('delete', $process);

        if ($this->isCsrfTokenValid(
            'delete'.$process->getId(),
            $request->request->get('_token')
        )) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($process);
            $entityManager->flush();
        }

        return $this->redirectToRoute('process_index');
    }

    /**
     * @Route("/{id}/complete", name="process_complete", methods={"GET","POST"})
     */
    public function complete(Request $request, Process $process, ServiceRepository $serviceRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        if (null !== $process->getCompletedAt()) {
            return $this->redirectToRoute(
                'process_show',
                ['id' => $process->getId()]
            );
        }

        $form = $this->createForm(ProcessCompleteType::class, $process);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $services = $serviceRepository->getByProcess($process);
            foreach ($services as $service) {
                $lockedNetValue = new LockedNetValue();
                $lockedNetValue->setService($service);
                $lockedNetValue->setProcess($process);
                $lockedNetValue->setValue($service->getNetDefaultValue());

                $em->persist($lockedNetValue);
            }

            $process->setCompletedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($process);
            $em->flush();

            return $this->redirectToRoute(
                'process_show',
                [
                    'id' => $process->getId(),
                ]
            );
        }

        return $this->render(
            '@KontrolgruppenCore/process/complete.html.twig',
            [
                'menuItems' => $this->menuService->getProcessMenu(
                    $request->getPathInfo(),
                    $process
                ),
                'process' => $process,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}/resume", name="process_resume", methods={"POST"})
     */
    public function resume(Request $request, Process $process): Response
    {
        $this->denyAccessUnlessGranted('edit', $process);

        $process->setCompletedAt(null);
        $process->setLockedNetValue(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($process);
        $em->flush();

        return $this->redirectToRoute('process_show', ['id' => $process->getId()]);
    }
}
