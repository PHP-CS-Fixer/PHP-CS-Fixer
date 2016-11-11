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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 *
 * @internal
 */
final class RandomApiMigrationFixerTest extends AbstractFixerTestCase
{
    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[random_api_migration\] "is_null" is not handled by the fixer.$#
     */
    public function testConfigureCheckSearchFunction()
    {
        $this->fixer->configure(array('is_null' => 'random_int'));
    }

    /**
     * @expectedException \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException
     * @expectedExceptionMessageRegExp #^\[random_api_migration\] Expected string got "NULL".$#
     */
    public function testConfigureCheckReplacementType()
    {
        $this->fixer->configure(array('rand' => null));
    }

    public function testConfigure()
    {
        $config = array('rand' => 'random_int');
        $this->fixer->configure($config);

        /** @var $replacements string[] */
        $replacements = static::getObjectAttribute($this->fixer, 'configuration');
        static::assertSame(
            array('rand' => array('alternativeName' => 'random_int', 'argumentCount' => array(0, 2))),
            $replacements
        );
    }

    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null, array $config = null)
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return array[]
     */
    public function provideCases()
    {
        return array(
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
                array('rand' => 'random_int'),
            ),
            array(
                '<?php random_int($d, random_int($a,$b));',
                '<?php rand($d, rand($a,$b));',
                array('rand' => 'random_int'),
            ),
            array(
                '<?php random_int($a, \Other\Scope\mt_rand($a));',
                '<?php rand($a, \Other\Scope\mt_rand($a));',
                array('rand' => 'random_int'),
            ),
            array(
                '<?php $a = random_int(1,2) + random_int(3,4);',
                '<?php $a = rand(1,2) + mt_rand(3,4);',
                array('rand' => 'random_int', 'mt_rand' => 'random_int'),
            ),
        );
    }
}
