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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Transformators
{
    public static function create()
    {
        static $instance = null;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }

    private $items = array();
    private $customTokens = array();

    private function __construct()
    {
        $this->registerBuiltInTransformators();
    }

    private function registerBuiltInTransformators()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/Transformator') as $file) {
            $relativeNamespace = $file->getRelativePath();
            $class = __NAMESPACE__.'\\Transformator\\'.($relativeNamespace ? $relativeNamespace.'\\' : '').$file->getBasename('.php');
            $this->registerTransformator(new $class());
        }
    }

    public function registerTransformator(TransformatorInterface $transformator)
    {
        $this->items[] = $transformator;

        $transformator->registerConstants();

        foreach ($transformator->getConstantDefinitions() as $value => $name) {
            $this->addCustomToken($value, $name);
        }
    }

    private function addCustomToken($value, $name)
    {
        if ($this->hasCustomToken($value)) {
            throw new \Exception();
        }

        $this->customTokens[$value] = $name;
    }

    public function hasCustomToken($value)
    {
        return isset($this->customTokens[$value]);
    }

    public function getCustomToken($value)
    {
        if (!$this->hasCustomToken($value)) {
            throw new \Exception();
        }

        return $this->customTokens[$value];
    }

    public function transform(Tokens $tokens)
    {
        foreach ($this->items as $transformator) {
            $transformator->process($tokens);
        }
    }
}
