<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tokenizer;

/**
 * Interface for Transformer class.
 *
 * Transformer role is to register custom tokens and transform Tokens collection to use them.
 *
 * Custom token is a user defined token type and is used to separate different meaning of original token type.
 * For example T_ARRAY is a token for both creating new array and typehinting a parameter. This two meaning should have two token types.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
interface TransformerInterface
{
    /**
     * Process Token to transform it into custom token when needed.
     *
     * @param Tokens $tokens
     * @param Token  $token
     * @param int    $index
     */
    public function process(Tokens $tokens, Token $token, $index);

    /**
     * Register constants for custom tokens created by Transformer.
     */
    public function registerCustomTokens();

    /**
     * Get names of custom tokens created by Transformer.
     *
     * @return array
     */
    public function getCustomTokenNames();

    /**
     * Returns the name of the fixer.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the fixer
     */
    public function getName();
}
