<?php declare(strict_types = 1);

namespace App\Enums\Condition;

use MadWeb\Enum\Enum;

/**
 * @method static Type CONJUNCTIVE()
 * @method static Type ALTERNATIVE()
 */
final class Type extends Enum
{
    const __default = self::CONJUNCTIVE;

    protected const CONJUNCTIVE = 'conjunctive';
    protected const ALTERNATIVE = 'alternative';
}
