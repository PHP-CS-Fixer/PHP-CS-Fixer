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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Preg;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\WhitespaceBeforeStatementEndFixer
 */
final class WhitespaceBeforeStatementEndFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = [], ?int $minPhpVersion = null): void
    {
        if (null !== $input && null !== $minPhpVersion && \PHP_VERSION_ID < $minPhpVersion) {
            $expected = $input;
            $input = null;
        }

        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCases
     */
    public function testFixWithTabs(string $expected, ?string $input = null, array $configuration = [], ?int $minPhpVersion = null): void
    {
        $expected = $this->toIndentWithTabs($expected);
        if (null !== $input) {
            $input = $this->toIndentWithTabs($input);
        }

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t"));
        $this->testFix($expected, $input, $configuration, $minPhpVersion);
    }

    public function provideFixCases()
    {
        return [
            'comma_remove_space_single_line' => [
                '<?php
return [
    $foo->bar(),
];
',
                '<?php
return [
    $foo->bar()    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_one_call' => [
                '<?php
return [
    $foo
        ->bar(),
];
',
                '<?php
return [
    $foo
        ->bar()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_one_static_call' => [
                '<?php
return [
    Foo
        ::bar(),
];
',
                '<?php
return [
    Foo
        ::bar()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_one_call_with_instantiation' => [
                '<?php
return [
    (new Foo())
        ->bar(),
];
',
                '<?php
return [
    (new Foo())
        ->bar()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_two_calls' => [
                '<?php
return [
    $foo
        ->bar()
        ->baz(),
];
',
                '<?php
return [
    $foo
        ->bar()
        ->baz()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_two_calls_one_static' => [
                '<?php
return [
    Foo
        ::bar()
        ->baz(),
];
',
                '<?php
return [
    Foo
        ::bar()
        ->baz()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_two_calls_with_instantiation' => [
                '<?php
return [
    (new Foo())
        ->bar()
        ->baz(),
];
',
                '<?php
return [
    (new Foo())
        ->bar()
        ->baz()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_remove_space_for_two_calls_explicit_config' => [
                '<?php
return [
    $foo
        ->bar()
        ->baz(),
];
',
                '<?php
return [
    $foo
        ->bar()
        ->baz()
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_move_to_newline_for_one_call' => [
                '<?php
return [
    $foo
        ->bar()
    ,
];
',
                '<?php
return [
    $foo
        ->bar(),
];
',
            ],
            'comma_move_to_newline_for_one_static_call' => [
                '<?php
return [
    Foo
        ::bar()
    ,
];
',
                '<?php
return [
    Foo
        ::bar(),
];
',
                ['comma_strategy' => 'new_line_for_multiline_statement'],
            ],
            'comma_move_to_newline_for_one_call_with_instantiation' => [
                '<?php
return [
    (new Foo())
        ->bar()
    ,
];
',
                '<?php
return [
    (new Foo())
        ->bar(),
];
',
                ['comma_strategy' => 'new_line_for_multiline_statement'],
            ],
            'comma_move_to_newline_for_two_calls' => [
                '<?php
return [
    $foo
        ->bar()
        ->baz()
    ,
];
',
                '<?php
return [
    $foo
        ->bar()
        ->baz(),
];
',
                ['comma_strategy' => 'new_line_for_multiline_statement'],
            ],
            'comma_move_to_newline_for_two_calls_one_static' => [
                '<?php
return [
    Foo
        ::bar()
        ->baz()
    ,
];
',
                '<?php
return [
    Foo
        ::bar()
        ->baz(),
];
',
            ],
            'comma_move_to_newline_for_two_calls_with_instantiation' => [
                '<?php
return [
    (new Foo())
        ->bar()
        ->baz()
    ,
];
',
                '<?php
return [
    (new Foo())
        ->bar()
        ->baz(),
];
',
            ],
            'comma_move_to_newline_for_two_calls_with_comment' => [
                '<?php
return [
    $foo
        ->bar()
        ->baz() // foo
    ,
];
',
                '<?php
return [
    $foo
        ->bar()
        ->baz(), // foo
];
',
            ],
            'comma_dont_remove_for_heredoc_and_nowdoc' => [
                '<?php
return [
    <<<HEREDOC
foo
HEREDOC,
    <<<NOWDOC
foo
NOWDOC,
];
',
                '<?php
return [
    <<<HEREDOC
foo
HEREDOC
    ,
    <<<NOWDOC
foo
NOWDOC
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
                70300,
            ],
            'comma_move_to_newline_with_comment' => [
                '<?php
return [
    $foo
        ->bar()
        ->baz() // foo
,
];
',
                '<?php
return [
    $foo
        ->bar()
        ->baz() // foo
    ,
];
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_move_to_newline_for_two_calls_6_spaces_indent' => [
                '<?php
return [
      $foo
            ->bar()
            ->baz()
      ,
];
',
                '<?php
return [
      $foo
            ->bar()
            ->baz(),
];
',
            ],
            'comma_remove_space_in_function_call' => [
                '<?php
foo(
    $foo
        ->bar()
        ->baz(),
    $foo
        ->bar()
        ->baz()
);
',
                '<?php
foo(
    $foo
        ->bar()
        ->baz()
    ,
    $foo
        ->bar()
        ->baz()
);
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_move_to_newline_in_function_call' => [
                '<?php
foo(
    $foo
        ->bar()
        ->baz()
    ,
    $foo
        ->bar()
        ->baz()
);
',
                '<?php
foo(
    $foo
        ->bar()
        ->baz(),
    $foo
        ->bar()
        ->baz()
);
',
            ],
            'comma_remove_space_in_instantiation' => [
                '<?php
new Foo(
    $foo
        ->bar()
        ->baz(),
    $foo
        ->bar()
        ->baz()
);
',
                '<?php
new Foo(
    $foo
        ->bar()
        ->baz()
    ,
    $foo
        ->bar()
        ->baz()
);
',
                ['comma_strategy' => 'no_whitespace'],
            ],
            'comma_move_to_newline_in_instantiation' => [
                '<?php
new Foo(
    $foo
        ->bar()
        ->baz()
    ,
    $foo
        ->bar()
        ->baz()
);
',
                '<?php
new Foo(
    $foo
        ->bar()
        ->baz(),
    $foo
        ->bar()
        ->baz()
);
',
            ],
            'semicolon_remove_space_for_one_call' => [
                '<?php
return $foo
    ->bar();
',
                '<?php
return $foo
    ->bar()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_remove_space_for_one_static_call' => [
                '<?php
return Foo
    ::bar();
',
                '<?php
return Foo
    ::bar()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_remove_space_for_one_call_with_instantiation' => [
                '<?php
return (new Foo())
    ->bar();
',
                '<?php
return (new Foo())
    ->bar()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_remove_space_for_two_calls' => [
                '<?php
return $foo
    ->bar()
    ->baz();
',
                '<?php
return $foo
    ->bar()
    ->baz()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_remove_space_for_two_calls_one_static' => [
                '<?php
return Foo
    ::bar()
    ->baz();
',
                '<?php
return Foo
    ::bar()
    ->baz()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_remove_space_for_two_calls_with_instantiation' => [
                '<?php
return (new Foo())
    ->bar()
    ->baz();
',
                '<?php
return (new Foo())
    ->bar()
    ->baz()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_remove_space_for_two_calls_explicit_config' => [
                '<?php
return $foo
    ->bar()
    ->baz();
',
                '<?php
return $foo
    ->bar()
    ->baz()
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
            'semicolon_move_to_newline_for_one_call' => [
                '<?php
return $foo
    ->bar()
;
',
                '<?php
return $foo
    ->bar();
',
            ],
            'semicolon_move_to_newline_for_one_static_call' => [
                '<?php
return Foo
    ::bar()
;
',
                '<?php
return Foo
    ::bar();
',
                ['semicolon_strategy' => 'new_line_for_multiline_statement'],
            ],
            'semicolon_move_to_newline_for_one_call_with_instantiation' => [
                '<?php
return (new Foo())
    ->bar()
;
',
                '<?php
return (new Foo())
    ->bar();
',
                ['semicolon_strategy' => 'new_line_for_multiline_statement'],
            ],
            'semicolon_move_to_newline_for_two_calls' => [
                '<?php
return $foo
    ->bar()
    ->baz()
;
',
                '<?php
return $foo
    ->bar()
    ->baz();
',
            ],
            'semicolon_move_to_newline_for_two_calls_one_static' => [
                '<?php
return Foo
    ::bar()
    ->baz()
;
',
                '<?php
return Foo
    ::bar()
    ->baz();
',
            ],
            'semicolon_move_to_newline_for_two_calls_with_instantiation' => [
                '<?php
return (new Foo())
    ->bar()
    ->baz()
;
',
                '<?php
return (new Foo())
    ->bar()
    ->baz();
',
            ],
            'semicolon_move_to_newline_for_two_calls_with_comment' => [
                '<?php
return $foo
    ->bar()
    ->baz() // foo
;
',
                '<?php
return $foo
    ->bar()
    ->baz(); // foo
',
            ],
            'semicolon_move_to_newline_with_comment' => [
                '<?php
return $foo
    ->bar()
    ->baz() // foo
;
',
                '<?php
return $foo
    ->bar()
    ->baz() // foo
    ;
',
                ['semicolon_strategy' => 'no_whitespace'],
            ],
        ];
    }

    private function toIndentWithTabs($php)
    {
        return Preg::replaceCallback('/^ +/m', function (array $matches) {
            $length = \strlen($matches[0]);

            return str_repeat("\t", (int) floor($length / 4)).str_repeat(' ', $length % 4);
        }, $php);
    }
}
