<?php

namespace PhpCsFixer\Tokenizer\Analyzer\Analysis;

class TypeAnalysis implements StartEndTokenAwareAnalysis
{
    private static $scalarTypes = [
        'array',
        'bool',
        'callable',
        'int',
        'iteratable',
        'float',
        'string',
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $endIndex;

    /**
     * TypeAnalysis constructor.
     * @param string $name
     * @param int $startIndex
     * @param int $endIndex
     */
    public function __construct($name, $startIndex, $endIndex)
    {
        $this->name = $name;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @return int
     */
    public function getEndIndex()
    {
        return $this->endIndex;
    }

    /**
     * @return bool
     */
    public function isScalar()
    {
        return \in_array($this->name, self::$scalarTypes, true);
    }
}
