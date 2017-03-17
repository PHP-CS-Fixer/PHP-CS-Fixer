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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class PhpUnitConstructFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @group legacy
     * @dataProvider provideTestFixCases
     * @expectedDeprecation Passing "assertions" at the root of the configuration is deprecated and will not be supported in 3.0, use "assertions" => array(...) option instead.
     */
    public function testLegacyFix($expected, $input = null)
    {
        $this->fixer->configure(array(
            'assertEquals',
            'assertSame',
            'assertNotEquals',
            'assertNotSame',
        ));
        $this->doTest($expected, $input);

        foreach (array('assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame') as $method) {
            $this->fixer->configure(array($method));
            $this->doTest(
                $expected,
                $input && false !== strpos($input, $method) ? $input : null
            );
        }
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->fixer->configure(array('assertions' => array(
            'assertEquals',
            'assertSame',
            'assertNotEquals',
            'assertNotSame',
        )));
        $this->doTest($expected, $input);

        foreach (array('assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame') as $method) {
            $this->fixer->configure(array('assertions' => array($method)));
            $this->doTest(
                $expected,
                $input && false !== strpos($input, $method) ? $input : null
            );
        }
    }

    public function provideTestFixCases()
    {
        $cases = array(
            array('<?php $sth->assertSame(true, $foo);'),
            array('<?php $this->assertSame($b, null);'),
            array(
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
            ),
            array(
                '<?php $this->assertNull(/*bar*/ $a);',
                '<?php $this->assertSame(null /*foo*/, /*bar*/ $a);',
            ),
            array(
                '<?php $this->assertSame(null === $eventException ? $exception : $eventException, $event->getException());',
            ),
            array(
                '<?php $this->assertSame(null /*comment*/ === $eventException ? $exception : $eventException, $event->getException());',
            ),
        );

        return array_merge(
            $cases,
            $this->generateCases('<?php $this->assert%s%s($a); //%s %s', '<?php $this->assert%s(%s, $a); //%s %s'),
            $this->generateCases('<?php $this->assert%s%s($a, "%s", "%s");', '<?php $this->assert%s(%s, $a, "%s", "%s");')
        );
    }

    public function testInvalidConfig()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '/^\[php_unit_construct\] Invalid configuration: The option "assertions" .*\.$/'
        );

        $this->fixer->configure(array('assertions' => array('__TEST__')));
    }

    private function generateCases($expectedTemplate, $inputTemplate)
    {
        $cases = array();
        $functionTypes = array('Same' => true, 'NotSame' => false, 'Equals' => true, 'NotEquals' => false);
        foreach (array('true', 'false', 'null') as $type) {
            foreach ($functionTypes as $method => $positive) {
                $cases[] = array(
                    sprintf($expectedTemplate, $positive ? '' : 'Not', ucfirst($type), $method, $type),
                    sprintf($inputTemplate, $method, $type, $method, $type),
                );
            }
        }

        return $cases;
    }
}
