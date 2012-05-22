<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class ControlSpacesFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $content = $this->fixControlsWithSuffixBrace($content);
        $content = $this->fixControlsWithPrefixBraceAndParentheses($content);
        $content = $this->fixControlsWithParenthesesAndSuffixBrace($content);
        $content = $this->fixControlsWithPrefixBraceAndSuffixBrace($content);
        $content = $this->fixControlsWithPrefixBraceAndParenthesesAndSuffixBrace($content);

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // should be run after the CurlyBracketsNewlineFixer
        return -10;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'controls_spaces';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'A single space should be between: the closing brace and the control, the control and the opening parenthese, the closing parenthese and the opening brace.';
    }

    /**
     * "xxx {"
     *
     * @param string $content
     *
     * @return string
     */
    private function fixControlsWithSuffixBrace($content)
    {
        $statements = array(
            'try',
            'do',
        );

        return preg_replace(sprintf('/(%s)[^\S\n]*{/', implode('|', $statements)), '\\1 {', $content);
    }

    /**
     * "} xxx ()"
     *
     * @param string $content
     *
     * @return string
     */
    private function fixControlsWithPrefixBraceAndParentheses($content)
    {
        $statements = array(
            'while'
        );

        return preg_replace(sprintf('/}[^\S\n]*(%s)[^\S\n]*\((.*)\)/', implode('|', $statements)), '} \\1 (\\2)', $content);
    }

    /**
     * "xxx () {"
     *
     * @param string $content
     *
     * @return string
     */
    private function fixControlsWithParenthesesAndSuffixBrace($content)
    {
        $statements = array(
            'if',
            'for',
            'while',
            'foreach',
            'switch',
        );

        return preg_replace(sprintf('/(%s)[^\S\n]*\((.*)\)[^\S\n]*{/', implode('|', $statements)), '\\1 (\\2) {', $content);
    }

    /**
     * "} xxx {"
     *
     * @param string $content
     *
     * @return string
     */
    private function fixControlsWithPrefixBraceAndSuffixBrace($content)
    {
        $statements = array(
            'else',
        );

        return preg_replace(sprintf('/}[^\S\n]*(%s)[^\S\n]*{/', implode('|', $statements)), '} \\1 {', $content);
    }

    /**
     * "} xxx (...) {
     *
     * @param string $content
     *
     * @return string
     */
    private function fixControlsWithPrefixBraceAndParenthesesAndSuffixBrace($content)
    {
        $statements = array(
            'elseif',
            'else if',
            'catch',
        );

        return preg_replace(sprintf('/}[^\S\n]*(%s)[^\S\n]*\((.*)\)[^\S\n]*{/', implode('|', $statements)), '} \\1 (\\2) {', $content);
    }
}
