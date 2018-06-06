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

namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractPhpdocTypesFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class PhpdocTypesFixer extends AbstractPhpdocTypesFixer
{
    /**
     * The types to process.
     *
     * @var string[]
     */
    private static $types = [
        'array',
        'bool',
        'boolean',
        'callable',
        'callback',
        'double',
        'false',
        'float',
        'int',
        'integer',
        'iterable',
        'mixed',
        'null',
        'object',
        'parent',
        'real',
        'resource',
        'scalar',
        'self',
        'static',
        'string',
        'true',
        'void',
        '$this',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The correct case must be used for standard PHP types in PHPDoc.',
            [
                new CodeSample(
                    '<?php
/**
 * @param STRING|String[] $bar
 *
 * @return inT[]
 */
'
                ),
            ]
        );
    }

    public function getPriority()
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment and phpdoc_indent fixer to make sure all fixers
         * apply correct indentation to new code they add. This should run
         * before alignment of params is done since this fixer might change
         * the type and thereby un-aligning the params. We also must run before
         * the phpdoc_scalar_fixer so that it can make changes after us.
         */
        return 16;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize($type)
    {
        $lower = strtolower($type);

        if (in_array($lower, self::$types, true)) {
            return $lower;
        }

        return $type;
    }
}
