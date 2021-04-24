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

use PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Jules Pietri <jules@heahprod.com>
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer
 */
final class ErrorSuppressionFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        $tests = [
            [
                '<?php trigger_error("This is not a deprecation warning."); ?>',
            ],
            [
                '<?php trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
            ],
            [
                '<?php A\B\trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                null,
                [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => false],
            ],
            [
                '<?php @\trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php \trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php echo "test";@trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php echo "test";trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php //
@Trigger_Error/**/("This is a deprecation warning.", E_USER_DEPRECATED/***/); ?>',
                '<?php //
Trigger_Error/**/("This is a deprecation warning.", E_USER_DEPRECATED/***/); ?>',
            ],
            [
                '<?php new trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php new \trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php $foo->trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php Foo::trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); mkdir("dir"); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
                [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true],
            ],
            [
                '<?php $foo->isBar(); ?>',
                '<?php @$foo->isBar(); ?>',
                [ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true],
            ],
            [
                '<?php Foo::isBar(); ?>',
                '<?php @Foo::isBar(); ?>',
                [ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true],
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
                [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['mkdir']],
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); unlink($path); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); @unlink($path); ?>',
                [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['mkdir']],
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
                [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['trigger_error']],
            ],
        ];

        foreach ($tests as $index => $test) {
            yield $index => $test;
        }

        if (\PHP_VERSION_ID < 80000) {
            yield [
                '<?php \A\B/* */\trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ];
        }
    }

    /**
     * @requires PHP 7.3
     */
    public function testFix73()
    {
        $this->doTest(
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED, );',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED, );'
        );
    }
}
