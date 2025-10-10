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

use org\bovigo\vfs\vfsStream;
use PhpCsFixer\Linter\CachingLinter;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Linter\LintingResultInterface;
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\CachingLinter
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class CachingLinterTest extends TestCase
{
    /**
     * @dataProvider provideIsAsyncCases
     */
    public function testIsAsync(bool $isAsync): void
    {
        $sublinter = $this->createLinterDouble($isAsync, [], []);

        $linter = new CachingLinter($sublinter);

        self::assertSame($isAsync, $linter->isAsync());
    }

    /**
     * @return iterable<int, array{bool}>
     */
    public static function provideIsAsyncCases(): iterable
    {
        yield [true];

        yield [false];
    }

    public function testLintFileIsCalledOnceOnSameContent(): void
    {
        $fs = vfsStream::setup('root', null, [
            'foo.php' => '<?php echo "baz";',
            'bar.php' => '<?php echo "baz";',
            'baz.php' => '<?php echo "foobarbaz";',
        ]);

        $result1 = $this->createLintingResultDouble();
        $result2 = $this->createLintingResultDouble();

        $sublinter = $this->createLinterDouble(
            null,
            [
                $fs->url().'/foo.php' => $result1,
                $fs->url().'/baz.php' => $result2,
            ],
            [],
        );

        $linter = new CachingLinter($sublinter);

        self::assertSame($result1, $linter->lintFile($fs->url().'/foo.php'));
        self::assertSame($result1, $linter->lintFile($fs->url().'/foo.php'));
        self::assertSame($result1, $linter->lintFile($fs->url().'/bar.php'));
        self::assertSame($result2, $linter->lintFile($fs->url().'/baz.php'));
    }

    public function testLintSourceIsCalledOnceOnSameContent(): void
    {
        $result1 = $this->createLintingResultDouble();
        $result2 = $this->createLintingResultDouble();

        $sublinter = $this->createLinterDouble(
            null,
            [],
            [
                '<?php echo "baz";' => $result1,
                '<?php echo "foobarbaz";' => $result2,
            ],
        );

        $linter = new CachingLinter($sublinter);

        self::assertSame($result1, $linter->lintSource('<?php echo "baz";'));
        self::assertSame($result1, $linter->lintSource('<?php echo "baz";'));
        self::assertSame($result2, $linter->lintSource('<?php echo "foobarbaz";'));
    }

    /**
     * @param array<string, LintingResultInterface> $allowedLintFileCalls
     * @param array<string, LintingResultInterface> $allowedLintSourceCalls
     */
    private function createLinterDouble(?bool $isAsync, array $allowedLintFileCalls, array $allowedLintSourceCalls): LinterInterface
    {
        return new class($isAsync, $allowedLintFileCalls, $allowedLintSourceCalls) implements LinterInterface {
            private ?bool $isAsync;

            /** @var array<string, LintingResultInterface> */
            private array $allowedLintFileCalls;

            /** @var array<string, LintingResultInterface> */
            private array $allowedLintSourceCalls;

            /**
             * @param array<string, LintingResultInterface> $allowedLintFileCalls
             * @param array<string, LintingResultInterface> $allowedLintSourceCalls
             */
            public function __construct(?bool $isAsync, array $allowedLintFileCalls, array $allowedLintSourceCalls)
            {
                $this->isAsync = $isAsync;
                $this->allowedLintFileCalls = $allowedLintFileCalls;
                $this->allowedLintSourceCalls = $allowedLintSourceCalls;
            }

            public function isAsync(): bool
            {
                return $this->isAsync;
            }

            public function lintFile(string $path): LintingResultInterface
            {
                if (!isset($this->allowedLintFileCalls[$path])) {
                    throw new \LogicException(\sprintf('File "%s" should not be linted.', $path));
                }

                $result = $this->allowedLintFileCalls[$path];
                unset($this->allowedLintFileCalls[$path]);

                return $result;
            }

            public function lintSource(string $source): LintingResultInterface
            {
                if (!isset($this->allowedLintSourceCalls[$source])) {
                    throw new \LogicException(\sprintf('File "%s" should not be linted.', $source));
                }

                $result = $this->allowedLintSourceCalls[$source];
                unset($this->allowedLintSourceCalls[$source]);

                return $result;
            }
        };
    }

    private function createLintingResultDouble(): LintingResultInterface
    {
        return new class implements LintingResultInterface {
            public function check(): void
            {
                throw new \LogicException('Not implemented.');
            }
        };
    }
}
