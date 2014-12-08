<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractFixer implements FixerInterface
{
    const ALIGNABLE_PLACEHOLDER = "\x2 ALIGNABLE%d \x3";

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        static $map = array(
            'PSR0'    => FixerInterface::PSR0_LEVEL,
            'PSR1'    => FixerInterface::PSR1_LEVEL,
            'PSR2'    => FixerInterface::PSR2_LEVEL,
            'Symfony' => FixerInterface::SYMFONY_LEVEL,
            'Contrib' => FixerInterface::CONTRIB_LEVEL,
        );

        $level = current(explode('\\', substr(get_called_class(), strlen(__NAMESPACE__.'\\Fixer\\'))));

        if (!isset($map[$level])) {
            throw new \LogicException('Can not determine Fixer level');
        }

        return $map[$level];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $nameParts = explode('\\', get_called_class());
        $name = substr(end($nameParts), 0, -strlen('Fixer'));

        return Utils::camelCaseToUnderscore($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    /**
     * Look for group of placeholders, and provide vertical alignment.
     *
     * @param string $tokens
     * @param int    $deepestLevel
     *
     * @return string
     */
    protected function replacePlaceholder($tokens, $deepestLevel)
    {
        $tmpCode = $tokens->generateCode();

        for ($j = 0; $j <= $deepestLevel; ++$j) {
            $placeholder = sprintf(self::ALIGNABLE_PLACEHOLDER, $j);

            if (false === strpos($tmpCode, $placeholder)) {
                continue;
            }

            $lines = explode("\n", $tmpCode);
            $linesWithPlaceholder = array();
            $blockSize = 0;

            $linesWithPlaceholder[$blockSize] = array();

            foreach ($lines as $index => $line) {
                if (substr_count($line, $placeholder) > 0) {
                    $linesWithPlaceholder[$blockSize][] = $index;
                } else {
                    ++$blockSize;
                    $linesWithPlaceholder[$blockSize] = array();
                }
            }

            $i = 0;
            foreach ($linesWithPlaceholder as $group) {
                if (1 === sizeof($group)) {
                    continue;
                }
                ++$i;
                $rightmostSymbol = 0;

                foreach ($group as $index) {
                    $rightmostSymbol = max($rightmostSymbol, strpos($lines[$index], $placeholder));
                }

                foreach ($group as $index) {
                    $line = $lines[$index];
                    $currentSymbol = strpos($line, $placeholder);
                    $delta = abs($rightmostSymbol - $currentSymbol);

                    if ($delta > 0) {
                        $line = str_replace($placeholder, str_repeat(' ', $delta).$placeholder, $line);
                        $lines[$index] = $line;
                    }
                }
            }

            $tmpCode = str_replace($placeholder, '', implode("\n", $lines));
        }

        return $tmpCode;
    }
}
