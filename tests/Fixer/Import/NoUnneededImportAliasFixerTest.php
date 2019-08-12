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

namespace PhpCsFixer\Tests\Fixer\Import;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer
 */
final class NoUnneededImportAliasFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            'simple' => [
                <<<'EOF'
<?php

use Foo\Bar\FooBar;
use Foo\Bar\Baz as Alias;
use const some\a\ConstA;
use function some\a\fn_b;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar\FooBar as FooBar;
use Foo\Bar\Baz as Alias;
use const some\a\ConstA as ConstA;
use function some\a\fn_b as fn_b;
EOF
            ],
            'comments' => [
                <<<'EOF'
<?php

use Foo\Bar\BazEnd/* comment */;
use Foo\Bar\BazEndWithSpace  /* comment */ ;
use Foo\Bar\BazAfterAs/* comment */;
use Foo\Bar\BazAfterAsWithSpace  /* comment */ ;
use Foo\Bar\BazBeforeAs/* comment */;
use Foo\Bar\BazBeforeAsWithSpace  /* comment */  ;
use/* comment */Foo\Bar\BazAfterUse;
use  /* comment */  Foo\Bar\BazAfterUseWithSpace;
use Foo\Bar\BazAfterTricky  // comment
  ;
use const some\a\ConstA/* comment */;
use function some\a\fn_b /* comment */ ;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar\BazEnd as BazEnd/* comment */;
use Foo\Bar\BazEndWithSpace as BazEndWithSpace  /* comment */ ;
use Foo\Bar\BazAfterAs as/* comment */BazAfterAs;
use Foo\Bar\BazAfterAsWithSpace as  /* comment */ BazAfterAsWithSpace;
use Foo\Bar\BazBeforeAs/* comment */as BazBeforeAs;
use Foo\Bar\BazBeforeAsWithSpace  /* comment */  as BazBeforeAsWithSpace;
use/* comment */Foo\Bar\BazAfterUse as BazAfterUse;
use  /* comment */  Foo\Bar\BazAfterUseWithSpace as BazAfterUseWithSpace;
use Foo\Bar\BazAfterTricky as  // comment
  BazAfterTricky ;
use const some\a\ConstA as ConstA/* comment */;
use function some\a\fn_b /* comment */ as fn_b;
EOF
            ],
            'case sensitive' => [
                <<<'EOF'
<?php

use Foo\Bar\FooBar as fooBar;
use const some\a\ConstA as Consta;
use function some\a\fn_b as fn_B;
EOF
            ],
            'extra spaces' => [
                <<<'EOF'
<?php

use Foo\Bar\FooBar;
use const some\a\ConstA;
use function some\a\fn_b;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar\FooBar  as FooBar  ;
use const some\a\ConstA as   ConstA   ;
use function some\a\fn_b   as   fn_b ;
EOF
            ],
            'root import' => [
                <<<'EOF'
<?php

use FooBarFooBar;
use const someConstA;
use function someFunction;
EOF
                ,
                <<<'EOF'
<?php

use FooBarFooBar as FooBarFooBar;
use const someConstA as someConstA;
use function someFunction as someFunction;
EOF
            ],
            'leading slash' => [
                <<<'EOF'
<?php

use \Foo\Bar\FooBar;
use const \some\a\ConstA;
use function \some\a\fn_b;
EOF
                ,
                <<<'EOF'
<?php

use \Foo\Bar\FooBar as FooBar;
use const \some\a\ConstA as ConstA;
use function \some\a\fn_b as fn_b;
EOF
            ],
        ];
    }
}
