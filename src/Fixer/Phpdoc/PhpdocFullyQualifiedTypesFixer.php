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
use PhpCsFixer\Tokenizer\Analyzer\Analysis\TypeAnalysis;
use PhpCsFixer\Tokenizer\Resolver\TypeShortNameResolver;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpdocFullyQualifiedTypesFixer extends AbstractPhpdocTypesFixer
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
     *
     * Must run before PhpdocAlignFixer.
     * Must run after CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority()
    {
        /*
         * Should be run before all other docblock fixers apart from the
         * phpdoc_to_comment and phpdoc_indent fixer to make sure all fixers
         * apply correct indentation to new code they add. This should run
         * before alignment of params is done since this fixer might change
         * the type and thereby un-aligning the params.
         */
        return 14;
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(Tokens $tokens, $type)
    {
        if (0 !== strpos($type, '\\')) {
            return $type;
        }

        if ((new TypeAnalysis(substr($type, 1), 1, 2))->isReservedType()) {
            return $type;
        }

        return (new TypeShortNameResolver())->resolve($tokens, $type);
    }
}
