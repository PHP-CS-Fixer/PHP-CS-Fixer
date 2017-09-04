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

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Vincent Klaiber <hello@vinkla.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpTag\NoShortEchoTagFixer
 */
final class NoShortEchoTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        if (50400 > PHP_VERSION_ID && !ini_get('short_open_tag')) {
            // On PHP <5.4 short echo tag is parsed as T_INLINE_HTML if short_open_tag is disabled
            // On PHP >=5.4 short echo tag is always parsed properly regardless of short_open_tag  option
            $this->markTestSkipped('PHP 5.4 (or later) or short_open_tag option is required.');
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
