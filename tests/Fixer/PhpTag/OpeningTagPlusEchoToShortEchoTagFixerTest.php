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
 */
final class OpeningTagPlusEchoToShortEchoTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideClosingTagExamples
     */
    public function testOneLineFix($expected, $input = null)
    {
        if (50400 > PHP_VERSION_ID && !ini_get('short_open_tag')) {
            // On PHP <5.4 short echo tag is parsed as T_INLINE_HTML if short_open_tag is disabled
            // On PHP >=5.4 short echo tag is always parsed properly regardless of short_open_tag  option
            $this->markTestSkipped('PHP 5.4 (or later) or short_open_tag option is required.');
        }

        if (defined('HHVM_VERSION')) {
            // HHVM parses '<?=' anywhere but at the beginning of file as T_ECHO instead of T_OPEN_TAG_WITH_ECHO
            // See https://github.com/facebook/hhvm/issues/7161
            $this->markTestSkipped('HHVM compares the results incorrectly.');
        }

        $this->doTest($expected, $input);
    }

    public function provideClosingTagExamples()
    {
        return array(
            array('<?= \'Foo\';', '<?php echo \'Foo\';'),
            array('<?= \'Foo\'; ?> PLAIN TEXT', '<?php echo \'Foo\'; ?> PLAIN TEXT'),
            array('PLAIN TEXT<?= \'Foo\'; ?>', 'PLAIN TEXT<?php echo \'Foo\'; ?>'),
            array('<?= \'Foo\'; ?> <?= \'Bar\'; ?>', '<?php echo \'Foo\'; ?> <?php echo \'Bar\'; ?>'),
            array("<?=\n\tfoo();", "<?php echo\n\tfoo();"),
            array('<?= \'foo\'; echo \'bar\';', '<?php echo \'foo\'; echo \'bar\';'),
            array('<?php foo(); echo \'bar\';'),
        );
    }
}
