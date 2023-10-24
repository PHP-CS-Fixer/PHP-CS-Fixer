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

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\ReferenceSpacesFixer
 */
final class ReferenceSpacesFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'default configuration - assign - reference char with a single space after' => [
            '<?php $foo = &$var;',
            '<?php $foo =& $var;',
        ];

        yield 'default configuration - assign - reference char with a single space on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo = & $var;',
        ];

        yield 'default configuration - assign - reference char with a single space before' => [
            '<?php $foo = &$var;',
        ];

        yield 'default configuration - assign - reference char with a no spaces around' => [
            '<?php $foo = &$var;',
            '<?php $foo =&$var;',
        ];

        yield 'default configuration - assign - reference char with multiple spaces after' => [
            '<?php $foo = &$var;',
            '<?php $foo =&    $var;',
        ];

        yield 'default configuration - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &    $var;',
        ];

        yield 'default configuration - assign - reference char with multiple spaces before' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &$var;',
        ];

        yield 'default configuration - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {}',
        ];

        yield 'default configuration - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {}',
            '<?php function bar(& $foo) {}',
        ];

        yield 'default configuration - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {}',
            '<?php function bar(&     $foo) {}',
        ];

        yield 'default configuration - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
        ];

        yield 'default configuration - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
        ];

        yield 'default configuration - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
        ];

        // --- Configuration: assignment: by_assign --- //

        yield 'configured: assignment: by_assign - assign - reference char with a single space after' => [
            '<?php $foo =& $var;',
            null,
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - assign - reference char with a single space on both sides' => [
            '<?php $foo =& $var;',
            '<?php $foo = & $var;',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - assign - reference char with a single space before' => [
            '<?php $foo =& $var;',
            '<?php $foo = &$var;',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - assign - reference char with a no spaces around' => [
            '<?php $foo =& $var;',
            '<?php $foo =&$var;',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - assign - reference char with multiple spaces after' => [
            '<?php $foo =& $var;',
            '<?php $foo = &    $var;',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo =& $var;',
            '<?php $foo =    &    $var;',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - assign - reference char with multiple spaces before' => [
            '<?php $foo =& $var;',
            '<?php $foo =    &$var;',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['assignment' => 'by_assign'],
        ];

        yield 'configured: assignment: by_assign - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['assignment' => 'by_assign'],
        ];

        // Configuration: assignment: by_reference --- //

        yield 'configured: assignment: by_reference - assign - reference char with a single space after' => [
            '<?php $foo = &$var;',
            '<?php $foo =& $var;',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - assign - reference char with a single space on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo = & $var;',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - assign - reference char with a single space before' => [
            '<?php $foo = &$var;',
            null,
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - assign - reference char with a no spaces around' => [
            '<?php $foo = &$var;',
            '<?php $foo =&$var;',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - assign - reference char with multiple spaces after' => [
            '<?php $foo = &$var;',
            '<?php $foo = &    $var;',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &    $var;',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - assign - reference char with multiple spaces before' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &$var;',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['assignment' => 'by_reference'],
        ];

        yield 'configured: assignment: by_reference - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['assignment' => 'by_reference'],
        ];

        // Configuration: assignment: single_space --- //

        yield 'configured: assignment: single_space - assign - reference char with a single space after' => [
            '<?php $foo = & $var;',
            '<?php $foo =& $var;',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - assign - reference char with a single space on both sides' => [
            '<?php $foo = & $var;',
            null,
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - assign - reference char with a single space before' => [
            '<?php $foo = & $var;',
            '<?php $foo = &$var;',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - assign - reference char with a no spaces around' => [
            '<?php $foo = & $var;',
            '<?php $foo =&$var;',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - assign - reference char with multiple spaces after' => [
            '<?php $foo = & $var;',
            '<?php $foo = &    $var;',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = & $var;',
            '<?php $foo =    &    $var;',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - assign - reference char with multiple spaces before' => [
            '<?php $foo = & $var;',
            '<?php $foo =    &$var;',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['assignment' => 'single_space'],
        ];

        yield 'configured: assignment: single_space - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['assignment' => 'single_space'],
        ];

        // Configuration: assignment: no_space --- //

        yield 'configured: assignment: no_space - assign - reference char with a single space after' => [
            '<?php $foo =&$var;',
            '<?php $foo =& $var;',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - assign - reference char with a single space on both sides' => [
            '<?php $foo =&$var;',
            '<?php $foo = & $var;',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - assign - reference char with a single space before' => [
            '<?php $foo =&$var;',
            '<?php $foo = &$var;',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - assign - reference char with a no spaces around' => [
            '<?php $foo =&$var;',
            null,
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - assign - reference char with multiple spaces after' => [
            '<?php $foo =&$var;',
            '<?php $foo = &    $var;',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo =&$var;',
            '<?php $foo =    &    $var;',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - assign - reference char with multiple spaces before' => [
            '<?php $foo =&$var;',
            '<?php $foo =    &$var;',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['assignment' => 'no_space'],
        ];

        yield 'configured: assignment: no_space - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['assignment' => 'no_space'],
        ];

        // Configuration: function_signature: by_reference --- //

        yield 'configured: function_signature: by_reference - assign - reference char with a single space after' => [
            '<?php $foo = &$var;',
            '<?php $foo =& $var;',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - assign - reference char with a single space on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo = & $var;',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - assign - reference char with a single space before' => [
            '<?php $foo = &$var;',
            null,
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - assign - reference char with a no spaces around' => [
            '<?php $foo = &$var;',
            '<?php $foo =&$var;',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - assign - reference char with multiple spaces after' => [
            '<?php $foo = &$var;',
            '<?php $foo = &    $var;',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &    $var;',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - assign - reference char with multiple spaces before' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &$var;',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['function_signature' => 'by_reference'],
        ];

        yield 'configured: function_signature: by_reference - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['function_signature' => 'by_reference'],
        ];

        // Configuration: function_signature: single_space --- //

        yield 'configured: function_signature: single_space - assign - reference char with a single space after' => [
            '<?php $foo = &$var;',
            '<?php $foo =& $var;',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - assign - reference char with a single space on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo = & $var;',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - assign - reference char with a single space before' => [
            '<?php $foo = &$var;',
            null,
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - assign - reference char with a no spaces around' => [
            '<?php $foo = &$var;',
            '<?php $foo =&$var;',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - assign - reference char with multiple spaces after' => [
            '<?php $foo = &$var;',
            '<?php $foo = &    $var;',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &    $var;',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - assign - reference char with multiple spaces before' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &$var;',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - function_signature - reference char with no space after' => [
            '<?php function bar(& $foo) {};',
            '<?php function bar(&$foo) {};',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - function_signature - reference char with a single space after' => [
            '<?php function bar(& $foo) {};',
            null,
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(& $foo) {};',
            '<?php function bar(&     $foo) {};',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['function_signature' => 'single_space'],
        ];

        yield 'configured: function_signature: single_space - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['function_signature' => 'single_space'],
        ];

        // Configuration: anonymous_function_use_block: by_reference --- //

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with a single space after' => [
            '<?php $foo = &$var;',
            '<?php $foo =& $var;',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with a single space on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo = & $var;',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with a single space before' => [
            '<?php $foo = &$var;',
            null,
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with a no spaces around' => [
            '<?php $foo = &$var;',
            '<?php $foo =&$var;',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with multiple spaces after' => [
            '<?php $foo = &$var;',
            '<?php $foo = &    $var;',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &    $var;',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - assign - reference char with multiple spaces before' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &$var;',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (&$bar) {};',
            null,
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (& $bar) {};',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        yield 'configured: anonymous_function_use_block: by_reference - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (&$bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['anonymous_function_use_block' => 'by_reference'],
        ];

        // Configuration: anonymous_function_use_block: single_space --- //

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with a single space after' => [
            '<?php $foo = &$var;',
            '<?php $foo =& $var;',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with a single space on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo = & $var;',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with a single space before' => [
            '<?php $foo = &$var;',
            null,
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with a no spaces around' => [
            '<?php $foo = &$var;',
            '<?php $foo =&$var;',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with multiple spaces after' => [
            '<?php $foo = &$var;',
            '<?php $foo = &    $var;',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with multiple spaces on both sides' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &    $var;',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - assign - reference char with multiple spaces before' => [
            '<?php $foo = &$var;',
            '<?php $foo =    &$var;',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - function_signature - reference char with no space after' => [
            '<?php function bar(&$foo) {};',
            null,
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - function_signature - reference char with a single space after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(& $foo) {};',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - function_signature - reference char with multiple spaces after' => [
            '<?php function bar(&$foo) {};',
            '<?php function bar(&     $foo) {};',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - anonymous_function_use_block - reference char with no space after' => [
            '<?php $foo = function() use (& $bar) {};',
            '<?php $foo = function() use (&$bar) {};',
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - anonymous_function_use_block - reference char a single multiple spaces after' => [
            '<?php $foo = function() use (& $bar) {};',
            null,
            ['anonymous_function_use_block' => 'single_space'],
        ];

        yield 'configured: anonymous_function_use_block: single_space - anonymous_function_use_block - reference char with multiple spaces after' => [
            '<?php $foo = function() use (& $bar) {};',
            '<?php $foo = function() use (&    $bar) {};',
            ['anonymous_function_use_block' => 'single_space'],
        ];
    }
}
