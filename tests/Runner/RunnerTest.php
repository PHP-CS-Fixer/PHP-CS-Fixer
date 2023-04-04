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

namespace PhpCsFixer\Tests\Runner;

use PhpCsFixer\Cache\Directory;
use PhpCsFixer\Cache\NullCacheManager;
use PhpCsFixer\Differ\NullDiffer;
use PhpCsFixer\Error\Error;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Fixer;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Runner\Runner;
use PhpCsFixer\Tests\Fixtures\FakeDiffer;
use PhpCsFixer\Tests\TestCase;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Runner\Runner
 */
final class RunnerTest extends TestCase
{
    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixSuccessfully(): void
    {
        $linterProphecy = $this->prophesize(\PhpCsFixer\Linter\LinterInterface::class);
        $linterProphecy
            ->isAsync()
            ->willReturn(false)
        ;
        $linterProphecy
            ->lintFile(Argument::type('string'))
            ->willReturn($this->prophesize(\PhpCsFixer\Linter\LintingResultInterface::class)->reveal())
        ;
        $linterProphecy
            ->lintSource(Argument::type('string'))
            ->willReturn($this->prophesize(\PhpCsFixer\Linter\LintingResultInterface::class)->reveal())
        ;

        $fixers = [
            new Fixer\ClassNotation\VisibilityRequiredFixer(),
            new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
        ];

        $expectedChangedInfo = [
            'appliedFixers' => ['visibility_required'],
            'diff' => '',
        ];

        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $runner = new Runner(
            Finder::create()->in($path),
            $fixers,
            new NullDiffer(),
            null,
            new ErrorsManager(),
            $linterProphecy->reveal(),
            true,
            new NullCacheManager(),
            new Directory($path),
            false
        );

        $changed = $runner->fix();

        self::assertCount(2, $changed);
        self::assertSame($expectedChangedInfo, array_pop($changed));
        self::assertSame($expectedChangedInfo, array_pop($changed));

        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $runner = new Runner(
            Finder::create()->in($path),
            $fixers,
            new NullDiffer(),
            null,
            new ErrorsManager(),
            $linterProphecy->reveal(),
            true,
            new NullCacheManager(),
            new Directory($path),
            true
        );

        $changed = $runner->fix();

        self::assertCount(1, $changed);
        self::assertSame($expectedChangedInfo, array_pop($changed));
    }

    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatFixInvalidFileReportsToErrorManager(): void
    {
        $errorsManager = new ErrorsManager();

        $path = realpath(__DIR__.\DIRECTORY_SEPARATOR.'..').\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'invalid';
        $runner = new Runner(
            Finder::create()->in($path),
            [
                new Fixer\ClassNotation\VisibilityRequiredFixer(),
                new Fixer\Import\NoUnusedImportsFixer(), // will be ignored cause of test keyword in namespace
            ],
            new NullDiffer(),
            null,
            $errorsManager,
            new Linter(),
            true,
            new NullCacheManager()
        );
        $changed = $runner->fix();
        $pathToInvalidFile = $path.\DIRECTORY_SEPARATOR.'somefile.php';

        self::assertCount(0, $changed);

        $errors = $errorsManager->getInvalidErrors();

        self::assertCount(1, $errors);

        $error = $errors[0];

        self::assertInstanceOf(\PhpCsFixer\Error\Error::class, $error);

        self::assertSame(Error::TYPE_INVALID, $error->getType());
        self::assertSame($pathToInvalidFile, $error->getFilePath());
    }

    /**
     * @covers \PhpCsFixer\Runner\Runner::fix
     * @covers \PhpCsFixer\Runner\Runner::fixFile
     */
    public function testThatDiffedFileIsPassedToDiffer(): void
    {
        $spy = new FakeDiffer();
        $path = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Fixtures'.\DIRECTORY_SEPARATOR.'FixerTest'.\DIRECTORY_SEPARATOR.'fix';
        $fixers = [
            new Fixer\ClassNotation\VisibilityRequiredFixer(),
        ];

        $runner = new Runner(
            Finder::create()->in($path),
            $fixers,
            $spy,
            null,
            new ErrorsManager(),
            new Linter(),
            true,
            new NullCacheManager(),
            new Directory($path),
            true
        );

        $runner->fix();

        self::assertSame($path, $spy->passedFile->getPath());
    }
}
