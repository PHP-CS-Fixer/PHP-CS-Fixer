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
 * @covers \PhpCsFixer\Fixer\Import\NoUnneededAliasFixer
 */
final class NoUnneededAliasFixerTest extends AbstractFixerTestCase
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
use const some\a\ConstA;
use function some\a\fn_b;
EOF
                ,
                <<<'EOF'
<?php

use Foo\Bar\FooBar as FooBar;
use const some\a\ConstA as ConstA;
use function some\a\fn_b as fn_b;
EOF
            ],
            'comments' => [
                <<<'EOF'
<?php

use Foo\Bar\FooBar as /* idem */FooBar;
use /* - */const some\a\ConstA as ConstA;
use function some\a\fn_b /* - */as fn_b;
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
        ];
    }
}
