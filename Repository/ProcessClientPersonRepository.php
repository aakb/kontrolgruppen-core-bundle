<?php

/*
 * This file is part of aakb/kontrolgruppen-core-bundle.
 *
 * (c) 2019 ITK Development
 *
 * This source file is subject to the MIT license.
 */

namespace Kontrolgruppen\CoreBundle\Repository;

use Kontrolgruppen\CoreBundle\Entity\ProcessClientPerson;

/**
 * @method ProcessClientPerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessClientPerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessClientPerson[]    findAll()
 * @method ProcessClientPerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessClientPersonRepository extends AbstractProcessClientRepository
{
    protected static $class = ProcessClientPerson::class;
}
