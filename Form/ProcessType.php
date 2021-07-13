<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Form;

use Kontrolgruppen\CoreBundle\Entity\AbstractProcessClient;
use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\ProcessType as ProcessTypeEntity;
use Kontrolgruppen\CoreBundle\Form\Process\ClientCompanyType;
use Kontrolgruppen\CoreBundle\Form\Process\ClientPersonType;
use Kontrolgruppen\CoreBundle\Repository\ChannelRepository;
use Kontrolgruppen\CoreBundle\Repository\ProcessTypeRepository;
use Kontrolgruppen\CoreBundle\Repository\ReasonRepository;
use Kontrolgruppen\CoreBundle\Repository\ServiceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ProcessType.
 */
class ProcessType extends AbstractType
{
    protected $processTypeRepository;
    protected $reasonRepository;
    protected $serviceRepository;
    protected $channelRepository;
    protected $router;
    protected $translator;

    /**
     * ProcessType constructor.
     *
     * @param ProcessTypeRepository $processTypeRepository
     * @param ReasonRepository      $reasonRepository
     * @param ServiceRepository     $serviceRepository
     * @param ChannelRepository     $channelRepository
     * @param RouterInterface       $router
     * @param TranslatorInterface   $translator
     */
    public function __construct(ProcessTypeRepository $processTypeRepository, ReasonRepository $reasonRepository, ServiceRepository $serviceRepository, ChannelRepository $channelRepository, RouterInterface $router, TranslatorInterface $translator)
    {
        $this->processTypeRepository = $processTypeRepository;
        $this->reasonRepository = $reasonRepository;
        $this->serviceRepository = $serviceRepository;
        $this->channelRepository = $channelRepository;
        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Process $process */
        $process = $builder->getData();
        // Add client controls on new processes.
        if (null !== $process && null === $process->getId()) {
            $client = $process->getProcessClient();
            if (null !== $client && null === $client->getId()) {
                switch ($client->getType()) {
                    case AbstractProcessClient::COMPANY:
                        $builder
                            ->add('company', ClientCompanyType::class, [
                                'label' => false,
                                'mapped' => false,
                            ]);
                        break;

                    case AbstractProcessClient::PERSON:
                        $builder
                            ->add('person', ClientPersonType::class, [
                                'label' => false,
                                'mapped' => false,
                            ]);
                        break;
                }
            }
        }

        $builder
            ->add('caseWorker', null, [
                'label' => 'process.form.case_worker',
            ])
            // Add placeholders which are replaced and filled in form events.
            ->add('processType', null, [
                'choices' => [],
                'label' => 'process.form.process_type',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('reason', null, [
                'choices' => [],
                'label' => 'process.form.reason',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('service', null, [
                'choices' => [],
                'label' => 'process.form.service',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ])
            ->add('channel', null, [
                'choices' => [],
                'label' => 'process.form.channel',
                'attr' => [
                    'disabled' => 'disabled',
                ],
            ]);

        $formModifier = function (FormInterface $form, Process $process, ProcessTypeEntity $processType = null) {
            if (null === $processType) {
                $processType = $process->getProcessType();
            }

            $choices = $this->processTypeRepository->findByProcess($process);

            if (!empty($choices)) {
                $form->add('processType', ChoiceType::class, [
                    'label' => 'process.form.process_type',
                    'choices' => $choices,
                    'choice_label' => 'name',
                ]);
            }

            $choices = $this->reasonRepository->findByProcess($process);

            if (!empty($choices)) {
                $form->add('reason', ChoiceType::class, [
                    'label' => 'process.form.reason',
                    'choices' => $choices,
                    'choice_label' => 'name',
                ]);
            }

            if (null !== $processType) {
                $choices = $this->serviceRepository->findByProcessType($process, $processType);

                if (!empty($choices)) {
                    $form->add('service', ChoiceType::class, [
                        'label' => 'process.form.service',
                        'choices' => $choices,
                        'choice_label' => 'name',
                    ]);
                }

                $choices = $this->channelRepository->findByProcessType($process, $processType);

                if (!empty($choices)) {
                    $form->add('channel', ChoiceType::class, [
                        'label' => 'process.form.channel',
                        'choices' => $choices,
                        'choice_label' => 'name',
                    ]);
                }
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                /** @var Process $process */
                $process = $event->getData();
                $formModifier($event->getForm(), $process, $process->getProcessType());
            }
        );

        $builder->get('processType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $processType = $event->getForm()->getData();
                $process = $event->getForm()->getParent()->getData();
                $formModifier($event->getForm()->getParent(), $process, $processType);
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
        ]);
    }
}
