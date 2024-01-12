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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\HeaderCommentFixer
 */
final class HeaderCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(array $configuration, string $expected, ?string $input = null): void
    {
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield [
            ['header' => ''],
            <<<'EOD'
                <?php

                $a;
                EOD,
            <<<'EOD'
                <?php

                /**
                 * new
                 */
                $a;
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_declare_strict',
            ],
            <<<'EOD'
                <?php
                declare(strict_types=1);

                /*
                 * tmp
                 */

                namespace A\B;

                echo 1;
                EOD,
            <<<'EOD'
                <?php
                declare(strict_types=1);namespace A\B;

                echo 1;
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_declare_strict',
                'separate' => 'bottom',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php
                declare(strict_types=1);
                /**
                 * tmp
                 */

                namespace A\B;

                echo 1;
                EOD,
            <<<'EOD'
                <?php
                declare(strict_types=1);

                namespace A\B;

                echo 1;
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_open',
            ],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */

                declare(strict_types=1);

                namespace A\B;

                echo 1;
                EOD,
            <<<'EOD'
                <?php
                declare(strict_types=1);

                namespace A\B;

                echo 1;
                EOD,
        ];

        yield [
            [
                'header' => 'new',
                'comment_type' => HeaderCommentFixer::HEADER_COMMENT,
            ],
            <<<'EOD'
                <?php

                /*
                 * new
                 */
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    /** test */
                EOD."\n                ",
        ];

        yield [
            [
                'header' => 'new',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php

                /**
                 * new
                 */
                EOD."\n                ",
            <<<'EOD'
                <?php
                                    /* test */
                EOD."\n                ",
        ];

        yield [
            [
                'header' => 'def',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php

                /**
                 * def
                 */

                EOD,
            <<<'EOD'
                <?php

                EOD,
        ];

        yield [
            ['header' => 'xyz'],
            <<<'EOD'
                <?php

                /*
                 * xyz
                 */

                    $b;
                EOD,
            <<<'EOD'
                <?php
                    $b;
                EOD,
        ];

        yield [
            [
                'header' => 'xyz123',
                'separate' => 'none',
            ],
            <<<'EOD'
                <?php
                /*
                 * xyz123
                 */
                    $a;
                EOD,
            <<<'EOD'
                <?php
                    $a;
                EOD,
        ];

        yield [
            [
                'header' => 'abc',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php

                /**
                 * abc
                 */

                $c;
                EOD,
            <<<'EOD'
                <?php
                $c;
                EOD,
        ];

        yield [
            [
                'header' => 'ghi',
                'separate' => 'both',
            ],
            <<<'EOD'
                <?php

                /*
                 * ghi
                 */

                $d;
                EOD,
            <<<'EOD'
                <?php
                $d;
                EOD,
        ];

        yield [
            [
                'header' => 'ghi',
                'separate' => 'top',
            ],
            <<<'EOD'
                <?php

                /*
                 * ghi
                 */
                $d;
                EOD,
            <<<'EOD'
                <?php
                $d;
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_declare_strict',
            ],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */

                declare(ticks=1);

                echo 1;
                EOD,
            <<<'EOD'
                <?php
                declare(ticks=1);

                echo 1;
                EOD,
        ];

        yield [
            ['header' => 'Foo'],
            <<<'EOD'
                <?php

                /*
                 * Foo
                 */

                echo 'bar';
                EOD,
            '<?php echo \'bar\';',
        ];

        yield [
            ['header' => 'x'],
            <<<'EOD'
                <?php

                /*
                 * x
                 */

                echo 'a';
                EOD,
            <<<'EOD'
                <?php

                /*
                 * y
                 * z
                 */

                echo 'a';
                EOD,
        ];

        yield [
            ['header' => "a\na"],
            <<<'EOD'
                <?php

                /*
                 * a
                 * a
                 */

                echo 'x';
                EOD,
            <<<'EOD'
                <?php


                /*
                 * b
                 * c
                 */


                echo 'x';
                EOD,
        ];

        yield [
            [
                'header' => 'foo',
                'location' => 'after_open',
                'separate' => 'bottom',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php
                /**
                 * foo
                 */

                declare(strict_types=1);

                namespace A;

                echo 1;
                EOD,
            <<<'EOD'
                <?php

                declare(strict_types=1);
                /**
                 * foo
                 */

                namespace A;

                echo 1;
                EOD,
        ];

        yield [
            [
                'header' => 'foo',
                'location' => 'after_open',
                'separate' => 'bottom',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php
                /**
                 * foo
                 */

                declare(strict_types=1);
                /**
                 * bar
                 */

                namespace A;

                echo 1;
                EOD,
            <<<'EOD'
                <?php

                declare(strict_types=1);
                /**
                 * bar
                 */

                namespace A;

                echo 1;
                EOD,
        ];

        yield [
            [
                'header' => 'Foo',
                'separate' => 'none',
            ],
            <<<'EOD'
                <?php

                declare(strict_types=1);
                /*
                 * Foo
                 */
                namespace SebastianBergmann\Foo;

                class Bar
                {
                }
                EOD,
            <<<'EOD'
                <?php
                /*
                 * Foo
                 */

                declare(strict_types=1);

                namespace SebastianBergmann\Foo;

                class Bar
                {
                }
                EOD,
        ];

        yield [
            ['header' => 'tmp'],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */

                /**
                 * Foo class doc.
                 */
                class Foo {}
                EOD,
            <<<'EOD'
                <?php

                /**
                 * Foo class doc.
                 */
                class Foo {}
                EOD,
        ];

        yield [
            ['header' => 'tmp'],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */

                class Foo {}
                EOD,
            <<<'EOD'
                <?php

                /*
                 * Foo class doc.
                 */
                class Foo {}
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php

                /**
                 * tmp
                 */

                /**
                 * Foo class doc.
                 */
                class Foo {}
                EOD,
            <<<'EOD'
                <?php

                /**
                 * Foo class doc.
                 */
                class Foo {}
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            <<<'EOD'
                <?php

                /**
                 * tmp
                 */

                class Foo {}
                EOD,
            <<<'EOD'
                <?php

                /**
                 * tmp
                 */
                class Foo {}
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'separate' => 'top',
            ],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */
                class Foo {}
                EOD,
            <<<'EOD'
                <?php
                /**
                 * Foo class doc.
                 */
                class Foo {}
                EOD,
        ];

        yield [
            [
                'header' => 'bar',
                'location' => 'after_open',
            ],
            <<<'EOD'
                <?php

                /*
                 * bar
                 */

                declare(strict_types=1);

                // foo
                foo();
                EOD,
            <<<'EOD'
                <?php

                /*
                 * foo
                 */

                declare(strict_types=1);

                // foo
                foo();
                EOD,
        ];

        yield [
            [
                'header' => 'bar',
                'location' => 'after_open',
            ],
            <<<'EOD'
                <?php

                /*
                 * bar
                 */

                declare(strict_types=1);

                /* foo */
                foo();
                EOD,
            <<<'EOD'
                <?php

                /*
                 * foo
                 */

                declare(strict_types=1);

                /* foo */
                foo();
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_declare_strict',
            ],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */

                declare(strict_types=1) ?>
                EOD,
            <<<'EOD'
                <?php
                declare(strict_types=1) ?>
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_declare_strict',
            ],
            <<<'EOD'
                #!/usr/bin/env php
                <?php
                declare(strict_types=1);

                /*
                 * tmp
                 */

                namespace A\B;

                echo 1;
                EOD,
            <<<'EOD'
                #!/usr/bin/env php
                <?php
                declare(strict_types=1);namespace A\B;

                echo 1;
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_open',
            ],
            <<<'EOD'
                Short mixed file A
                Hello<?php echo "World!"; ?>
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_open',
            ],
            <<<'EOD'
                Short mixed file B
                <?php echo "Hello"; ?>World!
                EOD,
        ];

        yield [
            [
                'header' => 'tmp',
                'location' => 'after_open',
            ],
            <<<'EOD'
                File with anything at the beginning and with multiple opening tags are not supported
                <?php
                echo 1;
                ?>Hello World!<?php
                script_continues_here();
                EOD,
        ];
    }

    public function testDefaultConfiguration(): void
    {
        $this->fixer->configure(['header' => 'a']);
        $this->doTest(
            <<<'EOD'
                <?php

                /*
                 * a
                 */

                echo 1;
                EOD,
            <<<'EOD'
                <?php
                echo 1;
                EOD
        );
    }

    /**
     * @param null|array<string, mixed> $configuration
     *
     * @dataProvider provideMisconfigurationCases
     */
    public function testMisconfiguration(?array $configuration, string $exceptionMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches("#^\\[header_comment\\] {$exceptionMessage}$#");

        $this->fixer->configure($configuration);
    }

    public static function provideMisconfigurationCases(): iterable
    {
        yield [[], 'Missing required configuration: The required option "header" is missing.'];

        yield [
            ['header' => 1],
            'Invalid configuration: The option "header" with value 1 is expected to be of type "string", but is of type "(int|integer)"\.',
        ];

        yield [
            [
                'header' => '',
                'comment_type' => 'foo',
            ],
            'Invalid configuration: The option "comment_type" with value "foo" is invalid\. Accepted values are: "PHPDoc", "comment"\.',
        ];

        yield [
            [
                'header' => '',
                'comment_type' => new \stdClass(),
            ],
            'Invalid configuration: The option "comment_type" with value stdClass is invalid\. Accepted values are: "PHPDoc", "comment"\.',
        ];

        yield [
            [
                'header' => '',
                'location' => new \stdClass(),
            ],
            'Invalid configuration: The option "location" with value stdClass is invalid\. Accepted values are: "after_open", "after_declare_strict"\.',
        ];

        yield [
            [
                'header' => '',
                'separate' => new \stdClass(),
            ],
            'Invalid configuration: The option "separate" with value stdClass is invalid\. Accepted values are: "both", "top", "bottom", "none"\.',
        ];
    }

    /**
     * @dataProvider provideHeaderGenerationCases
     */
    public function testHeaderGeneration(string $expected, string $header, string $type): void
    {
        $this->fixer->configure([
            'header' => $header,
            'comment_type' => $type,
        ]);
        $this->doTest(
            <<<'EOD'
                <?php


                EOD.$expected.<<<'EOD'


                echo 1;
                EOD,
            <<<'EOD'
                <?php
                echo 1;
                EOD
        );
    }

    public static function provideHeaderGenerationCases(): iterable
    {
        yield [
            <<<'EOD'
                /*
                 * a
                 */
                EOD,
            'a',
            HeaderCommentFixer::HEADER_COMMENT,
        ];

        yield [
            <<<'EOD'
                /**
                 * a
                 */
                EOD,
            'a',
            HeaderCommentFixer::HEADER_PHPDOC,
        ];
    }

    /**
     * @dataProvider provideDoNotTouchCases
     */
    public function testDoNotTouch(string $expected): void
    {
        $this->fixer->configure([
            'header' => '',
        ]);

        $this->doTest($expected);
    }

    public static function provideDoNotTouchCases(): iterable
    {
        yield ["<?php\nphpinfo();\n?>\n<?"];

        yield [" <?php\nphpinfo();\n"];

        yield ["<?php\nphpinfo();\n?><hr/>"];

        yield ["  <?php\n"];

        yield ['<?= 1?>'];

        yield ["<?= 1?><?php\n"];

        yield ["<?= 1?>\n<?php\n"];

        yield ["<?php\n// comment 1\n?><?php\n// comment 2\n"];
    }

    public function testWithoutConfiguration(): void
    {
        $this->expectException(RequiredFixerConfigurationException::class);

        $this->doTest('<?php echo 1;');
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $configuration, string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure($configuration);
        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            [
                'header' => 'whitemess',
                'location' => 'after_declare_strict',
                'separate' => 'bottom',
                'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
            ],
            "<?php\r\ndeclare(strict_types=1);\r\n/**\r\n * whitemess\r\n */\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
            "<?php\r\ndeclare(strict_types=1);\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
        ];
    }

    public function testConfigurationUpdatedWithWhitespsacesConfig(): void
    {
        $this->fixer->configure(['header' => 'Foo']);
        $this->doTest(
            "<?php\n\n/*\n * Foo\n */\n\necho 1;",
            "<?php\necho 1;"
        );

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\r\n"));
        $this->doTest(
            "<?php\r\n\r\n/*\r\n * Foo\r\n */\r\n\r\necho 1;",
            "<?php\r\necho 1;"
        );

        $this->fixer->configure(['header' => 'Bar']);
        $this->doTest(
            "<?php\r\n\r\n/*\r\n * Bar\r\n */\r\n\r\necho 1;",
            "<?php\r\necho 1;"
        );

        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig('    ', "\n"));

        $this->doTest(
            "<?php\n\n/*\n * Bar\n */\n\necho 1;",
            "<?php\necho 1;"
        );
    }

    public function testInvalidHeaderConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[header_comment\] Cannot use \'\*/\' in header\.$#');

        $this->fixer->configure([
            'header' => '/** test */',
            'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
        ]);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFix81Cases
     *
     * @requires PHP 8.1
     */
    public function testFix81(array $configuration, string $expected, ?string $input = null): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFix81Cases(): iterable
    {
        yield [
            ['header' => 'tmp'],
            <<<'EOD'
                <?php

                /*
                 * tmp
                 */

                /**
                 * Foo class doc.
                 */
                enum Foo {}
                EOD,
            <<<'EOD'
                <?php

                /**
                 * Foo class doc.
                 */
                enum Foo {}
                EOD,
        ];
    }
}
