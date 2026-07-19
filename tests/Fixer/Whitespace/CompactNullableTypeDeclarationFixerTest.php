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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Fixer\Whitespace\CompactNullableTypeDeclarationFixer;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\CompactNullableTypeDeclarationFixer
 *
 * @extends AbstractNullableTypeDeclarationFixerTestCase<\PhpCsFixer\Fixer\Whitespace\CompactNullableTypeDeclarationFixer>
 *
 * @author Jack Cherng <jfcherng@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(CompactNullableTypeDeclarationFixer::class)]
final class CompactNullableTypeDeclarationFixerTest extends AbstractNullableTypeDeclarationFixerTestCase {}
