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

use PhpCsFixer\Report\ReportFactory;
use PhpCsFixer\Test\AccessibleObject;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class ReportFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceIsFluent()
    {
        $builder = new ReportFactory();

        $testInstance = $builder->registerBuiltInReports();
        $this->assertSame($builder, $testInstance);

        $mock = $this->createReportMock('r1');
        $testInstance = $builder->registerReport($mock);
        $this->assertSame($builder, $testInstance);
    }

    public function testRegisterBuiltInReports()
    {
        $builder = new ReportFactory();
        $builder->registerBuiltInReports();

        $accessibleFactory = new AccessibleObject($builder);

        $this->assertGreaterThan(0, count($accessibleFactory->reports));
    }

    public function testThatCanRegisterAndGetReports()
    {
        $builder = new ReportFactory();

        $r1 = $this->createReportMock('r1');
        $r2 = $this->createReportMock('r2');
        $r3 = $this->createReportMock('r3');

        $builder->registerReport($r1);
        $builder->registerReport($r2);
        $builder->registerReport($r3);

        $this->assertSame($r1, $builder->getReport('r1'));
        $this->assertSame($r2, $builder->getReport('r2'));
        $this->assertSame($r3, $builder->getReport('r3'));
    }

    /**
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Report for format "non_unique_name" is already registered.
     */
    public function testRegisterReportWithOccupiedFormat()
    {
        $factory = new ReportFactory();

        $r1 = $this->createReportMock('non_unique_name');
        $r2 = $this->createReportMock('non_unique_name');
        $factory->registerReport($r1);
        $factory->registerReport($r2);
    }

    /**
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Report for format "non_registered_format" does not registered.
     */
    public function testGetNonRegisteredReport()
    {
        $builder = new ReportFactory();

        $builder->getReport('non_registered_format');
    }

    private function createReportMock($format)
    {
        $report = $this->getMock('PhpCsFixer\Report\ReportInterface');
        $report->expects($this->any())->method('getFormat')->willReturn($format);

        return $report;
    }
}
