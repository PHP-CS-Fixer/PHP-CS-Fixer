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
            'multiple use' => [
                <<<'EOF'
<?php

use Foo\Bar\FooBar, Foo\Bar\Baz as Alias;
use const some\a\ConstB as Alias, some\a\ConstA;
use function some\a\fn_b, some\a\fn_c;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar\FooBar as FooBar, Foo\Bar\Baz as Alias;
use const some\a\ConstB as Alias, some\a\ConstA as ConstA;
use function some\a\fn_b as fn_b, some\a\fn_c as fn_c;
EOF
            ],
            'comments' => [
                <<<'EOF'
<?php

use Foo\Bar\BazEnd/**/;
use Foo\Bar\BazEndWithSpace  /**/ ;
use Foo\Bar\BazAfterAs/**/;
use Foo\Bar\BazAfterAsWithSpace  /**/ ;
use Foo\Bar\BazBeforeAs/**/;
use Foo\Bar\BazBeforeAsWithSpace  /**/  ;
use/**/Foo\Bar\BazAfterUse;
use  /**/  Foo\Bar\BazAfterUseWithSpace;
use Foo\Bar\Multiple  /**/ /**/,/**/Foo\Bar\Second;
use Foo\Bar\BazAfterTricky  // comment
  ;
use const some\a\ConstA/**/;
use function some\a\fn_b /**/ ;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar\BazEnd as BazEnd/**/;
use Foo\Bar\BazEndWithSpace as BazEndWithSpace  /**/ ;
use Foo\Bar\BazAfterAs as/**/BazAfterAs;
use Foo\Bar\BazAfterAsWithSpace as  /**/ BazAfterAsWithSpace;
use Foo\Bar\BazBeforeAs/**/as BazBeforeAs;
use Foo\Bar\BazBeforeAsWithSpace  /**/  as BazBeforeAsWithSpace;
use/**/Foo\Bar\BazAfterUse as BazAfterUse;
use  /**/  Foo\Bar\BazAfterUseWithSpace as BazAfterUseWithSpace;
use Foo\Bar\Multiple as  /**/ Multiple/**/,/**/Foo\Bar\Second as Second;
use Foo\Bar\BazAfterTricky as  // comment
  BazAfterTricky ;
use const some\a\ConstA as ConstA/**/;
use function some\a\fn_b /**/ as fn_b;
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
            'close_tag_1' => [
                '<?php
     use B\C ?>inline content<?php use A\D; use E\F ?>',
                '<?php
     use B\C as C ?>inline content<?php use A\D; use E\F as F ?>',
            ],
            'close_tag_2' => [
                '<?php use A\B;?>',
                '<?php use A\B as B;?>',
            ],
            'close_tag_3' => [
                '<?php use A\B?>',
                '<?php use A\B as B?>',
            ],
        ];
    }
}
