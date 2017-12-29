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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer
 */
final class PhpUnitConstructFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->fixer->configure(['assertions' => [
            'assertEquals',
            'assertSame',
            'assertNotEquals',
            'assertNotSame',
        ]]);
        $this->doTest($expected, $input);

        foreach (['assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame'] as $method) {
            $this->fixer->configure(['assertions' => [$method]]);
            $this->doTest(
                $expected,
                $input && false !== strpos($input, $method) ? $input : null
            );
        }
    }

    public function provideTestFixCases()
    {
        $cases = [
            ['<?php $sth->assertSame(true, $foo);'],
            ['<?php $this->assertSame($b, null);'],
            [
                '<?php $this->assertNull(/*bar*/ $a);',
                '<?php $this->assertSame(null /*foo*/, /*bar*/ $a);',
            ],
            [
                '<?php $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException());',
            ],
            [
                '<?php $this->assertSame(null /*comment*/ === $eventException ? $exception : $eventException, $event->getException());',
            ],
            [
                '<?php
    $this->assertTrue(
        $a,
        "foo" . $bar
    );',
                '<?php
    $this->assertSame(
        true,
        $a,
        "foo" . $bar
    );',
            ],
            [
                '<?php
    $this->assertTrue(#
        #
        $a,#
        "foo" . $bar#
    );',
                '<?php
    $this->assertSame(#
        true,#
        $a,#
        "foo" . $bar#
    );',
            ],
            [
                '<?php $this->assertSame("a", $a); $this->assertTrue($b);',
                '<?php $this->assertSame("a", $a); $this->assertSame(true, $b);',
            ],
            [
                '<?php $this->assertSame(true || $a, $b); $this->assertTrue($c);',
                '<?php $this->assertSame(true || $a, $b); $this->assertSame(true, $c);',
            ],
        ];

        return array_merge(
            $cases,
            $this->generateCases('<?php $this->assert%s%s($a); //%s %s', '<?php $this->assert%s(%s, $a); //%s %s'),
            $this->generateCases('<?php $this->assert%s%s($a, "%s", "%s");', '<?php $this->assert%s(%s, $a, "%s", "%s");'),
            $this->generateCases('<?php static::assert%s%s($a); //%s %s', '<?php static::assert%s(%s, $a); //%s %s'),
            $this->generateCases('<?php self::assert%s%s($a); //%s %s', '<?php self::assert%s(%s, $a); //%s %s')
        );
    }

    public function testInvalidConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[php_unit_construct\] Invalid configuration: The option "assertions" .*\.$/');

        $this->fixer->configure(['assertions' => ['__TEST__']]);
    }

    private function generateCases($expectedTemplate, $inputTemplate)
    {
        $cases = [];
        $functionTypes = ['Same' => true, 'NotSame' => false, 'Equals' => true, 'NotEquals' => false];
        foreach (['true', 'false', 'null'] as $type) {
            foreach ($functionTypes as $method => $positive) {
                $cases[] = [
                    sprintf($expectedTemplate, $positive ? '' : 'Not', ucfirst($type), $method, $type),
                    sprintf($inputTemplate, $method, $type, $method, $type),
                ];
            }
        }

        return $cases;
    }
}
