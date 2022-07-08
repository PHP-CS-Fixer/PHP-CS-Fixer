<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\DocBlock;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;

/**
 * This class represents a docblock.
 *
 * It internally splits it up into "lines" that we can manipulate.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class DocBlock
{
    /**
     * The array of lines.
     *
     * @var Line[]
     */
    private array $lines = [];

    /**
     * The array of annotations.
     *
     * @var null|Annotation[]
     */
    private $annotations;

    private ?NamespaceAnalysis $namespace;

    /**
     * @var NamespaceUseAnalysis[]
     */
    private array $namespaceUses;

    public function __construct(string $content, ?NamespaceAnalysis $namespace = null, array $namespaceUses = [])
    {
        foreach (Preg::split('/([^\n\r]+\R*)/', $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $line) {
            $this->lines[] = new Line($line);
        }

        $this->namespace = $namespace;
        $this->namespaceUses = $namespaceUses;
    }

    /**
     * Get the string representation of object.
     */
    public function __toString(): string
    {
        return $this->getContent();
    }

    /**
     * Get this docblock's lines.
     *
     * @return Line[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Get a single line.
     */
    public function getLine(int $pos): ?Line
    {
        return $this->lines[$pos] ?? null;
    }

    /**
     * Get this docblock's annotations.
     *
     * @return Annotation[]
     */
    public function getAnnotations(): array
    {
        if (null !== $this->annotations) {
            return $this->annotations;
        }

        $this->annotations = [];
        $total = \count($this->lines);

        for ($index = 0; $index < $total; ++$index) {
            if ($this->lines[$index]->containsATag()) {
                // get all the lines that make up the annotation
                $lines = \array_slice($this->lines, $index, $this->findAnnotationLength($index), true);
                $annotation = new Annotation($lines, $this->namespace, $this->namespaceUses);
                // move the index to the end of the annotation to avoid
                // checking it again because we know the lines inside the
                // current annotation cannot be part of another annotation
                $index = $annotation->getEnd();
                // add the current annotation to the list of annotations
                $this->annotations[] = $annotation;
            }
        }

        return $this->annotations;
    }

    public function isMultiLine(): bool
    {
        return 1 !== \count($this->lines);
    }

    /**
     * Take a one line doc block, and turn it into a multi line doc block.
     */
    public function makeMultiLine(string $indent, string $lineEnd): void
    {
        if ($this->isMultiLine()) {
            return;
        }

        $lineContent = $this->getSingleLineDocBlockEntry($this->lines[0]);

        if ('' === $lineContent) {
            $this->lines = [
                new Line('/**'.$lineEnd),
                new Line($indent.' *'.$lineEnd),
                new Line($indent.' */'),
            ];

            return;
        }

        $this->lines = [
            new Line('/**'.$lineEnd),
            new Line($indent.' * '.$lineContent.$lineEnd),
            new Line($indent.' */'),
        ];
    }

    public function makeSingleLine(): void
    {
        if (!$this->isMultiLine()) {
            return;
        }

        $usefulLines = array_filter(
            $this->lines,
            static function (Line $line): bool {
                return $line->containsUsefulContent();
            }
        );

        if (1 < \count($usefulLines)) {
            return;
        }

        $lineContent = '';
        if (\count($usefulLines) > 0) {
            $lineContent = $this->getSingleLineDocBlockEntry(array_shift($usefulLines));
        }

        $this->lines = [new Line('/** '.$lineContent.' */')];
    }

    public function getAnnotation(int $pos): ?Annotation
    {
        $annotations = $this->getAnnotations();

        return $annotations[$pos] ?? null;
    }

    /**
     * Get specific types of annotations only.
     *
     * If none exist, we're returning an empty array.
     *
     * @param string|string[] $types
     *
     * @return Annotation[]
     */
    public function getAnnotationsOfType($types): array
    {
        $annotations = [];
        $types = (array) $types;

        foreach ($this->getAnnotations() as $annotation) {
            $tag = $annotation->getTag()->getName();
            foreach ($types as $type) {
                if ($type === $tag) {
                    $annotations[] = $annotation;
                }
            }
        }

        return $annotations;
    }

    /**
     * Get the actual content of this docblock.
     */
    public function getContent(): string
    {
        return implode('', $this->lines);
    }

    private function findAnnotationLength(int $start): int
    {
        $index = $start;

        while ($line = $this->getLine(++$index)) {
            if ($line->containsATag()) {
                // we've 100% reached the end of the description if we get here
                break;
            }

            if (!$line->containsUsefulContent()) {
                // if next line is also non-useful, or contains a tag, then we're done here
                $next = $this->getLine($index + 1);
                if (null === $next || !$next->containsUsefulContent() || $next->containsATag()) {
                    break;
                }
                // otherwise, continue, the annotation must have contained a blank line in its description
            }
        }

        return $index - $start;
    }

    private function getSingleLineDocBlockEntry(Line $line): string
    {
        $lineString = $line->getContent();

        if ('' === $lineString) {
            return $lineString;
        }

        $lineString = str_replace('*/', '', $lineString);
        $lineString = trim($lineString);

        if (str_starts_with($lineString, '/**')) {
            $lineString = substr($lineString, 3);
        } elseif (str_starts_with($lineString, '*')) {
            $lineString = substr($lineString, 1);
        }

        return trim($lineString);
    }
}
