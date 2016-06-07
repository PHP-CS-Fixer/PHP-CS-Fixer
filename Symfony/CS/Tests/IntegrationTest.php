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

namespace Symfony\CS\Tests;

use Symfony\CS\Fixer;

/**
 * Test that parses and runs the fixture '*.test' files found in '/Fixtures/Integration'.
 *
 * @author SpacePossum <possumfromspace@gmail.com>
 *
 * @internal
 */
final class IntegrationTest extends AbstractIntegrationTest
{
    /**
     * {@inheritdoc}
     */
    protected static function getFixturesDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'Integration';
    }

    /**
     * {@inheritdoc}
     */
    protected static function getTempFile()
    {
        return self::getFixturesDir().DIRECTORY_SEPARATOR.'.tmp.php';
    }

    /**
     * @dataProvider provideBasicSources
     */
    public function testBasicFiles($source, array $whiteListFixerNames = array())
    {
        $this->doTest($source, $whiteListFixerNames);
    }

    public function provideBasicSources()
    {
        return array(
            array(''),
            array('    '),
            array("       <?php\n"),
            array('<?php ', array('header_comment')),
            array("<?php\n", array('header_comment')),
            array("<?php\n\n\n", array('eof_ending', 'header_comment')),
            array('<?php //', array('no_empty_comment', 'eof_ending', 'header_comment')),
            array('<?php /**/  /**/', array('no_empty_comment', 'eof_ending', 'header_comment')),
        );
    }

    /**
     * @dataProvider provideBasicSources
     */
    public function testBasicFilesWithShortTag($source, array $whiteListFixerNames = array())
    {
        if (!ini_get('short_open_tag')) {
            $this->markTestSkipped('PHP short open tags are not enabled.');

            return;
        }

        $this->doTest($source, $whiteListFixerNames);
    }

    public function provideBasicSourcesWithShortTag()
    {
        return array(
            array('<?= 1?>', array('short_echo_tag', 'php_closing_tag')),
            array('<?=$a, $b, $c?>', array('short_echo_tag', 'php_closing_tag')),
        );
    }

    /**
     * @param string   $source
     * @param string[] $whiteListFixerNames
     *
     * @dataProvider provideBasicSources
     */
    private function doTest($source, array $whiteListFixerNames = array())
    {
        $this->assertNull($this->lintSource($source));

        $mockInfo = new \SplFileInfo(__FILE__);
        $fixer = new Fixer();
        $fixer->registerBuiltInFixers();
        $allFixers = $fixer->getFixers();

        $notExpectedChanges = array();
        $missingChanges = array();
        foreach ($allFixers as $fixer) {
            $fixed = $fixer->fix($mockInfo, $source);
            if (in_array($fixer->getName(), $whiteListFixerNames, true)) {
                if ($fixed === $source) {
                    $missingChanges[] = array(
                        $fixer->getName(),
                        $source,
                    );
                }
            } elseif ($fixed !== $source) {
                $notExpectedChanges[] = array(
                    $fixer->getName(),
                    $source,
                    $fixed,
                );
            }
        }

        if (count($notExpectedChanges) > 0) {
            $message = 'No changes expected, got:';
            foreach ($notExpectedChanges as $change) {
                $message .= sprintf("\nFixer \"%s\" changed:\n\"%s\"\nto:\n\"%s\".", $change[0], $change[1], $change[2]);
            }

            $this->fail($message);
        }

        if (count($missingChanges) > 0) {
            $message = 'Changes expected, missing:';
            foreach ($missingChanges as $notChange) {
                $message .= sprintf("\nFixer \"%s\" did not change:\n\"%s\".", $notChange[0], $notChange[1]);
            }

            $this->fail($message);
        }
    }
}
