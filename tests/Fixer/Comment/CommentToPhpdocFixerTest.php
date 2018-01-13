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

namespace PhpCsFixer\Tests\Fixer\Comment;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\CommentToPhpdocFixer
 */
final class CommentToPhpdocFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestCases()
    {
        return [
            [
                '<?php /* string $foo */',
            ],
            [
                '<?php /* $yoda string @var */',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php /* @var string $foo */',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php /*** @var string $foo */',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php // @var string $foo',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php //@var string $foo',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php # @var string $foo',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php #@var string $foo',
            ],
            [
                '<?php /** @var string $foo */',
                '<?php #@var string $foo',
            ],
            [
                <<<'EOT'
<?php

/**
 * @var string $foo
 */
EOT
                ,
                <<<'EOT'
<?php

/*
 * @var string $foo
 */
EOT
                ,
            ],
            [
                <<<'EOT'
<?php

/**
 * @Column(type="string", length=32, unique=true, nullable=false)
 */
EOT
                ,
                <<<'EOT'
<?php

/*
 * @Column(type="string", length=32, unique=true, nullable=false)
 */
EOT
                ,
            ],
            [
                <<<'EOT'
<?php

/**
 * @ORM\Column(name="id", type="integer")
 */
EOT
                ,
                <<<'EOT'
<?php

/*
 * @ORM\Column(name="id", type="integer")
 */
EOT
                ,
            ],
        ];
    }
}
