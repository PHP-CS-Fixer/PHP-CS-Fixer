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
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTagTypeFixer
 */
final class PhpdocTagTypeFixerTest extends AbstractFixerTestCase
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

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD'
                <?php
                /**
                 * @api
                 * @author
                 * @copyright
                 * @deprecated
                 * @example
                 * @global
                 * @inheritDoc
                 * @internal
                 * @license
                 * @method
                 * @package
                 * @param
                 * @property
                 * @return
                 * @see
                 * @since
                 * @throws
                 * @todo
                 * @uses
                 * @var
                 * @version
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * {@api}
                 * {@author}
                 * {@copyright}
                 * {@deprecated}
                 * {@example}
                 * {@global}
                 * {@inheritDoc}
                 * {@internal}
                 * {@license}
                 * {@method}
                 * {@package}
                 * {@param}
                 * {@property}
                 * {@return}
                 * {@see}
                 * {@since}
                 * {@throws}
                 * {@todo}
                 * {@uses}
                 * {@var}
                 * {@version}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @api
                 * @author
                 * @copyright
                 * @deprecated
                 * @example
                 * @global
                 * @inheritDoc
                 * @internal
                 * @license
                 * @method
                 * @package
                 * @param
                 * @property
                 * @return
                 * @see
                 * @since
                 * @throws
                 * @todo
                 * @uses
                 * @var
                 * @version
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * {@api}
                 * {@author}
                 * {@copyright}
                 * {@deprecated}
                 * {@example}
                 * {@global}
                 * {@inheritDoc}
                 * {@internal}
                 * {@license}
                 * {@method}
                 * {@package}
                 * {@param}
                 * {@property}
                 * {@return}
                 * {@see}
                 * {@since}
                 * {@throws}
                 * {@todo}
                 * {@uses}
                 * {@var}
                 * {@version}
                 */
                EOD,
            ['tags' => [
                'api' => 'annotation',
                'author' => 'annotation',
                'copyright' => 'annotation',
                'deprecated' => 'annotation',
                'example' => 'annotation',
                'global' => 'annotation',
                'inheritDoc' => 'annotation',
                'internal' => 'annotation',
                'license' => 'annotation',
                'method' => 'annotation',
                'package' => 'annotation',
                'param' => 'annotation',
                'property' => 'annotation',
                'return' => 'annotation',
                'see' => 'annotation',
                'since' => 'annotation',
                'throws' => 'annotation',
                'todo' => 'annotation',
                'uses' => 'annotation',
                'var' => 'annotation',
                'version' => 'annotation',
            ]],
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * {@api}
                 * {@author}
                 * {@copyright}
                 * {@deprecated}
                 * {@example}
                 * {@global}
                 * {@inheritDoc}
                 * {@internal}
                 * {@license}
                 * {@method}
                 * {@package}
                 * {@param}
                 * {@property}
                 * {@return}
                 * {@see}
                 * {@since}
                 * {@throws}
                 * {@todo}
                 * {@uses}
                 * {@var}
                 * {@version}
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @api
                 * @author
                 * @copyright
                 * @deprecated
                 * @example
                 * @global
                 * @inheritDoc
                 * @internal
                 * @license
                 * @method
                 * @package
                 * @param
                 * @property
                 * @return
                 * @see
                 * @since
                 * @throws
                 * @todo
                 * @uses
                 * @var
                 * @version
                 */
                EOD,
            ['tags' => [
                'api' => 'inline',
                'author' => 'inline',
                'copyright' => 'inline',
                'deprecated' => 'inline',
                'example' => 'inline',
                'global' => 'inline',
                'inheritDoc' => 'inline',
                'internal' => 'inline',
                'license' => 'inline',
                'method' => 'inline',
                'package' => 'inline',
                'param' => 'inline',
                'property' => 'inline',
                'return' => 'inline',
                'see' => 'inline',
                'since' => 'inline',
                'throws' => 'inline',
                'todo' => 'inline',
                'uses' => 'inline',
                'var' => 'inline',
                'version' => 'inline',
            ]],
        ];

        yield [
            <<<'EOD'
                <?php
                /** @api */
                EOD,
            <<<'EOD'
                <?php
                /** {@api} */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @deprecated since version X
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * {@deprecated since version X}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * {@deprecated since version X}
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @deprecated since version X
                 */
                EOD,
            ['tags' => ['deprecated' => 'inline']],
        ];

        yield [
            <<<'EOD'
                <?php
                /** {@deprecated since version X} */
                EOD,
            <<<'EOD'
                <?php
                /** @deprecated since version X */
                EOD,
            ['tags' => ['deprecated' => 'inline']],
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @inheritDoc
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * {@inheritDoc}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @inheritdoc
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * {@inheritdoc}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * {@inheritdoc}
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @inheritdoc
                 */
                EOD,
            ['tags' => ['inheritDoc' => 'inline']],
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Some summary.
                 *
                 * {@inheritdoc}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * Some summary.
                 *
                 * Some description.
                 *
                 * {@inheritdoc}
                 *
                 * More description.
                 *
                 * @param Foo $foo
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * {@inheritdoc}
                 *
                 * More description.
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 *
                 * @inheritdoc
                 *
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 *
                 * {@inheritdoc}
                 *
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @return array{0: float, 1: int}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /** @internal Please use {@see Foo} instead */
                EOD,
            <<<'EOD'
                <?php
                /** {@internal Please use {@see Foo} instead} */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 * @internal Please use {@see Foo} instead
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 * {@internal Please use {@see Foo} instead}
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                 *
                 * @internal Please use {@see Foo} instead
                 *
                 */
                EOD,
            <<<'EOD'
                <?php
                /**
                 *
                 * {@internal Please use {@see Foo} instead}
                 *
                 */
                EOD,
        ];

        yield [
            <<<'EOD'
                <?php
                /** @internal Foo Bar {@see JsonSerializable} */
                EOD,
        ];
    }

    public function testInvalidConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageMatches('#^\[phpdoc_tag_type\] Invalid configuration: Unknown tag type "foo"\.#');

        $this->fixer->configure([
            'tags' => ['inheritDoc' => 'foo'],
        ]);
    }
}
