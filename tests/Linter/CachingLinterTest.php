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
use PhpCsFixer\Tests\TestCase;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\CachingLinter
 */
final class CachingLinterTest extends TestCase
{
    /**
     * @dataProvider provideIsAsyncCases
     */
    public function testIsAsync(bool $isAsync): void
    {
        $sublinter = $this->prophesize(\PhpCsFixer\Linter\LinterInterface::class);
        $sublinter->isAsync()->willReturn($isAsync);

        $linter = new CachingLinter($sublinter->reveal());

        static::assertSame($isAsync, $linter->isAsync());
    }

    public static function provideIsAsyncCases(): array
    {
        return [
            [true],
            [false],
        ];
    }

    public function testLintFileIsCalledOnceOnSameContent(): void
    {
        $fs = vfsStream::setup('root', null, [
            'foo.php' => '<?php echo "baz";',
            'bar.php' => '<?php echo "baz";',
            'baz.php' => '<?php echo "foobarbaz";',
        ]);

        $result1 = $this->prophesize(\PhpCsFixer\Linter\LintingResultInterface::class);
        $result2 = $this->prophesize(\PhpCsFixer\Linter\LintingResultInterface::class);

        $sublinter = $this->prophesize(\PhpCsFixer\Linter\LinterInterface::class);
        $sublinter->lintFile($fs->url().'/foo.php')->shouldBeCalledTimes(1)->willReturn($result1->reveal());
        $sublinter->lintFile($fs->url().'/bar.php')->shouldNotBeCalled();
        $sublinter->lintFile($fs->url().'/baz.php')->shouldBeCalledTimes(1)->willReturn($result2->reveal());

        $linter = new CachingLinter($sublinter->reveal());

        static::assertSame($result1->reveal(), $linter->lintFile($fs->url().'/foo.php'));
        static::assertSame($result1->reveal(), $linter->lintFile($fs->url().'/foo.php'));
        static::assertSame($result1->reveal(), $linter->lintFile($fs->url().'/bar.php'));
        static::assertSame($result2->reveal(), $linter->lintFile($fs->url().'/baz.php'));
    }

    public function testLintSourceIsCalledOnceOnSameContent(): void
    {
        $result1 = $this->prophesize(\PhpCsFixer\Linter\LintingResultInterface::class);
        $result2 = $this->prophesize(\PhpCsFixer\Linter\LintingResultInterface::class);

        $sublinter = $this->prophesize(\PhpCsFixer\Linter\LinterInterface::class);
        $sublinter->lintSource('<?php echo "baz";')->shouldBeCalledTimes(1)->willReturn($result1->reveal());
        $sublinter->lintSource('<?php echo "foobarbaz";')->shouldBeCalledTimes(1)->willReturn($result2->reveal());

        $linter = new CachingLinter($sublinter->reveal());

        static::assertSame($result1->reveal(), $linter->lintSource('<?php echo "baz";'));
        static::assertSame($result1->reveal(), $linter->lintSource('<?php echo "baz";'));
        static::assertSame($result2->reveal(), $linter->lintSource('<?php echo "foobarbaz";'));
    }
}
