<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Test\Constraint;

if (!class_exists(\PHPUnit\Runner\Version::class)) {
    class_alias('PHPUnit_Runner_Version', \PHPUnit\Runner\Version::class);
}

if (version_compare(\PHPUnit\Runner\Version::id(), '7.0.0') < 0) {
    class_alias(SameStringsConstraintForV5::class, SameStringsConstraint::class);
} else {
    class_alias(SameStringsConstraintForV7::class, SameStringsConstraint::class);
}
