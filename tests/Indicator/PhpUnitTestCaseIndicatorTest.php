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

namespace PhpCsFixer\Tests\Indicator;

use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Indicator\PhpUnitTestCaseIndicator
 */
final class PhpUnitTestCaseIndicatorTest extends TestCase
{
    public function testIsPhpUnitClassThrowsLogicExceptionIfTokenAtIndexIsNotTClass()
    {
        $code = <<<'PHP'
<?php

class FooTest extends \PHPUnit_Framework_Testcase
{
}
PHP;

        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [
            [
                T_WHITESPACE,
            ],
        ]);
        $token = $tokens[$index];

        $indicator = new PhpUnitTestCaseIndicator();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'No T_CLASS at given index %d, got %s.',
            $index,
            $token->getName()
        ));

        $indicator->isPhpUnitClass(
            $tokens,
            $index
        );
    }

    /**
     * @dataProvider provideFailCases
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    public function testIsPhpUnitClassReturnsFalse(Tokens $tokens, $index)
    {
        $indicator = new PhpUnitTestCaseIndicator();

        $this->assertFalse($indicator->isPhpUnitClass(
            $tokens,
            $index
        ));
    }

    public function provideFailCases()
    {
        $cases = [
            'nothing' => '<?php

class Foo
{}
',
            'nothing-extends-old' => '<?php

class Foo extends \PHPUnit_Framework_TestCase
{}
',
            'nothing-extends-new' => '<?php

class Foo extends \PHPUnit\Framework\TestCase
{}
',
            'ends-with-test' => '<?php

class FooTest
{}
',
        ];

        foreach ($cases as $key => $code) {
            yield $key => $this->tokensAndClassIndexFromCode($code);
        }
    }

    /**
     * @dataProvider provideSuccessCases
     *
     * @param Tokens $tokens
     * @param int    $index
     */
    public function testIsPhpUnitClassReturnsTrue(Tokens $tokens, $index)
    {
        $indicator = new PhpUnitTestCaseIndicator();

        $this->assertTrue($indicator->isPhpUnitClass(
            $tokens,
            $index
        ));
    }

    public function provideSuccessCases()
    {
        $cases = [
            'old-in-root-namespace' => '<?php

class FooTest extends PHPUnit_Framework_TestCase
{}
',
            'old-relative-to-root-namespace' => '<?php

class FooTest extends \PHPUnit_Framework_TestCase
{}
',
            'old-imported' => '<?php

use PHPUnit_Framework_TestCase;

class FooTest extends PHPUnit_Framework_TestCase
{}
',
            'old-aliased' => '<?php

use PHPUnit_Framework_TestCase as BarCase;

class FooTest extends BarCase
{}
',
            'old-with-namespace-relative-to-root-namespace' => '<?php

namespace Foo;

class FooTest extends \PHPUnit_Framework_TestCase
{}
',
            'old-with-namespace-imported' => '<?php

namespace Foo;

use PHPUnit_Framework_TestCase;

class FooTest extends PHPUnit_Framework_TestCase
{}
',
            'old-with-namespace-aliased' => '<?php

namespace Foo;

use PHPUnit_Framework_TestCase as BarCase;

class FooTest extends BarCase
{}
',
            'new-in-root-namespace' => '<?php

class FooTest extends PHPUnit\Framework\TestCase
{}
',
            'new-relative-to-root-namespace' => '<?php

class FooTest extends \PHPUnit\Framework\TestCase
{}
',
            'new-imported-partially' => '<?php

use PHPUnit\Framework;

class FooTest extends Framework\TestCase
{}
',
            'new-imported-fully' => '<?php

use PHPUnit\Framework\TestCase;

class FooTest extends TestCase
{}
',
            'new-aliased' => '<?php

use PHPUnit\Framework\TestCase as BarCase;

class FooTest extends BarCase
{}
',
            'new-with-namespace-relative-to-root-namespace' => '<?php

namespace Foo;

class FooTest extends \PHPUnit\Framework\TestCase
{}
',
            'new-with-namespace-imported-partially' => '<?php

namespace Foo;

use PHPUnit\Framework;

class FooTest extends Framework\TestCase
{}
',
            'new-with-namespace-imported-fully' => '<?php

namespace Foo;

use PHPUnit\Framework\TestCase;

class FooTest extends TestCase
{}
',
            'new-with-namespace-aliased' => '<?php

namespace Foo;

use PHPUnit\Framework\TestCase as BarCase;

class FooTest extends BarCase
{}
',
        ];

        foreach ($cases as $key => $code) {
            yield $key => $this->tokensAndClassIndexFromCode($code);
        }
    }

    /**
     * @param string $code
     *
     * @return array
     */
    private function tokensAndClassIndexFromCode($code)
    {
        $tokens = Tokens::fromCode($code);
        $index = $tokens->getNextTokenOfKind(0, [
            [
                T_CLASS,
            ],
        ]);

        return [
            $tokens,
            $index,
        ];
    }
}
