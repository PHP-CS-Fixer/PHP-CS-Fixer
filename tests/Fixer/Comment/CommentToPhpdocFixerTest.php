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
     * @param array<string, mixed> $config
     *
     * @dataProvider provideTestCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideTestCases(): array
    {
        return [
            [
                '<?php /* header comment */ $foo = true; /* string $bar */ $bar = "baz";',
            ],
            [
                '<?php /* header comment */ $foo = true; /* $yoda string @var */',
            ],
            [
                '<?php /* header comment */ $foo = true; /* $yoda @var string */',
            ],
            [
                '<?php /* header comment */ $foo = true; /** @var string $bar */ $bar = "baz";',
                '<?php /* header comment */ $foo = true; /* @var string $bar */ $bar = "baz";',
            ],
            [
                '<?php /* header comment */ $foo = true; /** @var string $bar */ $bar = "baz";',
                '<?php /* header comment */ $foo = true; /*@var string $bar */ $bar = "baz";',
            ],
            [
                '<?php /* header comment */ $foo = true;
                /** @var string $bar */
                $bar = "baz";
                ',
                '<?php /* header comment */ $foo = true;
                /*** @var string $bar */
                $bar = "baz";
                ',
            ],
            [
                '<?php /* header comment */ $foo = true;
                /** @var string $bar */
                $bar = "baz";
                ',
                '<?php /* header comment */ $foo = true;
                // @var string $bar
                $bar = "baz";
                ',
            ],
            [
                '<?php /* header comment */ $foo = true;
                /** @var string $bar */
                $bar = "baz";
                ',
                '<?php /* header comment */ $foo = true;
                //@var string $bar
                $bar = "baz";
                ',
            ],
            [
                '<?php /* header comment */ $foo = true;
                /** @var string $bar */
                $bar = "baz";
                ',
                '<?php /* header comment */ $foo = true;
                # @var string $bar
                $bar = "baz";
                ',
            ],
            [
                '<?php /* header comment */ $foo = true;
                /** @var string $bar */
                $bar = "baz";
                ',
                '<?php /* header comment */ $foo = true;
                #@var string $bar
                $bar = "baz";
                ',
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * @var string $bar
 */
$bar = "baz";
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

/*
 * @var string $bar
 */
$bar = "baz";
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * This is my var
 * @var string $foo
 * stop using it
 * @deprecated since 1.2
 */
$foo = 1;
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

// This is my var
// @var string $foo
// stop using it
// @deprecated since 1.2
$foo = 1;
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

for (;;) {
    /**
     * This is my var
     * @var string $foo
     */
    $foo = someValue();
}
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

for (;;) {
    // This is my var
    // @var string $foo
    $foo = someValue();
}
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * This is my var
 * @var string $foo
 * stop using it
 * @deprecated since 1.3
 */
$foo = 1;
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

# This is my var
# @var string $foo
# stop using it
# @deprecated since 1.3
$foo = 1;
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * @Column(type="string", length=32, unique=true, nullable=false)
 */
$bar = 'baz';
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

/*
 * @Column(type="string", length=32, unique=true, nullable=false)
 */
$bar = 'baz';
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * @ORM\Column(name="id", type="integer")
 */
$bar = 42;
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

/*
 * @ORM\Column(name="id", type="integer")
 */
$bar = 42;
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

// This is my var
// /** @var string $foo */
$foo = 1;
EOT
                ,
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

// @todo do something later
$foo = 1;
EOT
                ,
                null,
                ['ignored_tags' => ['todo']],
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

// @TODO do something later
$foo = 1;
EOT
                ,
                null,
                ['ignored_tags' => ['todo']],
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * @todo do something later
 * @var int $foo
 */
$foo = 1;
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

// @todo do something later
// @var int $foo
$foo = 1;
EOT
                ,
                ['ignored_tags' => ['todo']],
            ],
            [
                <<<'EOT'
<?php /* header comment */ $foo = true;

/**
 * @var int $foo
 * @todo do something later
 */
$foo = 1;
EOT
                ,
                <<<'EOT'
<?php /* header comment */ $foo = true;

// @var int $foo
// @todo do something later
$foo = 1;
EOT
                ,
                ['ignored_tags' => ['todo']],
            ],
            [
                '<?php // header
                /** /@foo */
                namespace Foo\Bar;
',
                '<?php // header
                ///@foo
                namespace Foo\Bar;
',
            ],
            [
                '<?php // header
                /**
                 * / @foo
                 * / @bar
                 */
                namespace Foo\Bar;
',
                '<?php // header
                /// @foo
                /// @bar
                namespace Foo\Bar;
',
            ],
            [
                '<?php /* header comment */ $foo = true; class Foo { /** @phpstan-use Bar<Baz> $bar */ use Bar; }',
                '<?php /* header comment */ $foo = true; class Foo { /* @phpstan-use Bar<Baz> $bar */ use Bar; }',
            ],
        ];
    }
}
