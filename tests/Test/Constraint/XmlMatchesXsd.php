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

namespace PhpCsFixer\Tests\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class XmlMatchesXsd extends Constraint
{
    /**
     * @var list<string>
     */
    private array $xmlConstraintErrors = [];

    private string $xsd;

    public function __construct(string $xsd)
    {
        // replace first only
        $needle = 'http://www.w3.org/2001/xml.xsd';
        if (false !== $pos = strpos($xsd, $needle)) {
            $xsd = substr_replace($xsd, 'file:///'.str_replace('\\', '/', __DIR__).'/xml.xsd', $pos, \strlen($needle));
        }

        $this->xsd = $xsd;
    }

    public function toString(): string
    {
        return 'matches XSD';
    }

    /**
     * @param mixed $other
     */
    protected function failureDescription($other): string
    {
        if (\is_string($other)) {
            return \sprintf("%s %s.\n%s", $other, $this->toString(), implode("\n", $this->xmlConstraintErrors));
        }

        if (\is_object($other)) {
            $type = \sprintf('%s#%s', \get_class($other), method_exists($other, '__toString') ? $other->__toString() : '');
        } elseif (null === $other) {
            $type = 'null';
        } else {
            $type = \gettype($other).'#'.$other;
        }

        return $type.' '.$this->toString();
    }

    /**
     * @param mixed $other
     */
    protected function matches($other): bool
    {
        return \is_string($other)
            ? $this->stringMatches($other)
            : false;
    }

    private function stringMatches(string $other): bool
    {
        $internalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->validateOnParse = true;

        if (!@$dom->loadXML($other, \LIBXML_NONET | (\defined('LIBXML_COMPACT') ? \LIBXML_COMPACT : 0))) {
            $this->setXMLConstraintErrors();
            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);

            return false;
        }

        $dom->normalizeDocument();

        libxml_clear_errors();

        if (false === $result = @$dom->schemaValidateSource($this->xsd)) {
            $this->setXMLConstraintErrors();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $result;
    }

    private function setXMLConstraintErrors(): void
    {
        foreach (libxml_get_errors() as $error) {
            if (\LIBXML_ERR_WARNING === $error->level) {
                $level = 'warning ';
            } elseif (\LIBXML_ERR_ERROR === $error->level) {
                $level = 'error ';
            } elseif (\LIBXML_ERR_FATAL === $error->level) {
                $level = 'fatal ';
            } else {
                $level = '';
            }

            $this->xmlConstraintErrors[] = \sprintf('[%s%s] %s (line %d, column %d).', $level, $error->code, trim($error->message), $error->line, $error->column);
        }
    }
}
