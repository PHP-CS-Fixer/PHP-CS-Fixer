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

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

/**
 * @internal
 */
final class ClassyElementAnalysis
{
    const TYPE_CONSTANT = 1;
    const TYPE_METHOD = 2;
    const TYPE_PROPERTY = 3;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $classIndex;

    /**
     * @param int $type
     * @param int $classIndex
     */
    public function __construct($type, $classIndex)
    {
        $this->type = $type;
        $this->classIndex = $classIndex;
    }

    /**
     * @return bool
     */
    public function isConstant()
    {
        return self::TYPE_CONSTANT === $this->type;
    }

    /**
     * @return bool
     */
    public function isMethod()
    {
        return self::TYPE_METHOD === $this->type;
    }

    /**
     * @return bool
     */
    public function isProperty()
    {
        return self::TYPE_PROPERTY === $this->type;
    }

    /**
     * @return int
     */
    public function getClassIndex()
    {
        return $this->classIndex;
    }
}
