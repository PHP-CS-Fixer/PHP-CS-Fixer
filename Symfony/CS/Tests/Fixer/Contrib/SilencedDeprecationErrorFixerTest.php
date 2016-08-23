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

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Jules Pietri <jules@heahprod.com>
 *
 * @internal
 */
final class SilencedDeprecationErrorFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php trigger_error("This is a deprecation warning."); ?>',
            ),
            array(
                '<?php trigger_error("This is a deprecation warning.", E_USER_WARNING); ?>',
            ),
            array(
                '<?php A\B\trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ),
            array(
                '<?php \A\B/* */\trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ),
            array(
                '<?php @trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ),
            array(
                '<?php @\trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php \trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ),
            array(
                '<?php echo "test";@trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
                '<?php echo "test";trigger_error("This is a deprecation warning.", E_USER_DEPRECATED); ?>',
            ),
            array(
                '<?php //
@Trigger_Error/**/("This is a deprecation warning.", E_USER_DEPRECATED/***/); ?>',
                '<?php //
Trigger_Error/**/("This is a deprecation warning.", E_USER_DEPRECATED/***/); ?>',
            ),
        );
    }
}
