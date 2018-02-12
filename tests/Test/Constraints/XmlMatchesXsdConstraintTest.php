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

namespace PhpCsFixer\Tests\Test\Constraints;

use PhpCsFixer\Test\Constraints\XmlMatchesXsdConstraint;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 */
final class XmlMatchesXsdConstraintTest extends TestCase
{
    public function testAssertXMLMatchesXSD()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $this->assertTrue($constraint->evaluate(file_get_contents($this->getAssetsDir().'xliff_sample.xml'), '', true));
    }

    public function testXMLValidConstraintBasics()
    {
        $constraint = new XmlMatchesXsdConstraint('');
        $this->assertSame(1, $constraint->count());
        $this->assertSame('matches XSD', $constraint->toString());
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessageRegExp #^Failed asserting that boolean\# matches XSD\.$#
     */
    public function testXMLValidConstraintFalse()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(false);
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessageRegExp #^Failed asserting that integer\#1 matches XSD\.$#
     */
    public function testXMLValidConstraintInt()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(1);
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessageRegExp #^Failed asserting that <a></b> matches XSD.[\n]\[error \d{1,}\](?s).*\.$#
     */
    public function testXMLValidConstraintInvalidXML()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate('<a></b>');
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessageRegExp #^Failed asserting that <a></a> matches XSD.[\n]\[error \d{1,}\](?s).*\.$#
     */
    public function testXMLValidConstraintNotMatchingXML()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate('<a></a>');
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessageRegExp #^Failed asserting that null matches XSD\.$#
     */
    public function testXMLValidConstraintNull()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(null);
    }

    /**
     * @expectedException PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessageRegExp #^Failed asserting that stdClass\# matches XSD\.$#
     */
    public function testXMLValidConstraintObject()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(new \stdClass());
    }

    /**
     * @return string
     */
    private function getXSD()
    {
        return file_get_contents($this->getAssetsDir().'xliff-core-1.2-strict.xsd');
    }

    /**
     * @return string
     */
    private function getAssetsDir()
    {
        return __DIR__.'/../../Fixtures/Test/XmlMatchesXsdConstraintTest/';
    }
}
