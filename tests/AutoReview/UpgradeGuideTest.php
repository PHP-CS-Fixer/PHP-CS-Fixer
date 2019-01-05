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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group auto-review
 * @group covers-nothing
 */
final class UpgradeGuideTest extends TestCase
{
    /**
     * @var array<string, string>
     */
    private static $fixerRenames = [];

    public static function setUpBeforeClass()
    {
        $inRenamedRulesSection = false;

        foreach (file(__DIR__.'/../../UPGRADE.md', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            if ('Old name | New name | Note' === $line) {
                $inRenamedRulesSection = true;

                continue;
            }
            if ('Changes to Fixers' === $line) {
                break;
            }
            if ($inRenamedRulesSection && false === strpos($line, '--------')) {
                $columns = explode('|', $line);
                self::$fixerRenames[trim($columns[0])] = trim($columns[1]);
            }
        }
    }

    public static function tearDownAfterClass()
    {
        self::$fixerRenames = [];
    }

    public function testRenamesAreSorted()
    {
        $sorted = self::$fixerRenames;
        ksort($sorted);

        $this->assertSame($sorted, self::$fixerRenames);
    }

    /**
     * @dataProvider provideDeprecatedFixerCases
     *
     * @param DeprecatedFixerInterface $fixer
     */
    public function testDeprecatedFixerIsNotListedAsNew(DeprecatedFixerInterface $fixer)
    {
        $this->assertNotContains($fixer->getName(), self::$fixerRenames);
    }

    public function provideDeprecatedFixerCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        foreach ($factory->getFixers() as $fixer) {
            if ($fixer instanceof DeprecatedFixerInterface) {
                yield [$fixer];
            }
        }
    }
}
