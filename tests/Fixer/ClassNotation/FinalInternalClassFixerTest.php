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

namespace PhpCsFixer\Tests\Fixer\ClassNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer
 */
final class FinalInternalClassFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected PHP source code
     * @param null|string $input    PHP source code
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $input = $expected = '<?php ';
        for ($i = 1; $i < 10; ++$i) {
            $input .= sprintf("/** @internal */\nclass class%d\n{\n}\n", $i);
            $expected .= sprintf("/** @internal */\nfinal class class%d\n{\n}\n", $i);
        }

        return [
            [
                $expected,
                $input,
            ],
            [
                '<?php

/** @internal */
final class class1
{
}

interface A {}
trait B{}

/** @internal */
final class class2
{
}
',
                '<?php

/** @internal */
class class1
{
}

interface A {}
trait B{}

/** @internal */
class class2
{
}
',
            ],
            [
                '<?php
/** @internal */
final class class1
{
}

/** @internal */
final class class2
{
}

/**
 * @internal
 * @final
 */
class class3
{
}

/**
 * @internal
 */
abstract class class4 {}
',
                '<?php
/** @internal */
final class class1
{
}

/** @internal */
class class2
{
}

/**
 * @internal
 * @final
 */
class class3
{
}

/**
 * @internal
 */
abstract class class4 {}
',
            ],
        ];
    }

    /**
     * @param string $expected PHP source code
     * @param string $input    PHP source code
     * @param array  $config   Fixer configuration
     *
     * @dataProvider provideFixWithConfigCases
     */
    public function testFixWithConfig($expected, $input, array $config)
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFixWithConfigCases()
    {
        return [
            [
                "<?php\n/** @CUSTOM */final class A{}",
                "<?php\n/** @CUSTOM */class A{}",
                [
                    'annotation-white-list' => ['@Custom'],
                ],
            ],
            [
                '<?php
/**
 * @CUSTOM
 * @abc
 */
final class A{}

/**
 * @CUSTOM
 */
class B{}
',
                '<?php
/**
 * @CUSTOM
 * @abc
 */
class A{}

/**
 * @CUSTOM
 */
class B{}
',
                [
                    'annotation-white-list' => ['@Custom', '@abc'],
                ],
            ],
            [
                '<?php
/**
 * @CUSTOM
 * @internal
 */
 final class A{}

/**
 * @CUSTOM
 * @internal
 * @other
 */
 final class B{}

/**
 * @CUSTOM
 * @internal
 * @not-fix
 */
 class C{}
',
                '<?php
/**
 * @CUSTOM
 * @internal
 */
 class A{}

/**
 * @CUSTOM
 * @internal
 * @other
 */
 class B{}

/**
 * @CUSTOM
 * @internal
 * @not-fix
 */
 class C{}
',
                [
                    'annotation-white-list' => ['@Custom', '@internal'],
                    'annotation-black-list' => ['@not-fix'],
                ],
            ],
            [
                '<?php
/**
 * @internal
 */
final class A{}

/**
 * @abc
 */
class B{}
',
                '<?php
/**
 * @internal
 */
class A{}

/**
 * @abc
 */
class B{}
',
                [
                    'annotation-black-list' => ['@abc'],
                ],
            ],
        ];
    }

    /**
     * @param string      $expected PHP source code
     * @param null|string $input    PHP source code
     *
     * @requires PHP 7.0
     * @dataProvider provideAnonymousClassesCases
     */
    public function testAnonymousClassesCases($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideAnonymousClassesCases()
    {
        return [
            [
                '<?php
/** @internal */
$a = new class (){};',
            ],
        ];
    }
}
