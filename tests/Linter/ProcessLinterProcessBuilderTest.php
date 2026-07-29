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

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\ProcessLinterProcessBuilder;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresOperatingSystem;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLinterProcessBuilder
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(ProcessLinterProcessBuilder::class)]
final class ProcessLinterProcessBuilderTest extends TestCase
{
    /**
     * @dataProvider providePrepareCommandOnPhpOnLinuxOrMacCases
     *
     * @requires OS Linux|Darwin
     */
    #[DataProvider('providePrepareCommandOnPhpOnLinuxOrMacCases')]
    #[RequiresOperatingSystem('Linux|Darwin')]
    public function testPrepareCommandOnPhpOnLinuxOrMac(string $executable, string $file, string $expected): void
    {
        $this->testPrepareCommand($executable, $file, $expected);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function providePrepareCommandOnPhpOnLinuxOrMacCases(): iterable
    {
        yield 'Linux-like' => ['php', 'foo.php', "'php' '-l' 'foo.php'"];

        yield 'Windows-like' => ['C:\Program Files\php\php.exe', 'foo bar\baz.php', "'C:\\Program Files\\php\\php.exe' '-l' 'foo bar\\baz.php'"];
    }

    /**
     * @dataProvider providePrepareCommandOnPhpOnWindowsCases
     *
     * @requires OS ^Win
     */
    #[DataProvider('providePrepareCommandOnPhpOnWindowsCases')]
    #[RequiresOperatingSystem('^Win')]
    public function testPrepareCommandOnPhpOnWindows(string $executable, string $file, string $expected): void
    {
        $this->testPrepareCommand($executable, $file, $expected);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function providePrepareCommandOnPhpOnWindowsCases(): iterable
    {
        yield 'Linux-like' => ['php', 'foo.php', 'C:\tools\php\php.EXE -l foo.php'];

        yield 'Windows-like' => ['C:\Program Files\php\php.exe', 'foo bar\baz.php', '"C:\Program Files\php\php.exe" -l "foo bar\baz.php"'];
    }

    private function testPrepareCommand(string $executable, string $file, string $expected): void
    {
        $builder = new ProcessLinterProcessBuilder($executable);

        self::assertSame(
            $expected,
            $builder->build($file)->getCommandLine(),
        );
    }
}
