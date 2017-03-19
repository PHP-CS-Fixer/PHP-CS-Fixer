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

use PhpCsFixer\Test\AbstractFixerTestCase;

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
     * @dataProvider provideClosingTagExamples
     */
    public function testOneLineFix($expected, $input = null)
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('The short_open_tag option is required to be enabled.');
        }

        $this->doTest($expected, $input);
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
