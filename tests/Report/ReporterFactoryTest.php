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

namespace PhpCsFixer\Report\Tests;

use PhpCsFixer\Report\ReporterFactory;
use PhpCsFixer\Test\AccessibleObject;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class ReporterFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceIsFluent()
    {
        $builder = new ReporterFactory();

        $testInstance = $builder->registerBuiltInReporters();
        $this->assertSame($builder, $testInstance);

        $mock = $this->createReporterMock('r1');
        $testInstance = $builder->registerReporter($mock);
        $this->assertSame($builder, $testInstance);
    }

    private function createReporterMock($format)
    {
        $report = $this->getMock('PhpCsFixer\Report\ReporterInterface');
        $report->expects($this->any())->method('getFormat')->willReturn($format);

        return $report;
    }

    public function testRegisterBuiltInReports()
    {
        $builder = new ReporterFactory();
        $builder->registerBuiltInReporters();

        $accessibleFactory = new AccessibleObject($builder);

        $this->assertGreaterThan(0, count($accessibleFactory->reporters));
    }

    public function testThatCanRegisterAndGetReports()
    {
        $builder = new ReporterFactory();

        $r1 = $this->createReporterMock('r1');
        $r2 = $this->createReporterMock('r2');
        $r3 = $this->createReporterMock('r3');

        $builder->registerReporter($r1);
        $builder->registerReporter($r2);
        $builder->registerReporter($r3);

        $this->assertSame($r1, $builder->getReporter('r1'));
        $this->assertSame($r2, $builder->getReporter('r2'));
        $this->assertSame($r3, $builder->getReporter('r3'));
    }

    /**
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Reporter for format "non_unique_name" is already registered.
     */
    public function testRegisterReportWithOccupiedFormat()
    {
        $factory = new ReporterFactory();

        $r1 = $this->createReporterMock('non_unique_name');
        $r2 = $this->createReporterMock('non_unique_name');
        $factory->registerReporter($r1);
        $factory->registerReporter($r2);
    }

    /**
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Reporter for format "non_registered_format" is not registered.
     */
    public function testGetNonRegisteredReport()
    {
        $builder = new ReporterFactory();

        $builder->getReporter('non_registered_format');
    }
}
