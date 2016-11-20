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

namespace PhpCsFixer;

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface FixerInterface
{
    /**
     * Set configuration.
     *
     * New configuration must override current one, not patch it.
     * Using `null` makes fixer to use default configuration (or reset configuration from previously configured back
     * to default one).
     *
     * Some fixers may have no configuration, then - simply pass null.
     * Other ones may have configuration that will change behavior of fixer,
     * eg `php_unit_strict` fixer allows to configure which methods should be fixed.
     * Finally, some fixers need configuration to work, eg `header_comment`.
     *
     * @param array|null $configuration configuration depends on Fixer
     *
     * @throws InvalidFixerConfigurationException
     */
    public function configure(array $configuration = null);

    /**
     * Check if the fixer is a candidate for given Tokens collection.
     *
     * Fixer is a candidate when the collection contains tokens that may be fixed
     * during fixer work. This could be considered as some kind of bloom filter.
     * When this method returns true then to the Tokens collection may or may not
     * need a fixing, but when this method returns false then the Tokens collection
     * need no fixing for sure.
     *
     * @param Tokens $tokens
     *
     * @return bool
     */
    public function isCandidate(Tokens $tokens);

    /**
     * Check if fixer is risky or not.
     *
     * Risky fixer could change code behavior!
     *
     * @return bool
     */
    public function isRisky();

    /**
     * Fixes a file.
     *
     * @param \SplFileInfo $file   A \SplFileInfo instance
     * @param Tokens       $tokens Tokens collection
     */
    public function fix(\SplFileInfo $file, Tokens $tokens);

    /**
     * Returns the definition of the fixer.
     *
     * @return FixerDefinitionInterface
     */
    public function getDefinition();

    /**
     * Returns the name of the fixer.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the fixer
     */
    public function getName();

    /**
     * Returns the priority of the fixer.
     *
     * The default priority is 0 and higher priorities are executed first.
     *
     * @return int
     */
    public function getPriority();

    /**
     * Returns true if the file is supported by this fixer.
     *
     * @param \SplFileInfo $file
     *
     * @return bool true if the file is supported by this fixer, false otherwise
     */
    public function supports(\SplFileInfo $file);
}
