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
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            '<?php trigger_error("This is not a deprecation warning."); @f(); ?>',
        ];

        yield [
            '<?php trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
        ];

        yield [
            '<?php A\B\trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            null,
            [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => false],
        ];

        yield [
            '<?php @\trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            '<?php \trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php echo "test";@trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            '<?php echo "test";trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php //
@Trigger_Error/**/("This is a deprecation warning.", E_USER_DEPRECATED/***/); ?>',
            '<?php //
Trigger_Error/**/("This is a deprecation warning.", E_USER_DEPRECATED/***/); ?>',
        ];

        yield [
            '<?php new trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php new \trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php $foo->trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php Foo::trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];

        yield [
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); mkdir("dir"); ?>',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
            [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true],
        ];

        yield [
            '<?php $foo->isBar(); ?>',
            '<?php @$foo->isBar(); ?>',
            [ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true],
        ];

        yield [
            '<?php Foo::isBar(); ?>',
            '<?php @Foo::isBar(); ?>',
            [ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true],
        ];

        yield [
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
            [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['mkdir']],
        ];

        yield [
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); unlink($path); ?>',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); @unlink($path); ?>',
            [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['mkdir']],
        ];

        yield [
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
            [ErrorSuppressionFixer::OPTION_MUTE_DEPRECATION_ERROR => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES => true, ErrorSuppressionFixer::OPTION_NOISE_REMAINING_USAGES_EXCLUDE => ['trigger_error']],
        ];

        yield [
            '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED, );',
            '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED, );',
        ];
    }

    /**
     * @dataProvider provideFixPre80Cases
     *
     * @requires PHP <8.0
     */
    public function testFixPre80(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixPre80Cases(): iterable
    {
        yield [
            '<?php \A\B/* */\trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
        ];
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            '<?php $a = trigger_error(...);',
        ];
    }
}
