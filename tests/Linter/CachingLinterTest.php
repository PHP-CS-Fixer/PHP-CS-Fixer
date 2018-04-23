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
     * @param bool $isAsync
     *
     * @dataProvider provideIsAsyncCases
     */
    public function testIsAsync($isAsync)
    {
        $sublinter = $this->prophesize('PhpCsFixer\Linter\LinterInterface');
        $sublinter->isAsync()->willReturn($isAsync);

        $linter = new CachingLinter($sublinter->reveal());

        $this->assertSame($isAsync, $linter->isAsync());
    }

    public function provideIsAsyncCases()
    {
        return array(
            array(true),
            array(false),
        );
    }

    public function testLintFileIsCalledOnceOnSameContent()
    {
        $fs = vfsStream::setup('root', null, array(
            'foo.php' => '<?php echo "baz";',
            'bar.php' => '<?php echo "baz";',
            'baz.php' => '<?php echo "foobarbaz";',
        ));

        $result1 = $this->prophesize('PhpCsFixer\Linter\LintingResultInterface');
        $result2 = $this->prophesize('PhpCsFixer\Linter\LintingResultInterface');

        $sublinter = $this->prophesize('PhpCsFixer\Linter\LinterInterface');
        $sublinter->lintFile($fs->url().'/foo.php')->shouldBeCalledTimes(1)->willReturn($result1->reveal());
        $sublinter->lintFile($fs->url().'/bar.php')->shouldNotBeCalled();
        $sublinter->lintFile($fs->url().'/baz.php')->shouldBeCalledTimes(1)->willReturn($result2->reveal());

        $linter = new CachingLinter($sublinter->reveal());

        $this->assertSame($result1->reveal(), $linter->lintFile($fs->url().'/foo.php'));
        $this->assertSame($result1->reveal(), $linter->lintFile($fs->url().'/foo.php'));
        $this->assertSame($result1->reveal(), $linter->lintFile($fs->url().'/bar.php'));
        $this->assertSame($result2->reveal(), $linter->lintFile($fs->url().'/baz.php'));
    }

    public function testLintSourceIsCalledOnceOnSameContent()
    {
        $result1 = $this->prophesize('PhpCsFixer\Linter\LintingResultInterface');
        $result2 = $this->prophesize('PhpCsFixer\Linter\LintingResultInterface');

        $sublinter = $this->prophesize('PhpCsFixer\Linter\LinterInterface');
        $sublinter->lintSource('<?php echo "baz";')->shouldBeCalledTimes(1)->willReturn($result1->reveal());
        $sublinter->lintSource('<?php echo "foobarbaz";')->shouldBeCalledTimes(1)->willReturn($result2->reveal());

        $linter = new CachingLinter($sublinter->reveal());

        $this->assertSame($result1->reveal(), $linter->lintSource('<?php echo "baz";'));
        $this->assertSame($result1->reveal(), $linter->lintSource('<?php echo "baz";'));
        $this->assertSame($result2->reveal(), $linter->lintSource('<?php echo "foobarbaz";'));
    }
}
