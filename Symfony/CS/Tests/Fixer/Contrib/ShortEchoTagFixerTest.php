<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Vincent Klaiber <hello@vinkla.com>
 */
final class ShortEchoTagFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideClosingTagExamples
     * @requires PHP 5.4
     */
    public function testOneLineFix($expected, $input = null)
    {
        /*
         * short_echo_tag setting is ignored by HHVM
         * @see https://github.com/facebook/hhvm/issues/4809
         */
        if (!defined('HHVM_VERSION')) {
            $this->makeTest($expected, $input);
        }
    }

    public function provideClosingTagExamples()
    {
        return array(
            array('<?php echo \'Foo\';', '<?= \'Foo\';'),
            array('<?php echo \'Foo\'; ?> PLAIN TEXT', '<?= \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?php echo \'Foo\'; ?>', 'PLAIN TEXT<?= \'Foo\'; ?>'),
            array('<?php echo \'Foo\'; ?> <?php echo \'Bar\'; ?>', '<?= \'Foo\'; ?> <?= \'Bar\'; ?>'),
            array('<?php echo foo();', '<?=foo();'),
        );
    }
}
