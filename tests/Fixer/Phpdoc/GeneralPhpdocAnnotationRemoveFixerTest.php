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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @author Gert de Pagter
 * @covers \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer
 */
final class GeneralPhpdocAnnotationRemoveFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     *
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            'An Annotation gets removed' => [
                '<?php
/**
 * @internal
 */
function hello($name)
{
    return "hello " . $name;
}',
                '<?php
/**
 * @internal
 * @param string $name
 */
function hello($name)
{
    return "hello " . $name;
}',
                ['annotations' => ['param']],
            ],
            'It removes multiple annotations' => [
                '<?php
/**
 * @author me
 * @internal
 */
function hello($name)
{
    return "hello " . $name;
}',
                '<?php
/**
 * @author me
 * @internal
 * @param string $name
 * @return string
 * @throws \Exception
 */
function hello($name)
{
    return "hello " . $name;
}',
                ['annotations' => ['param', 'return', 'throws']],
            ],
            'It does nothing if no configuration is given' => [
                '<?php
/**
 * @author me
 * @internal
 * @param string $name
 * @return string
 * @throws \Exception
 */
function hello($name)
{
    return "hello " . $name;
}',
            ],
            'It works on multiple functions' => [
                '<?php
/**
 * @param string $name
 * @throws \Exception
 */
function hello($name)
{
    return "hello " . $name;
}
/**
 */
function goodBye()
{
    return 0;
}
function noComment()
{
    callOtherFunction();
}',
                '<?php
/**
 * @author me
 * @internal
 * @param string $name
 * @return string
 * @throws \Exception
 */
function hello($name)
{
    return "hello " . $name;
}
/**
 * @internal
 * @author Piet-Henk
 * @return int
 */
function goodBye()
{
    return 0;
}
function noComment()
{
    callOtherFunction();
}',
                ['annotations' => ['author', 'return', 'internal']],
            ],
            'Nothing happens to non doc-block comments' => [
                '<?php
/*
 * @internal
 * @param string $name
 */
function hello($name)
{
    return "hello " . $name;
}',
                null,
                ['annotations' => ['internal', 'param', 'return']],
            ],
            'Nothing happens if to be deleted annotations are not present' => [
                '<?php
/**
 * @internal
 * @param string $name
 */
function hello($name)
{
    return "hello " . $name;
}',
                null,
                ['annotations' => ['author', 'test', 'return', 'deprecated']],
            ],
        ];
    }
}
