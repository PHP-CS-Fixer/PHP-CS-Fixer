<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\RuleSet\Sets;

use PhpCsFixer\RuleSet\Sets\PERCS2x0Set;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @covers \PhpCsFixer\RuleSet\Sets\PERCS2x0Set
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(PERCS2x0Set::class)]
final class PERCS2x0SetTest extends AbstractSetTestCase {}
