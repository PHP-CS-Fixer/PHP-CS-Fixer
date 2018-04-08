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

namespace PhpCsFixer\Tests\Test\Constraint;

use PhpCsFixer\Tests\TestCase;

if (!class_exists('PHPUnit\Framework\ExpectationFailedException')) {
    class_alias('PHPUnit_Framework_ExpectationFailedException', 'PHPUnit\Framework\ExpectationFailedException');
}

/**
 * @internal
 *
 * @covers \PhpCsFixer\Tests\Test\Constraint\XmlMatchesXsdConstraint
 */
final class XmlMatchesXsdConstraintTest extends TestCase
{
    public function testAssertXMLMatchesXSD()
    {
        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $sampleFile = $this->getAssetsDir().'xliff_sample.xml';
        $content = @file_get_contents($sampleFile);

        if (false === $content) {
            $error = error_get_last();

            throw new \RuntimeException(sprintf(
                'Failed to read content of the sample file "%s".%s',
                $content,
                $error ? ' '.$error['message'] : ''
            ));
        }

        $constraint->evaluate($content); // should not throw an exception
        $this->assertTrue($constraint->evaluate($content, '', true));
    }

    public function testXMLValidConstraintBasics()
    {
        $constraint = new XmlMatchesXsdConstraint('');
        $this->assertSame(1, $constraint->count());
        $this->assertSame('matches XSD', $constraint->toString());
    }

    public function testXMLValidConstraintFalse()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that boolean\# matches XSD\.$#'
        );

        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(false);
    }

    public function testXMLValidConstraintInt()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that integer\#1 matches XSD\.$#'
        );

        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(1);
    }

    public function testXMLValidConstraintInvalidXML()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that <a></b> matches XSD.[\n]\[error \d{1,}\](?s).*\.$#'
        );

        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate('<a></b>');
    }

    public function testXMLValidConstraintNotMatchingXML()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that <a></a> matches XSD.[\n]\[error \d{1,}\](?s).*\.$#'
        );

        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate('<a></a>');
    }

    public function testXMLValidConstraintNull()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that null matches XSD\.$#'
        );

        $constraint = new XmlMatchesXsdConstraint($this->getXSD());
        $constraint->evaluate(null);
    }

    public function testXMLValidConstraintObject()
    {
        $this->expectException(
            'PHPUnit\Framework\ExpectationFailedException'
        );
        $this->expectExceptionMessageRegExp(
            '#^Failed asserting that stdClass\# matches XSD\.$#'
        );

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
