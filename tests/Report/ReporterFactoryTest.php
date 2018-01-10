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

namespace PhpCsFixer\Tests\Report;

use PhpCsFixer\Report\ReporterFactory;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\ReporterFactory
 */
final class ReporterFactoryTest extends TestCase
{
    public function testCreate()
    {
        $factory = ReporterFactory::create();

        $this->assertInstanceOf('PhpCsFixer\Report\ReporterFactory', $factory);
    }

    public function testInterfaceIsFluent()
    {
        $builder = new ReporterFactory();

        $testInstance = $builder->registerBuiltInReporters();
        $this->assertSame($builder, $testInstance);

        $double = $this->createReporterDouble('r1');
        $testInstance = $builder->registerReporter($double);
        $this->assertSame($builder, $testInstance);
    }

    public function testRegisterBuiltInReports()
    {
        $builder = new ReporterFactory();

        $this->assertCount(0, $builder->getFormats());

        $builder->registerBuiltInReporters();
        $this->assertSame(
            array('json', 'junit', 'txt', 'xml'),
            $builder->getFormats()
        );
    }

    public function testThatCanRegisterAndGetReports()
    {
        $builder = new ReporterFactory();

        $r1 = $this->createReporterDouble('r1');
        $r2 = $this->createReporterDouble('r2');
        $r3 = $this->createReporterDouble('r3');

        $builder->registerReporter($r1);
        $builder->registerReporter($r2);
        $builder->registerReporter($r3);

        $this->assertSame($r1, $builder->getReporter('r1'));
        $this->assertSame($r2, $builder->getReporter('r2'));
        $this->assertSame($r3, $builder->getReporter('r3'));
    }

    public function testGetFormats()
    {
        $builder = new ReporterFactory();

        $r1 = $this->createReporterDouble('r1');
        $r2 = $this->createReporterDouble('r2');
        $r3 = $this->createReporterDouble('r3');

        $builder->registerReporter($r1);
        $builder->registerReporter($r2);
        $builder->registerReporter($r3);

        $this->assertSame(array('r1', 'r2', 'r3'), $builder->getFormats());
    }

    public function testRegisterReportWithOccupiedFormat()
    {
        $this->setExpectedException(
            'UnexpectedValueException',
            'Reporter for format "non_unique_name" is already registered.'
        );

        $factory = new ReporterFactory();

        $r1 = $this->createReporterDouble('non_unique_name');
        $r2 = $this->createReporterDouble('non_unique_name');
        $factory->registerReporter($r1);
        $factory->registerReporter($r2);
    }

    public function testGetNonRegisteredReport()
    {
        $this->setExpectedException(
            'UnexpectedValueException',
            'Reporter for format "non_registered_format" is not registered.'
        );

        $builder = new ReporterFactory();

        $builder->getReporter('non_registered_format');
    }

    private function createReporterDouble($format)
    {
        $reporter = $this->prophesize('PhpCsFixer\Report\ReporterInterface');
        $reporter->getFormat()->willReturn($format);

        return $reporter->reveal();
    }
}
