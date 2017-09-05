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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer
 */
final class FunctionToConstantFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     * @param null|array  $config
     *
     * @dataProvider provideTestCases
     */
    public function testFix($expected, $input = null, array $config = null)
    {
        if ($config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    public function provideTestCases()
    {
        return array(
            'Minimal case, alternative casing, alternative statement end.' => array(
                '<?php echo PHP_VERSION?>',
                '<?php echo PHPversion()?>',
            ),
            'With embedded comment.' => array(
                '<?php echo PHP_VERSION/**/?>',
                '<?php echo phpversion(/**/)?>',
            ),
            'With white space.' => array(
                '<?php echo PHP_VERSION      ;',
                '<?php echo phpversion  (  )  ;',
            ),
            'With multi line whitespace.' => array(
                '<?php echo
                PHP_VERSION
                '.'
                '.'
                ;',
                '<?php echo
                phpversion
                (
                )
                ;',
            ),
            'Global namespaced.' => array(
                '<?php echo \PHP_VERSION;',
                '<?php echo \phpversion();',
            ),
            'Wrong number of arguments.' => array(
                '<?php phpversion($a);',
            ),
            'Wrong namespace.' => array(
                '<?php A\B\phpversion();',
            ),
            'Class creating.' => array(
                '<?php new phpversion();',
            ),
            'Class static method call.' => array(
                '<?php A::phpversion();',
            ),
            'Class method call.' => array(
                '<?php $a->phpversion();',
            ),
            'Overridden function.' => array(
                '<?php if (!function_exists("phpversion")){function phpversion(){}}?>',
            ),
            'phpversion only' => array(
                '<?php echo PHP_VERSION; echo php_sapi_name(); echo pi();',
                '<?php echo phpversion(); echo php_sapi_name(); echo pi();',
                array('functions' => array('phpversion')),
            ),
            'php_sapi_name only' => array(
                '<?php echo phpversion(); echo PHP_SAPI; echo pi();',
                '<?php echo phpversion(); echo php_sapi_name(); echo pi();',
                array('functions' => array('php_sapi_name')),
            ),
            'php_sapi_name in conditional' => array(
                '<?php if ("cli" === PHP_SAPI && $a){ echo 123;}',
                '<?php if ("cli" === php_sapi_name() && $a){ echo 123;}',
                array('functions' => array('php_sapi_name')),
            ),
            'pi only' => array(
                '<?php echo phpversion(); echo php_sapi_name(); echo M_PI;',
                '<?php echo phpversion(); echo php_sapi_name(); echo pi();',
                array('functions' => array('pi')),
            ),
            'multi line pi' => array(
                '<?php
$a =
    $b
    || $c < M_PI
;',
                '<?php
$a =
    $b
    || $c < pi()
;',
                array('functions' => array('pi')),
            ),
            'phpversion and pi' => array(
                '<?php echo PHP_VERSION; echo php_sapi_name(); echo M_PI;',
                '<?php echo phpversion(); echo php_sapi_name(); echo M_PI;',
                array('functions' => array('pi', 'phpversion')),
            ),
            'diff argument count than native allows' => array(
                '<?php
                    echo phpversion(1);
                    echo php_sapi_name(1,2);
                    echo pi(1);
                ',
            ),
        );
    }

    /**
     * @param array $config
     *
     * @dataProvider provideInvalidConfigurationKeysCases
     */
    public function testInvalidConfigurationKeys(array $config)
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[function_to_constant\] Invalid configuration: The option "functions" with value array is invalid\.$#'
        );

        $this->fixer->configure($config);
    }

    public function provideInvalidConfigurationKeysCases()
    {
        return array(
            array(array('functions' => array('a'))),
            array(array('functions' => array(false => 1))),
            array(array('functions' => array('abc' => true))),
        );
    }

    public function testInvalidConfigurationValue()
    {
        $this->setExpectedExceptionRegExp(
            'PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException',
            '#^\[function_to_constant\] Invalid configuration: The option "0" does not exist\. (Defined|Known) options are: "functions"\.$#'
        );

        $this->fixer->configure(array('pi123'));
    }
}
