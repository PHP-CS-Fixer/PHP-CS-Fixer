<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer;

/**
 * Interface for Transformator class.
 *
 * Transformator role is to register custom tokens and transform Tokens collection to use them.
 *
 * Custom token is a user defined token type and is used to separate different meaning of original token type.
 * For example T_ARRAY is a token for both creating new array and typehinting a parameter. This two meaning should have two token types.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
interface TransformatorInterface
{
    /**
     * Process Tokens collection to transform tokens into custom tokens when needed.
     *
     * @param Tokens $tokens Tokens collection
     */
    public function process(Tokens $tokens);

    /**
     * Register constants for custom tokens created by Transformator.
     */
    public function registerCustomTokens();

    /**
     * Get names of custom tokens created by Transformator.
     *
     * @return array
     */
    public function getCustomTokenNames();
}
