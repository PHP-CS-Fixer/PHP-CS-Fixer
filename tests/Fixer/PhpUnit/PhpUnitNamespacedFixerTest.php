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

namespace PhpCsFixer\Tests\Fixer\PhpUnit;

use PhpCsFixer\Fixer\PhpUnit\PhpUnitTargetVersion;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\PhpUnit\PhpUnitNamespacedFixer
 */
final class PhpUnitNamespacedFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
    {
        return [
            'class_mapping' => [
                '<?php new PHPUnit\Framework\Error\Error();',
                '<?php new PHPUnit_Framework_Error();',
            ],
            'class_mapping_bogus_fqcn' => [
                '<?php new \PHPUnit\Framework\MockObject\Stub\ReturnStub();',
                '<?php new \PHPUnit_Framework_MockObject_Stub_Return();',
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    final class TextDiffTest extends PHPUnit\Framework\TestCase
    {
    }',
                '<?php
    final class TextDiffTest extends PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    use \PHPUnit\Framework\TestCase;
    final class TextDiffTest extends TestCase
    {
    }',
                '<?php
    use \PHPUnit_Framework_TestCase;
    final class TextDiffTest extends PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    use \PHPUnit\FRAMEWORK\TestCase as TestAlias;
    final class TextDiffTest extends TestAlias
    {
    }',
                '<?php
    use \PHPUnit_FRAMEWORK_TestCase as TestAlias;
    final class TextDiffTest extends TestAlias
    {
    }',
            ],
            [
                '<?php
    namespace Foo;
    use PHPUnit\Framework\TestCase;
    final class TextDiffTest extends TestCase
    {
    }',
                '<?php
    namespace Foo;
    use PHPUnit_Framework_TestCase;
    final class TextDiffTest extends PHPUnit_Framework_TestCase
    {
    }',
            ],
            [
                '<?php
    namespace Foo;
    use PHPUnit\Framework\TestCase as TestAlias;
    final class TextDiffTest extends TestAlias
    {
    }',
                '<?php
    namespace Foo;
    use PHPUnit_Framework_TestCase as TestAlias;
    final class TextDiffTest extends TestAlias
    {
    }',
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function aaa()
        {
            $a = new PHPUnit_Framework_Assert();
            $b = new PHPUnit_Framework_BaseTestListener();
            $c = new PHPUnit_Framework_TestListener();

            $d1 = new PHPUnit_Aaa();
            $d2 = new PHPUnit_Aaa_Bbb();
            $d3 = new PHPUnit_Aaa_Bbb_Ccc();
            $d4 = new PHPUnit_Aaa_Bbb_Ccc_Ddd();
            $d5 = new PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function aaa()
        {
            $a = new PHPUnit_Framework_Assert();
            $b = new PHPUnit_Framework_BaseTestListener();
            $c = new PHPUnit_Framework_TestListener();

            $d1 = new PHPUnit_Aaa();
            $d2 = new PHPUnit_Aaa_Bbb();
            $d3 = new PHPUnit_Aaa_Bbb_Ccc();
            $d4 = new PHPUnit_Aaa_Bbb_Ccc_Ddd();
            $d5 = new PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee();
        }
    }',
                ['target' => PhpUnitTargetVersion::VERSION_4_8],
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function aaa()
        {
            $a = new PHPUnit\Framework\Assert();
            $b = new PHPUnit\Framework\BaseTestListener();
            $c = new PHPUnit\Framework\TestListener();

            $d1 = new PHPUnit_Aaa();
            $d2 = new PHPUnit_Aaa_Bbb();
            $d3 = new PHPUnit_Aaa_Bbb_Ccc();
            $d4 = new PHPUnit_Aaa_Bbb_Ccc_Ddd();
            $d5 = new PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function aaa()
        {
            $a = new PHPUnit_Framework_Assert();
            $b = new PHPUnit_Framework_BaseTestListener();
            $c = new PHPUnit_Framework_TestListener();

            $d1 = new PHPUnit_Aaa();
            $d2 = new PHPUnit_Aaa_Bbb();
            $d3 = new PHPUnit_Aaa_Bbb_Ccc();
            $d4 = new PHPUnit_Aaa_Bbb_Ccc_Ddd();
            $d5 = new PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee();
        }
    }',
                ['target' => PhpUnitTargetVersion::VERSION_5_7],
            ],
            [
                '<?php
    final class MyTest extends \PHPUnit\Framework\TestCase
    {
        public function aaa()
        {
            $a = new PHPUnit\Framework\Assert();
            $b = new PHPUnit\Framework\BaseTestListener();
            $c = new PHPUnit\Framework\TestListener();

            $d1 = new PHPUnit\Aaa();
            $d2 = new PHPUnit\Aaa\Bbb();
            $d3 = new PHPUnit\Aaa\Bbb\Ccc();
            $d4 = new PHPUnit\Aaa\Bbb\Ccc\Ddd();
            $d5 = new PHPUnit\Aaa\Bbb\Ccc\Ddd\Eee();
        }
    }',
                '<?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function aaa()
        {
            $a = new PHPUnit_Framework_Assert();
            $b = new PHPUnit_Framework_BaseTestListener();
            $c = new PHPUnit_Framework_TestListener();

            $d1 = new PHPUnit_Aaa();
            $d2 = new PHPUnit_Aaa_Bbb();
            $d3 = new PHPUnit_Aaa_Bbb_Ccc();
            $d4 = new PHPUnit_Aaa_Bbb_Ccc_Ddd();
            $d5 = new PHPUnit_Aaa_Bbb_Ccc_Ddd_Eee();
        }
    }',
                ['target' => PhpUnitTargetVersion::VERSION_6_0],
            ],
            [
                '<?php
                    echo \PHPUnit\Runner\Version::id();
                    echo \PHPUnit\Runner\Version::id();
                    ',
                '<?php
                    echo \PHPUnit_Runner_Version::id();
                    echo \PHPUnit_Runner_Version::id();
                    ',
            ],
            [
                '<?php
                final class MyTest extends TestCase
                {
                    const PHPUNIT_FOO = "foo";
                }',
            ],
            [
                '<?php
                final class MyTest extends TestCase
                {
                    const FOO = Bar::PHPUNIT_FOO;
                }',
            ],
        ];
    }

    /**
     * @dataProvider provideClassIsFixedCases
     */
    public function testClassIsFixed(string $class): void
    {
        $this->fixer->configure(['target' => PhpUnitTargetVersion::VERSION_NEWEST]);

        Tokens::clearCache();
        $tokens = Tokens::fromCode(sprintf('<?php new %s();', $class));

        $this->fixer->fix($this->getTestFile(), $tokens);

        static::assertTrue($tokens->isChanged());
        static::assertStringNotContainsString('_', $tokens->generateCode());
    }

    public static function provideClassIsFixedCases(): iterable
    {
        $classmap = require __DIR__.'/../../../vendor/composer/autoload_classmap.php';

        foreach ($classmap as $class => $file) {
            if (str_starts_with($class, 'PHPUnit_')) {
                yield $file => [$class];
            }
        }
    }

    /**
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideFix81Cases(): iterable
    {
        yield [
            '<?php
                final class MyTest extends TestCase
                {
                    final public const PHPUNIT_FOO_A = "foo";
                    final public const PHPUNIT_FOO_B = Bar::PHPUNIT_FOO;
                }',
        ];
    }
}
