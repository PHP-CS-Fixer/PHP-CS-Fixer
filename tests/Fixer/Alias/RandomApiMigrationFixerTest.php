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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractFunctionReferenceFixer
 * @covers \PhpCsFixer\Fixer\Alias\RandomApiMigrationFixer
 */
final class RandomApiMigrationFixerTest extends AbstractFixerTestCase
{
    public function testConfigureCheckSearchFunction()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[random_api_migration\] Invalid configuration: Function "is_null" is not handled by the fixer\.$#'
        );

        $this->fixer->configure(array('replacements' => array('is_null' => 'random_int')));
    }

    public function testConfigureCheckReplacementType()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[random_api_migration\] Invalid configuration: Replacement for function "rand" must be a string, "NULL" given\.$#'
        );

        $this->fixer->configure(array('replacements' => array('rand' => null)));
    }

    /**
     * @group legacy
     * @expectedDeprecation Passing "replacements" at the root of the configuration is deprecated and will not be supported in 3.0, use "replacements" => array(...) option instead.
     */
    public function testLegacyConfigure()
    {
        $this->fixer->configure(array('rand' => 'random_int'));

        static::assertSame(
            array('replacements' => array(
                'rand' => array('alternativeName' => 'random_int', 'argumentCount' => array(0, 2)), ),
            ),
            static::getObjectAttribute($this->fixer, 'configuration')
        );
    }

    public function testConfigure()
    {
        $this->fixer->configure(array('replacements' => array('rand' => 'random_int')));

        static::assertSame(
            array('replacements' => array(
                'rand' => array('alternativeName' => 'random_int', 'argumentCount' => array(0, 2)), ),
            ),
            static::getObjectAttribute($this->fixer, 'configuration')
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     * @param array       $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = array())
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return array[]
     */
    public function provideFixCases()
    {
        return array(
            array(
                '<?php random_int(0, getrandmax());',
                '<?php rand();',
                array('replacements' => array('rand' => 'random_int')),
            ),
            array(
                '<?php random_int#1
                #2
                (0, getrandmax()#3
                #4
                )#5
                ;',
                '<?php rand#1
                #2
                (#3
                #4
                )#5
                ;',
                array('replacements' => array('rand' => 'random_int')),
            ),
            array('<?php $smth->srand($a);'),
            array('<?php srandSmth($a);'),
            array('<?php smth_srand($a);'),
            array('<?php new srand($a);'),
            array('<?php new Smth\\srand($a);'),
            array('<?php Smth\\srand($a);'),
            array('<?php namespace\\srand($a);'),
            array('<?php Smth::srand($a);'),
            array('<?php new srand\\smth($a);'),
            array('<?php srand::smth($a);'),
            array('<?php srand\\smth($a);'),
            array('<?php "SELECT ... srand(\$a) ...";'),
            array('<?php "SELECT ... SRAND($a) ...";'),
            array("<?php 'test'.'srand' . 'in concatenation';"),
            array('<?php "test" . "srand"."in concatenation";'),
            array(
                '<?php
class SrandClass
{
    const srand = 1;
    public function srand($srand)
    {
        if (!defined("srand") || $srand instanceof srand) {
            echo srand;
        }
    }
}

class srand extends SrandClass{
    const srand = "srand";
}
', ),
            array('<?php mt_srand($a);', '<?php srand($a);'),
            array('<?php \\mt_srand($a);', '<?php \\srand($a);'),
            array('<?php $a = &mt_srand($a);', '<?php $a = &srand($a);'),
            array('<?php $a = &\\mt_srand($a);', '<?php $a = &\\srand($a);'),
            array('<?php /* foo */ mt_srand /** bar */ ($a);', '<?php /* foo */ srand /** bar */ ($a);'),
            array('<?php a(mt_getrandmax ());', '<?php a(getrandmax ());'),
            array('<?php a(mt_rand());', '<?php a(rand());'),
            array('<?php a(mt_srand());', '<?php a(srand());'),
            array('<?php a(\\mt_srand());', '<?php a(\\srand());'),
            array(
                '<?php rand(rand($a));',
                null,
                array('replacements' => array('rand' => 'random_int')),
            ),
            array(
                '<?php random_int($d, random_int($a,$b));',
                '<?php rand($d, rand($a,$b));',
                array('replacements' => array('rand' => 'random_int')),
            ),
            array(
                '<?php random_int($a, \Other\Scope\mt_rand($a));',
                '<?php rand($a, \Other\Scope\mt_rand($a));',
                array('replacements' => array('rand' => 'random_int')),
            ),
            array(
                '<?php $a = random_int(1,2) + random_int(3,4);',
                '<?php $a = rand(1,2) + mt_rand(3,4);',
                array('replacements' => array('rand' => 'random_int', 'mt_rand' => 'random_int')),
            ),
            array(
                '<?php
                interface Test
                {
                    public function getrandmax();
                    public function &rand();
                }',
                null,
                array('replacements' => array('rand' => 'random_int')),
            ),
        );
    }
}
