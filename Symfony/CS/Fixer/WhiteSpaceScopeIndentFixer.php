<?php

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

class WhiteSpaceScopeIndentFixer
    implements FixerInterface
{
    /**
     * The number of spaces code should be indented.
     *
     * @var integer
     */
    private $indent = 4;

    /**
     * The tokenizer to use when code is parsed into token array.
     *
     * @var \PHP_CodeSniffer_Tokenizers_PHP
     */
    private $tokenizer = null;

    /**
     * @see \Symfony\CS\FixerInterface::fix()
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = $this->getCodeSnifferTokensByContent($content);

        // We check the used indentation from the content of the previous token, hence we need to keep track of it:
        $previousToken = null;

        $fixedContent = '';

        foreach ($tokens as $token) {
            // by default the content of the previous token is correct...
            $fixedPreviousTokenContent = $previousToken['content'];

            // ...but if its not:
            if ($previousToken !== null && strpos($previousToken['content'], "\n") === 0) {
                // split the previous content by newlines:
                $linesOnPrevious = explode("\n", $previousToken['content']);

                // calculate the expected indentation using the level on token:
                $expectedIndent = $token['level'] * $this->indent;

                // remove the last line from previous, glue with newlines and add correct indentation:
                array_pop($linesOnPrevious);
                $fixedPreviousTokenContent = implode("\n", $linesOnPrevious) . "\n" . str_repeat(' ', $expectedIndent);
            }

            $fixedContent.= $fixedPreviousTokenContent;

            $previousToken = $token;
        }

        if ($previousToken !== null) {
            // we need to make sure the last token gets processed as well:
            $fixedContent.= $previousToken['content'];
        }

        return $fixedContent;
    }

    /**
     * @see \Symfony\CS\FixerInterface::getLevel()
     */
    public function getLevel()
    {
        // defined in PSR2 2.4
        return FixerInterface::PSR2_LEVEL;
    }

    /**
     * @see \Symfony\CS\FixerInterface::getPriority()
     */
    public function getPriority()
    {
        return 50;
    }

    /**
     * @see \Symfony\CS\FixerInterface::supports()
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * @see \Symfony\CS\FixerInterface::getName()
     */
    public function getName()
    {
        return 'indentation';
    }

    /**
     * @see \Symfony\CS\FixerInterface::getDescription()
     */
    public function getDescription()
    {
        return 'Code must use 4 spaces for indenting, not tabs.';
    }

    /**
     * Tokenizes the given content.
     *
     * @param string $content
     * @return array Array of arrays.
     */
    protected function getCodeSnifferTokensByContent($content)
    {
        if($this->tokenizer == null) {
            // this is instantiated here just for the required constants used in PHP_CodeSniffer_File:
            new \PHP_CodeSniffer();
            $this->tokenizer = new \PHP_CodeSniffer_Tokenizers_PHP();
        }

        return \PHP_CodeSniffer_File::tokenizeString($content, $this->tokenizer);
    }
}