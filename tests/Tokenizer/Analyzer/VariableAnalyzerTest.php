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

namespace PhpCsFixer\Tests\Tokenizer\Analyzer;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\Analyzer\VariableAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @covers \PhpCsFixer\Tokenizer\Analyzer\VariableAnalyzer
 *
 * @internal
 */
final class VariableAnalyzerTest extends TestCase
{
    /**
     * @param string             $source
     * @param array<string, int> $variableNames
     * @param array<string, int> $expected
     * @param int                $startIndex
     * @param null|int           $endIndex
     *
     * @dataProvider provideVariableAnalyzerCases
     */
    public function testVariableAnalyzer($source, array $variableNames, array $expected = null, $startIndex = null, $endIndex = null)
    {
        $analyzer = new VariableAnalyzer();
        $tokens = Tokens::fromCode($source);

        $startIndex = null === $startIndex ? 0 : $startIndex;
        $endIndex = null === $endIndex ? \count($tokens) : $endIndex;
        $expected = null === $expected ? $variableNames : $expected;

        static::assertSame(
            $expected,
            $analyzer->filterVariablePossiblyUsed($tokens, $startIndex, $endIndex, $variableNames)
        );
    }

    public function provideVariableAnalyzerCases()
    {
        yield 'simple used case, but out ot of range to check' => [
            '<?php $a; // foo
            // bar
            echo $a;
            ',
            ['$a' => 1],
            [],
            0,
            10,
        ];

        // used

        $used = [
            'as import 1' => '$f = static function () use ($f) {};',
            'as import 2' => '$f = static function () use ($a, $b, $f, $g) {};',
            'as import, reference' => '$f = static function () use (&$f) {};',
            'compact 1' => 'compact(\'f\');',
            'compact 2' => '\compact(\'f\');',
            'compact 1.1' => 'compact(\'g\');',
            'compact 2.1' => '\compact(\'g\');',
            'eval 1' => 'eval($z);',
            'super global' => '$_COOKIE[$x] = $y;',
            'include' => 'include __DIR__."/test3.php";',
            'include_once' => 'include_once __DIR__."/test3.php";',
            'require' => 'include __DIR__."/test3.php";',
            'require_once' => 'include_once __DIR__."/test3.php";',
            '${X}' => '$h = ${$g};',
            '$$c' => '$h = ${$$g};',
            '$$d' => '$h = $$d;',
            'interpolation 1' => 'echo "hello $f";',
            'interpolation 2' => 'echo "hello {$f}";',
            'interpolation 3' => 'echo "hello ${f}";',
            'heredoc' => '<<<"TEST"
Foo $f
TEST;
',
            'function call' => 'foo($f);',
            'lambda call' => '$a($f);',
        ];

        foreach ($used as $name => $item) {
            yield $name => [
                sprintf("<?php \$f = 1;\n%s", $item),
                ['$f' => 1],
                [],
                2,
            ];
        }

        yield 'simple used case, minimal range' => [
            '<?php echo $a; //',
            ['$a' => 3],
            [],
            3,
            4,
        ];

        yield 'simple used case, $_COOKIE' => [
            '<?php echo $_COOKIE[1];',
            ['$_COOKIE' => 3],
            [],
        ];

        // not used

        yield 'test not used, preserve index' => [
            '<?php $a = 1; // foo',
            ['$a' => 1],
            null,
            2,
        ];

        yield 'test not used' => [
            '<?php
$a = 1; // foo
# $a = 1;
// $a=1;
/** @var int $a */
$b = \'$a\';
$f = "\$a";

$d = function ($a) {
    echo $a;
};
$d(1);

$d = 1;
$d = static function () use ($d) {
    $a = 1;
    echo $a;
};
$d();

function foo($a = null){
    var_dump($a);
}

echo $A; // variables are case sensitive
',
            ['$a' => 1],
            null,
            2,
        ];
    }

    /**
     * @param string             $source
     * @param array<string, int> $variableNames
     * @param array<string, int> $expected
     * @param int                $startIndex
     * @param null|int           $endIndex
     *
     * @requires PHP 7.0
     * @dataProvider providePhp70Cases
     */
    public function testFixPhp70($source, array $variableNames, array $expected = null, $startIndex = null, $endIndex = null)
    {
        $this->testVariableAnalyzer($source, $variableNames, $expected, $startIndex, $endIndex);
    }

    public function providePhp70Cases()
    {
        $usedCases = [
            'used case, anonymous class constructor 1' => '<?php echo $a; /* */ $f = new class($a) {};',
            'used case, anonymous class constructor 2' => '<?php echo $a; /* */ $f = new class($a, $b) {};',
            'used case, anonymous class constructor 3' => '<?php echo $a; /* */ $f = new class($b, $a, $d) {};',
        ];

        foreach ($usedCases as $index => $usedCase) {
            yield $index => [
                $usedCase,
                ['$a' => 3],
                [],
                5,
            ];
        }

        $notUsedCases = [
            'not used case, anonymous class constructor 1' => '<?php echo $a; /* */ $f = new class {};',
            'not used case, anonymous class constructor 2' => '<?php echo $a; /* */ $f = new class($aa) {};',
        ];

        foreach ($notUsedCases as $index => $notUsedCase) {
            yield $index => [
                $notUsedCase,
                ['$a' => 3],
                null,
                5,
            ];
        }
    }

    public function testNotVariableIndex()
    {
        $analyzer = new VariableAnalyzer();
        $tokens = Tokens::fromCode(
            '<?php
            $a = 1;
            $b = 2;
            echo $a.$b;
            '
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Not a variable at 10.');

        $analyzer->filterVariablePossiblyUsed($tokens, 0, \count($tokens), ['$a' => 2, '$b' => 9 + 1]);
    }
}
