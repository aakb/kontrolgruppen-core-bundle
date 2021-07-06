<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Kontrolgruppen\CoreBundle\Entity\AbstractTaxonomy;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ValidProcessStatusClientValidator.
 */
class ValidClientTaxonomyValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ValidClientTaxonomyValidator constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var \Kontrolgruppen\CoreBundle\Validator\ValidClientTaxonomy $constraint */

        $entity = $this->context->getObject();

        if ($entity instanceof AbstractTaxonomy) {
            $allowedClientTypes = $entity->getClientTypes();
            if (empty($allowedClientTypes)) {
                return;
            }

            $allowedClientTypesDisplay = implode(', ', array_map(function (string $clientType) {
                return $this->translator->trans('process_client_type.'.$clientType);
            }, $allowedClientTypes));

            if (!$value instanceof Collection) {
                $value = new ArrayCollection([$value]);
            }

            foreach ($value as $item) {
                if ($item instanceof AbstractTaxonomy) {
                    if (!empty(array_diff($item->getClientTypes(), $allowedClientTypes))) {
                        $this->context->buildViolation($constraint->message)
                            ->setParameter('{{ value }}', $item->getName())
                            ->setParameter('{{ client_types }}', $allowedClientTypesDisplay)
                            ->addViolation();
                    }
                }
            }
        }
    }
}
