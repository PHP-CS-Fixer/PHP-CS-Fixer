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
        return [
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
                '<?php \A\B/* */\trigger_error("This is not a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ],
            [
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                null,
                ['mute_deprecation_error' => false],
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
                ['mute_deprecation_error' => true, 'noise_remaining_usages' => true],
            ],
            [
                '<?php $foo->isBar(); ?>',
                '<?php @$foo->isBar(); ?>',
                ['noise_remaining_usages' => true],
            ],
            [
                '<?php Foo::isBar(); ?>',
                '<?php @Foo::isBar(); ?>',
                ['noise_remaining_usages' => true],
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); ?>',
                ['mute_deprecation_error' => true, 'noise_remaining_usages' => true, 'noise_remaining_usages_exclude' => ['mkdir']],
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); unlink($path); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @mkdir("dir"); @unlink($path); ?>',
                ['mute_deprecation_error' => true, 'noise_remaining_usages' => true, 'noise_remaining_usages_exclude' => ['mkdir']],
            ],
            [
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); @trigger_error("This is not a deprecation warning.", E_USER_WARNING); ?>',
                ['mute_deprecation_error' => true, 'noise_remaining_usages' => true, 'noise_remaining_usages_exclude' => ['trigger_error']],
            ],
        ];
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
