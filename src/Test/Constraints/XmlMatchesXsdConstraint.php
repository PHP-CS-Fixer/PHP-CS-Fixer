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

namespace PhpCsFixer\Test\Constraints;

/**
 * @author SpacePossum
 *
 * @internal
 */
final class XmlMatchesXsdConstraint extends \PHPUnit_Framework_Constraint
{
    /**
     * @var string[]
     */
    private $xmlConstraintErrors = array();

    /**
     * @var string
     */
    private $xsd;

    /**
     * @param string $xsd
     */
    public function __construct($xsd)
    {
        parent::__construct();

        // replace first only
        $needle = 'http://www.w3.org/2001/xml.xsd';
        if (false !== $pos = strpos($xsd, $needle)) {
            $xsd = substr_replace($xsd, 'file:///'.str_replace('\\', '/', __DIR__).'/xml.xsd', $pos, strlen($needle));
        }

        $this->xsd = $xsd;
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return 'matches XSD';
    }

    /**
     * {@inheritdoc}
     */
    protected function failureDescription($other)
    {
        if (is_string($other)) {
            return sprintf("%s %s.\n%s", $other, $this->toString(), implode("\n", $this->xmlConstraintErrors));
        }

        if (is_object($other)) {
            $type = sprintf('%s#%s', get_class($other), method_exists($other, '__toString') ? $other->__toString() : '');
        } elseif (null === $other) {
            $type = 'null';
        } else {
            $type = gettype($other).'#'.$other;
        }

        return $type.' '.$this->toString();
    }

    /**
     * {@inheritdoc}
     */
    protected function matches($other)
    {
        return is_string($other)
            ? $this->stringMatches($other)
            : false
        ;
    }

    /**
     * @param string $other
     *
     * @return bool
     */
    private function stringMatches($other)
    {
        $internalErrors = libxml_use_internal_errors(true);
        $disableEntities = libxml_disable_entity_loader(true);
        libxml_clear_errors();

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->validateOnParse = true;

        if (!@$dom->loadXML($other, LIBXML_NONET | (defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0))) {
            libxml_disable_entity_loader($disableEntities);
            $this->setXMLConstraintErrors();
            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);

            return false;
        }

        $dom->normalizeDocument();

        libxml_disable_entity_loader($disableEntities);
        libxml_clear_errors();

        if (false === $result = @$dom->schemaValidateSource($this->xsd)) {
            $this->setXMLConstraintErrors();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $result;
    }

    private function setXMLConstraintErrors()
    {
        foreach (libxml_get_errors() as $error) {
            if (LIBXML_ERR_WARNING === $error->level) {
                $level = 'warning ';
            } elseif (LIBXML_ERR_ERROR === $error->level) {
                $level = 'error ';
            } elseif (LIBXML_ERR_FATAL === $error->level) {
                $level = 'fatal ';
            } else {
                $level = '';
            }

            $this->xmlConstraintErrors[] = sprintf('[%s%s] %s (line %d, column %d).', $level, $error->code, trim($error->message), $error->line, $error->column);
        }
    }
}
