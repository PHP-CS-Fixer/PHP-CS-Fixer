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
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $configuration = null)
    {
        if (null !== $configuration) {
            $this->fixer->configure($configuration);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php
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
 */',
                '<?php
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
 */',
            ],
            [
                '<?php
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
 */',
                '<?php
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
 */',
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
            ],
            [
                '<?php
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
 */',
                '<?php
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
 */',
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
            ],
            [
                '<?php
/** @api */',
                '<?php
/** {@api} */',
            ],
            [
                '<?php
/**
 * @deprecated since version X
 */',
                '<?php
/**
 * {@deprecated since version X}
 */',
            ],
            [
                '<?php
/**
 * {@deprecated since version X}
 */',
                '<?php
/**
 * @deprecated since version X
 */',
                ['tags' => ['deprecated' => 'inline']],
            ],
            [
                '<?php
/** {@deprecated since version X} */',
                '<?php
/** @deprecated since version X */',
                ['tags' => ['deprecated' => 'inline']],
            ],
            [
                '<?php
/**
 * @inheritDoc
 */',
                '<?php
/**
 * {@inheritDoc}
 */',
            ],
            [
                '<?php
/**
 * @inheritdoc
 */',
                '<?php
/**
 * {@inheritdoc}
 */',
            ],
            [
                '<?php
/**
 * {@inheritdoc}
 */',
                '<?php
/**
 * @inheritdoc
 */',
                ['tags' => ['inheritDoc' => 'inline']],
            ],
            [
                '<?php
/**
 * Some summary.
 *
 * {@inheritdoc}
 */',
            ],
            [
                '<?php
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
 */',
            ],
            [
                '<?php
/**
 * {@inheritdoc}
 *
 * More description.
 */',
            ],
            [
                '<?php
/**
 *
 * @inheritdoc
 *
 */',
                '<?php
/**
 *
 * {@inheritdoc}
 *
 */',
            ],
        ];
    }

    public function testConfigureWithInvalidTagType()
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('#^\[phpdoc_tag_type\] Invalid configuration: Unknown tag type "foo"\.#');

        $this->fixer->configure([
            'tags' => ['inheritDoc' => 'foo'],
        ]);
    }
}
