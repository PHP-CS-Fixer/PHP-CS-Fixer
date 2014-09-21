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

use Symfony\Component\Finder\Finder;

/**
 * Collection of Transformator classes.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Transformators
{
    /**
     * Array of registered Transformator classes.
     *
     * @var Transformator[]
     */
    private $items = array();

    /**
     * Array mapping custom token value => custom token name.
     *
     * @var array
     */
    private $customTokens = array();

    /**
     * Constructor. Register built in Transformators.
     */
    private function __construct()
    {
        $this->registerBuiltInTransformators();
    }

    /**
     * Create Transformators instance.
     *
     * @return Transformators
     */
    public static function create()
    {
        static $instance = null;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Get name for registered custom token.
     *
     * @param int $value custom token value
     *
     * @return string
     */
    public function getCustomToken($value)
    {
        if (!$this->hasCustomToken($value)) {
            throw new \InvalidArgumentException("No custom token was found for $value");
        }

        return $this->customTokens[$value];
    }

    /**
     * Check if given custom token was added to collection.
     *
     * @param int $value custom token value
     *
     * @return bool
     */
    public function hasCustomToken($value)
    {
        return isset($this->customTokens[$value]);
    }

    /**
     * Register Transformator.
     *
     * @param TransformatorInterface $transformator Transformator
     */
    public function registerTransformator(TransformatorInterface $transformator)
    {
        $this->items[] = $transformator;

        $transformator->registerCustomTokens();

        foreach ($transformator->getCustomTokenNames() as $name) {
            $this->addCustomToken(constant($name), $name);
        }
    }

    /**
     * Transform given Tokens collection thru all Transformator classes.
     *
     * @param Tokens $tokens Tokens collection
     */
    public function transform(Tokens $tokens)
    {
        foreach ($this->items as $transformator) {
            $transformator->process($tokens);
        }
    }

    /**
     * Add custom token.
     *
     * @param int    $value custom token value
     * @param string $name  custom token name
     */
    private function addCustomToken($value, $name)
    {
        if ($this->hasCustomToken($value)) {
            throw new \LogicException("Trying to register token $name ($value), token with this value was already defined: ".$this->getCustomToken($value));
        }

        $this->customTokens[$value] = $name;
    }

    /**
     * Register all built in Transformatrs.
     */
    private function registerBuiltInTransformators()
    {
        static $registered = false;

        if ($registered) {
            return;
        }

        $registered = true;

        foreach (Finder::create()->files()->in(__DIR__.'/Transformator') as $file) {
            $relativeNamespace = $file->getRelativePath();
            $class = __NAMESPACE__.'\\Transformator\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            $this->registerTransformator(new $class());
        }
    }
}
