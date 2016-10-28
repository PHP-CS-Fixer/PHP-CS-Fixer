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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Fixer;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Runner\Runner;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class RunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PhpCsFixer\Runner\Runner::fix
     * @covers PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixSuccessfully()
    {
        $linterProphecy = $this->prophesize('PhpCsFixer\Linter\LinterInterface');
        $linterProphecy
            ->isAsync()
            ->willReturn(false);
        $linterProphecy
            ->lintFile(Argument::type('string'))
            ->willReturn($this->prophesize('PhpCsFixer\Linter\LintingResultInterface')->reveal());
        $linterProphecy
            ->lintSource(Argument::type('string'))
            ->willReturn($this->prophesize('PhpCsFixer\Linter\LintingResultInterface')->reveal());

        $runner = new Runner(
            Finder::create()->in(
                __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'
            ),
            array(
                new Fixer\ClassNotation\VisibilityRequiredFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ),
            new NullDiffer(),
            null,
            new ErrorsManager(),
            $linterProphecy->reveal(),
            true,
            new NullCacheManager()
        );

        $changed = $runner->fix();
        $pathToInvalidFile = 'somefile.php';

        $this->assertCount(1, $changed);
        $this->assertCount(2, $changed[$pathToInvalidFile]);
        $this->assertSame(array('appliedFixers', 'diff'), array_keys($changed[$pathToInvalidFile]));
        $this->assertSame('visibility_required', $changed[$pathToInvalidFile]['appliedFixers'][0]);
    }

    /**
     * @covers PhpCsFixer\Runner\Runner::fix
     * @covers PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixInvalidFileReportsToErrorManager()
    {
        $errorsManager = new ErrorsManager();

        $runner = new Runner(
            Finder::create()->in(
                __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'invalid'
            ),
            array(
                new Fixer\ClassNotation\VisibilityRequiredFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ),
            new NullDiffer(),
            null,
            $errorsManager,
            new Linter(),
            true,
            new NullCacheManager()
        );

        $changed = $runner->fix();
        $pathToInvalidFile = 'somefile.php';

        $this->assertCount(0, $changed);

        $errors = $errorsManager->getInvalidErrors();

        $this->assertCount(1, $errors);

        $error = $errors[0];

        $this->assertInstanceOf('PhpCsFixer\Error\Error', $error);

        $this->assertSame(Error::TYPE_INVALID, $error->getType());
        $this->assertSame($pathToInvalidFile, $error->getFilePath());
    }
}
