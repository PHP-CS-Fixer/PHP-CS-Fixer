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

namespace PhpCsFixer\Tests\Priority;

use PhpCsFixer\FixerFactory;
use PhpCsFixer\Priority\PrioritiesCalculator;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Priority\PriorityFixer
 */
final class PriorityFixerTest extends AbstractFixerTestCase
{
    public function testFixerIsRunLast()
    {
        $calculator = new PrioritiesCalculator();
        $priorities = $calculator->calculate();

        static::assertLessThan(min($priorities), $this->fixer->getPriority());
    }

    public function testAllFixersAreSupported()
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        foreach ($fixerFactory->getFixers() as $fixer) {
            $reflection = new \ReflectionObject($fixer);
            $file = new \SplFileObject($reflection->getFileName());
            static::assertTrue($this->fixer->supports($file));
            static::assertTrue($this->fixer->isCandidate(Tokens::fromCode('<?php // dummy code to make sure always being candidate')));
        }
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input, new \SplFileInfo(__DIR__.'/../../src/Fixer/Basic/EncodingFixer.php'));
    }

    public static function provideFixCases()
    {
        return [
            [
                '<?php class Foo {}',
            ],
            [
                '<?php class Foo extends Bar {}',
            ],
            [
                '<?php class NonPrintableCharacterFixer extends AbstractFixer {}',
            ],
            [
                '<?php class NonPrintableCharacterFixer extends AbstractFixer {
                    public function getPriority() { return $this->proxyFixer->getPriority(); }
                }',
            ],
            [
                '<?php class NonPrintableCharacterFixer extends AbstractFixer {
                    public function getPriority() { return 0; }
                }',
                '<?php class NonPrintableCharacterFixer extends AbstractFixer {
                    public function getPriority() { return -100; }
                }',
            ],
        ];
    }
}
