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
use PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpdocFullyQualifiesTypesFixer extends AbstractPhpdocTypesFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Transforms imported FQCN in docblocks to short version.',
            [new CodeSample('<?php

use Foo\Bar;

/**
 * @param \Foo\Bar $foo
 */
function foo($foo) {}
')]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(Tokens $tokens, $type)
    {
        if (0 !== strpos($type, '\\')) {
            return $type;
        }

        $shortType = (new TypeShortNameResolver())->resolve($tokens, $type);

        return $shortType;
    }
}
