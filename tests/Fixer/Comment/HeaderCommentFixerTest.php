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
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Comment\HeaderCommentFixer
 */
final class HeaderCommentFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string $expected
     * @param string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix(array $configuration, $expected, $input)
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
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
                    'commentType' => 'PHPDoc',
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
                    'commentType' => 'comment',
                ],
                '<?php

/*
 * new
 */
                    '.'
                ',
                '<?php
                    /** test */
                ',
            ],
            [
                [
                    'header' => 'new',
                    'commentType' => 'PHPDoc',
                ],
                '<?php

/**
 * new
 */
                    '.'
                ',
                '<?php
                    /* test */
                ',
            ],
            [
                [
                    'header' => 'def',
                    'commentType' => 'PHPDoc',
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
                    'commentType' => 'PHPDoc',
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
        ];
    }

    public function testDefaultConfiguration()
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
     * @group legacy
     * @expectedDeprecation Passing NULL to set default configuration is deprecated and will not be supported in 3.0, use an empty array instead.
     */
    public function testLegacyMisconfiguration()
    {
        $this->setExpectedException(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '[header_comment] Missing required configuration: The required option "header" is missing.'
        );

        $this->fixer->configure(null);
    }

    /**
     * @param null|array $configuration
     * @param string     $exceptionMessage
     *
     * @dataProvider provideMisconfiguration
     */
    public function testMisconfiguration($configuration, $exceptionMessage)
    {
        $this->setExpectedException(
            \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class,
            '[header_comment] '.$exceptionMessage
        );

        $this->fixer->configure($configuration);
    }

    public function provideMisconfiguration()
    {
        return [
            [[], 'Missing required configuration: The required option "header" is missing.'],
            [
                ['header' => 1],
                'Invalid configuration: The option "header" with value 1 is expected to be of type "string", but is of type "integer".',
            ],
            [
                [
                    'header' => '',
                    'commentType' => 'foo',
                ],
                'Invalid configuration: The option "commentType" with value "foo" is invalid. Accepted values are: "PHPDoc", "comment".',
            ],
            [
                [
                    'header' => '',
                    'commentType' => new \stdClass(),
                ],
                'Invalid configuration: The option "commentType" with value stdClass is invalid. Accepted values are: "PHPDoc", "comment".',
            ],
            [
                [
                    'header' => '',
                    'location' => new \stdClass(),
                ],
                'Invalid configuration: The option "location" with value stdClass is invalid. Accepted values are: "after_open", "after_declare_strict".',
            ],
            [
                [
                    'header' => '',
                    'separate' => new \stdClass(),
                ],
                'Invalid configuration: The option "separate" with value stdClass is invalid. Accepted values are: "both", "top", "bottom", "none".',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $header
     * @param string $type
     *
     * @dataProvider provideHeaderGenerationCases
     */
    public function testHeaderGeneration($expected, $header, $type)
    {
        $this->fixer->configure([
            'header' => $header,
            'commentType' => $type,
        ]);
        $this->doTest(
            '<?php

'.$expected.'

echo 1;',
            '<?php
echo 1;'
        );
    }

    public function provideHeaderGenerationCases()
    {
        return [
            [
                '/*
 * a
 */',
                'a',
                'comment',
            ],
            [
                '/**
 * a
 */',
                'a',
                'PHPDoc',
            ],
        ];
    }

    /**
     * @param int    $expected
     * @param string $code
     *
     * @dataProvider provideFindHeaderCommentInsertionIndexCases
     */
    public function testFindHeaderCommentInsertionIndex($expected, $code, array $config)
    {
        Tokens::clearCache();
        $tokens = Tokens::fromCode($code);

        $this->fixer->configure($config);

        $method = new \ReflectionMethod($this->fixer, 'findHeaderCommentInsertionIndex');
        $method->setAccessible(true);
        $this->assertSame($expected, $method->invoke($this->fixer, $tokens));
    }

    public function provideFindHeaderCommentInsertionIndexCases()
    {
        $config = ['header' => ''];
        $cases = [
            [1, '<?php #', $config],
            [1, '<?php /**/ $bc;', $config],
            [1, '<?php $bc;', $config],
            [1, "<?php\n\n", $config],
            [1, '<?php ', $config],
        ];

        $config['location'] = 'after_declare_strict';
        $cases[] = [
            8,
            '<?php
declare(strict_types=1);

namespace A\B;

echo 1;',
            $config,
        ];

        $cases[] = [
            8,
            '<?php
declare(strict_types=0);
echo 1;',
            $config,
        ];

        $cases[] = [
            1,
            '<?php
declare(strict_types=1)?>',
            $config,
        ];

        return $cases;
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideDoNotTouchCases
     */
    public function testDoNotTouch($expected)
    {
        $this->fixer->configure([
            'header' => '',
        ]);

        $this->doTest($expected);
    }

    public function provideDoNotTouchCases()
    {
        return [
            ["<?php\nphpinfo();\n?>\n<?"],
            [" <?php\nphpinfo();\n"],
            ["<?php\nphpinfo();\n?><hr/>"],
            ["  <?php\n"],
            ['<?= 1?>'],
            ['<?= 1?><?php'],
            ["<?= 1?>\n<?php"],
        ];
    }

    public function testWithoutConfiguration()
    {
        $this->setExpectedException(\PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException::class);

        $this->doTest('<?php echo 1;');
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(array $configuration, $expected, $input = null)
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public function provideMessyWhitespacesCases()
    {
        return [
            [
                [
                    'header' => 'whitemess',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'commentType' => 'PHPDoc',
                ],
                "<?php\r\ndeclare(strict_types=1);\r\n/**\r\n * whitemess\r\n */\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
                "<?php\r\ndeclare(strict_types=1);\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
            ],
        ];
    }

    public function testConfigurationUpdatedWithWhitespsacesConfig()
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
}
