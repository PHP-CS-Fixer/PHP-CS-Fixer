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

    public function provideFixCases(): array
    {
        return [
            [
                ['header' => ''],
                '<?php

$a;',
                '<?php

/**
 * new
 */
$a;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ],
                '<?php
declare(strict_types=1);

/*
 * tmp
 */

namespace A\B;

echo 1;',
                '<?php
declare(strict_types=1);namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php
declare(strict_types=1);
/**
 * tmp
 */

namespace A\B;

echo 1;',
                '<?php
declare(strict_types=1);

namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_open',
                ],
                '<?php

/*
 * tmp
 */

declare(strict_types=1);

namespace A\B;

echo 1;',
                '<?php
declare(strict_types=1);

namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'new',
                    'comment_type' => HeaderCommentFixer::HEADER_COMMENT,
                ],
                '<?php

/*
 * new
 */
                ',
                '<?php
                    /** test */
                ',
            ],
            [
                [
                    'header' => 'new',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php

/**
 * new
 */
                ',
                '<?php
                    /* test */
                ',
            ],
            [
                [
                    'header' => 'def',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php

/**
 * def
 */
',
                '<?php
',
            ],
            [
                ['header' => 'xyz'],
                '<?php

/*
 * xyz
 */

    $b;',
                '<?php
    $b;',
            ],
            [
                [
                    'header' => 'xyz123',
                    'separate' => 'none',
                ],
                '<?php
/*
 * xyz123
 */
    $a;',
                '<?php
    $a;',
            ],
            [
                [
                    'header' => 'abc',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php

/**
 * abc
 */

$c;',
                '<?php
$c;',
            ],
            [
                [
                    'header' => 'ghi',
                    'separate' => 'both',
                ],
                '<?php

/*
 * ghi
 */

$d;',
                '<?php
$d;',
            ],
            [
                [
                    'header' => 'ghi',
                    'separate' => 'top',
                ],
                '<?php

/*
 * ghi
 */
$d;',
                '<?php
$d;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ],
                '<?php

/*
 * tmp
 */

declare(ticks=1);

echo 1;',
                '<?php
declare(ticks=1);

echo 1;',
            ],
            [
                ['header' => 'Foo'],
                '<?php

/*
 * Foo
 */

echo \'bar\';',
                '<?php echo \'bar\';',
            ],
            [
                ['header' => 'x'],
                '<?php

/*
 * x
 */

echo \'a\';',
                '<?php

/*
 * y
 * z
 */

echo \'a\';',
            ],
            [
                ['header' => "a\na"],
                '<?php

/*
 * a
 * a
 */

echo \'x\';',
                '<?php


/*
 * b
 * c
 */


echo \'x\';',
            ],
            [
                [
                    'header' => 'foo',
                    'location' => 'after_open',
                    'separate' => 'bottom',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php
/**
 * foo
 */

declare(strict_types=1);

namespace A;

echo 1;',
                '<?php

declare(strict_types=1);
/**
 * foo
 */

namespace A;

echo 1;',
            ],
            [
                [
                    'header' => 'foo',
                    'location' => 'after_open',
                    'separate' => 'bottom',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php
/**
 * foo
 */

declare(strict_types=1);
/**
 * bar
 */

namespace A;

echo 1;',
                '<?php

declare(strict_types=1);
/**
 * bar
 */

namespace A;

echo 1;',
            ],
            [
                [
                    'header' => 'Foo',
                    'separate' => 'none',
                ],
                '<?php

declare(strict_types=1);
/*
 * Foo
 */
namespace SebastianBergmann\Foo;

class Bar
{
}',
                '<?php
/*
 * Foo
 */

declare(strict_types=1);

namespace SebastianBergmann\Foo;

class Bar
{
}',
            ],
            [
                ['header' => 'tmp'],
                '<?php

/*
 * tmp
 */

/**
 * Foo class doc.
 */
class Foo {}',
                '<?php

/**
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                ['header' => 'tmp'],
                '<?php

/*
 * tmp
 */

class Foo {}',
                '<?php

/*
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'tmp',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php

/**
 * tmp
 */

/**
 * Foo class doc.
 */
class Foo {}',
                '<?php

/**
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'tmp',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                '<?php

/**
 * tmp
 */

class Foo {}',
                '<?php

/**
 * tmp
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'tmp',
                    'separate' => 'top',
                ],
                '<?php

/*
 * tmp
 */
class Foo {}',
                '<?php
/**
 * Foo class doc.
 */
class Foo {}',
            ],
            [
                [
                    'header' => 'bar',
                    'location' => 'after_open',
                ],
                '<?php

/*
 * bar
 */

declare(strict_types=1);

// foo
foo();',
                '<?php

/*
 * foo
 */

declare(strict_types=1);

// foo
foo();',
            ],
            [
                [
                    'header' => 'bar',
                    'location' => 'after_open',
                ],
                '<?php

/*
 * bar
 */

declare(strict_types=1);

/* foo */
foo();',
                '<?php

/*
 * foo
 */

declare(strict_types=1);

/* foo */
foo();',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ],
                '<?php

/*
 * tmp
 */

declare(strict_types=1) ?>',
                '<?php
declare(strict_types=1) ?>',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ],
                '#!/usr/bin/env php
<?php
declare(strict_types=1);

/*
 * tmp
 */

namespace A\B;

echo 1;',
                '#!/usr/bin/env php
<?php
declare(strict_types=1);namespace A\B;

echo 1;',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_open',
                ],
                'Short mixed file A
Hello<?php echo "World!"; ?>',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_open',
                ],
                'Short mixed file B
<?php echo "Hello"; ?>World!',
            ],
            [
                [
                    'header' => 'tmp',
                    'location' => 'after_open',
                ],
                'File with anything at the beginning and with multiple opening tags are not supported
<?php
echo 1;
?>Hello World!<?php
script_continues_here();',
            ],
        ];
    }

    public function testDefaultConfiguration(): void
    {
        $this->fixer->configure(['header' => 'a']);
        $this->doTest(
            '<?php

/*
 * a
 */

echo 1;',
            '<?php
echo 1;'
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

    public function provideMisconfigurationCases(): array
    {
        return [
            [[], 'Missing required configuration: The required option "header" is missing.'],
            [
                ['header' => 1],
                'Invalid configuration: The option "header" with value 1 is expected to be of type "string", but is of type "(int|integer)"\.',
            ],
            [
                [
                    'header' => '',
                    'comment_type' => 'foo',
                ],
                'Invalid configuration: The option "comment_type" with value "foo" is invalid\. Accepted values are: "PHPDoc", "comment"\.',
            ],
            [
                [
                    'header' => '',
                    'comment_type' => new \stdClass(),
                ],
                'Invalid configuration: The option "comment_type" with value stdClass is invalid\. Accepted values are: "PHPDoc", "comment"\.',
            ],
            [
                [
                    'header' => '',
                    'location' => new \stdClass(),
                ],
                'Invalid configuration: The option "location" with value stdClass is invalid\. Accepted values are: "after_open", "after_declare_strict"\.',
            ],
            [
                [
                    'header' => '',
                    'separate' => new \stdClass(),
                ],
                'Invalid configuration: The option "separate" with value stdClass is invalid\. Accepted values are: "both", "top", "bottom", "none"\.',
            ],
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
            '<?php

'.$expected.'

echo 1;',
            '<?php
echo 1;'
        );
    }

    public function provideHeaderGenerationCases(): array
    {
        return [
            [
                '/*
 * a
 */',
                'a',
                HeaderCommentFixer::HEADER_COMMENT,
            ],
            [
                '/**
 * a
 */',
                'a',
                HeaderCommentFixer::HEADER_PHPDOC,
            ],
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

    public function provideDoNotTouchCases(): array
    {
        return [
            ["<?php\nphpinfo();\n?>\n<?"],
            [" <?php\nphpinfo();\n"],
            ["<?php\nphpinfo();\n?><hr/>"],
            ["  <?php\n"],
            ['<?= 1?>'],
            ["<?= 1?><?php\n"],
            ["<?= 1?>\n<?php\n"],
            ["<?php\n// comment 1\n?><?php\n// comment 2\n"],
        ];
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

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                [
                    'header' => 'whitemess',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'comment_type' => HeaderCommentFixer::HEADER_PHPDOC,
                ],
                "<?php\r\ndeclare(strict_types=1);\r\n/**\r\n * whitemess\r\n */\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
                "<?php\r\ndeclare(strict_types=1);\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
            ],
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

    public function provideFix81Cases(): iterable
    {
        yield [
            ['header' => 'tmp'],
            '<?php

/*
 * tmp
 */

/**
 * Foo class doc.
 */
enum Foo {}',
            '<?php

/**
 * Foo class doc.
 */
enum Foo {}',
        ];
    }
}
