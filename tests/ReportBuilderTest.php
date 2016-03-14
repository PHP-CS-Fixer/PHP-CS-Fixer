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

namespace PhpCsFixer\Tests;

use PhpCsFixer\ReportBuilder;
use PhpCsFixer\Test\AccessibleObject;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class ReportBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testInterfaceIsFluent()
    {
	$builder = new ReportBuilder();

	$testInstance = $builder->registerBuiltInReports();
	$this->assertSame($builder, $testInstance);

	$mock = $this->createReportMock('r1');
	$testInstance = $builder->registerReport($mock);
	$this->assertSame($builder, $testInstance);
    }

    /**
     * @covers PhpCsFixer\ReportBuilder::registerBuiltInReports
     */
    public function testRegisterBuiltInReports()
    {
	$builder = new ReportBuilder();
	$builder->registerBuiltInReports();

	$accessibleFactory = new AccessibleObject($builder);

	$this->assertGreaterThan(0, count($accessibleFactory->reports));
    }

    /**
     * @covers PhpCsFixer\ReportBuilder::getReport
     * @covers PhpCsFixer\ReportBuilder::registerReport
     */
    public function testThatCanRegisterAndGetReports()
    {
	$builder = new ReportBuilder();

	$r1 = $this->createReportMock('r1');
	$r2 = $this->createReportMock('r2');
	$r3 = $this->createReportMock('r3');

	$builder->registerReport($r1);
	$builder->registerReport($r2);
	$builder->registerReport($r3);

	$this->assertSame($r1, $builder->setFormat('r1')->getReport());
	$this->assertSame($r2, $builder->setFormat('r2')->getReport());
	$this->assertSame($r3, $builder->setFormat('r3')->getReport());
    }

    /**
     * @covers PhpCsFixer\ReportBuilder::registerReport
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Report for format "non_unique_name" is already registered.
     */
    public function testRegisterReportWithOccupiedFormat()
    {
	$factory = new ReportBuilder();

	$r1 = $this->createReportMock('non_unique_name');
	$r2 = $this->createReportMock('non_unique_name');
	$factory->registerReport($r1);
	$factory->registerReport($r2);
    }

    /**
     * @covers PhpCsFixer\ReportBuilder::getReport
     * @expectedException        \UnexpectedValueException
     * @expectedExceptionMessage Report for format "non_registered_format" does not registered.
     */
    public function testGetNonRegisteredReport()
    {
	$builder = new ReportBuilder();

	$builder->setFormat('non_registered_format')->getReport();
    }

    /**
     * @covers PhpCsFixer\ReportBuilder::setIsDryRun
     * @covers PhpCsFixer\ReportBuilder::setIsDecoratedOutput
     * @covers PhpCsFixer\ReportBuilder::setAddAppliedFixers
     * @covers PhpCsFixer\ReportBuilder::setTime
     * @covers PhpCsFixer\ReportBuilder::setMemory
     */
    public function testSetters()
    {
	$builder = new ReportBuilder();
	$builder->registerBuiltInReports();

	$accessibleBuilder = new AccessibleObject($builder);

	$builder->setIsDryRun(true);
	$builder->setIsDecoratedOutput(true);
	$builder->setAddAppliedFixers(true);
	$builder->setTime(1234);
	$builder->setMemory(1024 * 1024);

	$expectedOptions = array(
	    'isDryRun' => true,
	    'isDecoratedOutput' => true,
	    'addAppliedFixers' => true,
	    'time' => 1234,
	    'memory' => 1048576,
	);

	$this->assertSame($expectedOptions, $accessibleBuilder->options);
    }

    private function createReportMock($format)
    {
	$report = $this->getMock('PhpCsFixer\ReportInterface');
	$report->expects($this->any())->method('getFormat')->willReturn($format);

	return $report;
    }
}
