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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer
 */
final class GeneralPhpdocTagRenameFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $configuration
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $configuration = []): void
    {
        $this->fixer->configure($configuration);

        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): array
    {
        return [
            [
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
            ],
            [
                '<?php
    /**
     * @inheritDoc
     * @inheritDoc
     * {@inheritDoc}
     * {@inheritDoc}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                [
                    'replacements' => ['inheritDocs' => 'inheritDoc'],
                ],
            ],
            [
                '<?php
    /**
     * @inheritdoc
     * @inheritdoc
     * {@inheritdoc}
     * {@inheritdoc}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                [
                    'fix_annotation' => true,
                    'fix_inline' => true,
                    'replacements' => ['inheritdocs' => 'inheritdoc'],
                    'case_sensitive' => false,
                ],
            ],
            [
                '<?php
    /**
     * @inheritDoc
     * @inheritDoc
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                [
                    'fix_inline' => false,
                    'replacements' => ['inheritDocs' => 'inheritDoc'],
                ],
            ],
            [
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritDoc}
     * {@inheritDoc}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                [
                    'fix_annotation' => false,
                    'replacements' => ['inheritDocs' => 'inheritDoc'],
                ],
            ],
            [
                '<?php
    /**
     * @inheritdocs
     * @inheritDoc
     * {@inheritdocs}
     * {@inheritDoc}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                [
                    'case_sensitive' => true,
                    'replacements' => ['inheritDocs' => 'inheritDoc'],
                ],
            ],
            [
                '<?php
    /**
     * @inheritdoc
     * @inheritdoc
     * {@inheritdoc}
     * {@inheritdoc}
     * @see Foo::bar()
     * {@see Foo::bar()}
     */',
                '<?php
    /**
     * @inheritdocs
     * @inheritDocs
     * {@inheritdocs}
     * {@inheritDocs}
     * @link Foo::bar()
     * {@link Foo::bar()}
     */',
                [
                    'replacements' => [
                        'inheritdocs' => 'inheritdoc',
                        'link' => 'see',
                    ],
                ],
            ],
            [
                '<?php
    /**
     * @var int $foo
     * @Annotation("@type")
     */',
                '<?php
    /**
     * @type int $foo
     * @Annotation("@type")
     */',
                [
                    'fix_annotation' => true,
                    'fix_inline' => true,
                    'replacements' => [
                        'type' => 'var',
                    ],
                ],
            ],
            [
                '<?php
    /**
     * @var int $foo
     * @Annotation("@type")
     */',
                '<?php
    /**
     * @type int $foo
     * @Annotation("@type")
     */',
                [
                    'fix_annotation' => true,
                    'fix_inline' => false,
                    'replacements' => [
                        'type' => 'var',
                    ],
                ],
            ],
        ];
    }

    public function testConfigureWithInvalidOption(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[general_phpdoc_tag_rename\] Invalid configuration: The option "replacements" with value true is expected to be of type "array", but is of type ".*ool.*"\.$/');

        $this->fixer->configure([
            'replacements' => true,
        ]);
    }

    public function testConfigureWithUnknownOption(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('/^\[general_phpdoc_tag_rename\] Invalid configuration: The option "foo" does not exist\. (Known|Defined) options are: "case_sensitive", "fix_annotation", "fix_inline", "replacements"\.$/');

        $this->fixer->configure([
            'foo' => true,
        ]);
    }

    /**
     * @param array<mixed> $replacements
     *
     * @dataProvider provideConfigureWithInvalidReplacementsCases
     */
    public function testConfigureWithInvalidReplacements(array $replacements, bool $caseSensitive, string $expectedMessage): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches(sprintf(
            '/^\[general_phpdoc_tag_rename\] Invalid configuration: %s$/',
            preg_quote($expectedMessage, '/')
        ));

        $this->fixer->configure([
            'replacements' => $replacements,
            'case_sensitive' => $caseSensitive,
        ]);
    }

    public static function provideConfigureWithInvalidReplacementsCases(): array
    {
        return [
            [
                [1 => 'abc'],
                true,
                'Tag to replace must be a string.',
            ],
            [
                ['a' => null],
                true,
                'Tag to replace to from "a" must be a string.',
            ],
            [
                ['see' => 'link*/'],
                true,
                'Tag "see" cannot be replaced by invalid tag "link*/".',
            ],
            [
                [
                    'link' => 'see',
                    'a' => 'b',
                    'see' => 'link',
                ],
                true,
                'Cannot change tag "link" to tag "see", as the tag "see" is configured to be replaced to "link".',
            ],
            [
                [
                    'b' => 'see',
                    'see' => 'link',
                    'link' => 'b',
                ],
                true,
                'Cannot change tag "b" to tag "see", as the tag "see" is configured to be replaced to "link".',
            ],
            [
                [
                    'see' => 'link',
                    'link' => 'b',
                ],
                true,
                'Cannot change tag "see" to tag "link", as the tag "link" is configured to be replaced to "b".',
            ],
            [
                [
                    'Foo' => 'bar',
                    'foo' => 'baz',
                ],
                false,
                'Tag "foo" cannot be configured to be replaced with several different tags when case sensitivity is off.',
            ],
        ];
    }
}
