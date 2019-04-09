<?php

namespace Kontrolgruppen\CoreBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class JournalEntryEnumType extends AbstractEnumType
{
    public const NOTE = 'NOTE';
    public const INTERNAL_NOTE = 'INTERNAL_NOTE';
    public const DIARY = 'DIARY';

    protected static $choices = [
        self::NOTE => 'common.enum.journal_entry.note',
        self::INTERNAL_NOTE => 'common.enum.journal_entry.internal_note',
        self::DIARY => 'common.enum.journal_entry.diary',
    ];
}
