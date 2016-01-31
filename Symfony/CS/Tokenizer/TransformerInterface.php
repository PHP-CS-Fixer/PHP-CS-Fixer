<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer;

/**
 * Interface for Transformer class.
 *
 * Transformer role is to register custom tokens and transform Tokens collection to use them.
 *
 * Custom token is a user defined token type and is used to separate different meaning of original token type.
 * For example T_ARRAY is a token for both creating new array and typehinting a parameter. This two meaning should have two token types.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface TransformerInterface
{
    /**
     * Process Tokens collection to transform tokens into custom tokens when needed.
     *
     * @param Tokens $tokens Tokens collection
     */
    public function process(Tokens $tokens);

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

    /**
     * Returns the priority of the Transformer.
     *
     * The default priority is 0 and higher priorities are executed first.
     *
     * @return int
     */
    public function getPriority();
}
