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
 * @author Andreas Frömer <blubb0r05+github@gmail.com>
 */
final class PhpdocScalarBreakdownFixer extends AbstractPhpdocTypesFixer
{
    /**
     * The types to fix.
     *
     * @var array
     */
    private static $types = [
        'scalar' => 'int|string|float|bool',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'The scalar phpdoc type will be broken down into a list of valid scalar types.',
            [new CodeSample('<?php
/**
 * @param scalar $a
 *
 * @return scalar
 */
function sample($a)
{
    return sample2($a);
}
')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize($type)
    {
        if (array_key_exists($type, self::$types)) {
            return self::$types[$type];
        }

        return $type;
    }
}
