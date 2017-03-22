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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
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
        return array(
            array(
                array('header' => ''),
                '<?php


$a;',
                '<?php

/**
 * new
 */
$a;',
            ),
            array(
                array(
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ),
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
            ),
            array(
                array(
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'commentType' => 'PHPDoc',
                ),
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
            ),
            array(
                array(
                    'header' => 'tmp',
                    'location' => 'after_open',
                ),
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
            ),
            array(
                array(
                    'header' => 'new',
                    'commentType' => 'comment',
                ),
                '<?php

/*
 * new
 */
                    '.'
                ',
                '<?php
                    /** test */
                ',
            ),
            array(
                array(
                    'header' => 'new',
                    'commentType' => 'PHPDoc',
                ),
                '<?php

/**
 * new
 */
                    '.'
                ',
                '<?php
                    /* test */
                ',
            ),
            array(
                array(
                    'header' => 'def',
                    'commentType' => 'PHPDoc',
                ),
                '<?php

/**
 * def
 */

',
                '<?php
',
            ),
            array(
                array('header' => 'xyz'),
                '<?php

/*
 * xyz
 */

    $b;',
                '<?php
    $b;',
            ),
            array(
                array(
                    'header' => 'xyz123',
                    'separate' => 'none',
                ),
                '<?php
/*
 * xyz123
 */
    $a;',
                '<?php
    $a;',
            ),
            array(
                array(
                    'header' => 'abc',
                    'commentType' => 'PHPDoc',
                ),
                '<?php

/**
 * abc
 */

$c;',
                '<?php
$c;',
            ),
            array(
                array(
                    'header' => 'ghi',
                    'separate' => 'both',
                ),
                '<?php

/*
 * ghi
 */

$d;',
                '<?php
$d;',
             ),
            array(
                array(
                    'header' => 'ghi',
                    'separate' => 'top',
                ),
                '<?php

/*
 * ghi
 */
$d;',
                '<?php
$d;',
            ),
            array(
                array(
                    'header' => 'tmp',
                    'location' => 'after_declare_strict',
                ),
                '<?php

/*
 * tmp
 */

declare(ticks=1);

echo 1;',
                '<?php
declare(ticks=1);

echo 1;',
            ),
            array(
                array('header' => 'Foo'),
                '<?php

/*
 * Foo
 */

echo \'bar\';',
                '<?php echo \'bar\';',
            ),
        );
    }

    public function testDefaultConfiguration()
    {
        $method = new \ReflectionMethod($this->fixer, 'parseConfiguration');
        $method->setAccessible(true);
        $this->assertSame(
            array(
                "/*\n * a\n */",
                HeaderCommentFixer::HEADER_COMMENT,
                HeaderCommentFixer::HEADER_LOCATION_AFTER_DECLARE_STRICT,
                HeaderCommentFixer::HEADER_LINE_SEPARATION_BOTH,
            ),
            $method->invoke($this->fixer, array('header' => 'a'))
        );
    }

    /**
     * @param null|array $configuration
     * @param string     $exceptionMessage
     *
     * @dataProvider provideMisconfiguration
     */
    public function testMisconfiguration($configuration, $exceptionMessage)
    {
        $exceptionMatch = false;
        try {
            $this->fixer->configure($configuration);
        } catch (InvalidFixerConfigurationException $e) {
            $this->assertSame('[header_comment] '.$exceptionMessage, $e->getMessage());
            $exceptionMatch = true;
        }

        $this->assertTrue($exceptionMatch, sprintf('Expected InvalidFixerConfigurationException with message \"%s\" was not thrown.', $exceptionMessage));
    }

    public function provideMisconfiguration()
    {
        return array(
            array(null, 'Configuration is required.'),
            array(array(), 'Configuration is required.'),
            array(array('header' => 1), 'Header configuration is invalid. Expected "string", got "integer".'),
            array(
                array(
                    'header' => '',
                    'commentType' => 'foo',
                ),
                'Header type configuration is invalid, expected "PHPDoc" or "comment", got "\'foo\'".',
            ),
            array(
                array(
                    'header' => '',
                    'commentType' => new \stdClass(),
                ),
                'Header type configuration is invalid, expected "PHPDoc" or "comment", got "stdClass".',
            ),
            array(
                array(
                    'header' => '',
                    'location' => new \stdClass(),
                ),
                'Header location configuration is invalid, expected "after_open" or "after_declare_strict", got "stdClass".',
            ),
            array(
                array(
                    'header' => '',
                    'separate' => new \stdClass(),
                ),
                'Header separate configuration is invalid, expected "both", "top", "bottom" or "none", got "stdClass".',
            ),
        );
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
        $method = new \ReflectionMethod($this->fixer, 'encloseTextInComment');
        $method->setAccessible(true);
        $this->assertSame($expected, $method->invoke($this->fixer, $header, $type));
    }

    public function provideHeaderGenerationCases()
    {
        return array(
            array(
                '/*
 * a
 */',
                'a',
                HeaderCommentFixer::HEADER_COMMENT,
            ),
            array(
                '/**
 * a
 */',
                'a',
                HeaderCommentFixer::HEADER_PHPDOC,
            ),
        );
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
        $config = array('header' => '');
        $cases = array(
            array(1, '<?php #', $config),
            array(1, '<?php /**/ $bc;', $config),
            array(1, '<?php $bc;', $config),
            array(1, "<?php\n\n", $config),
            array(1, '<?php ', $config),
        );

        $config['location'] = 'after_declare_strict';
        $cases[] = array(
            8,
            '<?php
declare(strict_types=1);

namespace A\B;

echo 1;',
            $config,
        );

        $cases[] = array(
            8,
            '<?php
declare(strict_types=0);
echo 1;',
            $config,
        );

        $cases[] = array(
            1,
            '<?php
declare(strict_types=1)?>',
            $config,
        );

        return $cases;
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideDoNotTouchCases
     */
    public function testDoNotTouch($expected)
    {
        $this->doTest($expected);
    }

    public function provideDoNotTouchCases()
    {
        return array(
            array("<?php\nphpinfo();\n?>\n<?"),
            array(" <?php\nphpinfo();\n"),
            array("<?php\nphpinfo();\n?><hr/>"),
            array("  <?php\n"),
            array('<?= 1?>'),
            array('<?= 1?><?php'),
            array("<?= 1?>\n<?php"),
        );
    }

    public function testWithoutConfiguration()
    {
        $this->setExpectedException('PhpCsFixer\ConfigurationException\RequiredFixerConfigurationException');

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
        return array(
            array(
                array(
                    'header' => 'whitemess',
                    'location' => 'after_declare_strict',
                    'separate' => 'bottom',
                    'commentType' => 'PHPDoc',
                ),
                "<?php\r\ndeclare(strict_types=1);\r\n/**\r\n * whitemess\r\n */\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
                "<?php\r\ndeclare(strict_types=1);\r\n\r\nnamespace A\\B;\r\n\r\necho 1;",
            ),
        );
    }
}
