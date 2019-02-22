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

use PhpCsFixer\Report\NullReporter;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Report\NullReporter
 */
final class NullReporterTest extends AbstractReporterTestCase
{
    public function createSimpleReport()
    {
    }

    public function createWithDiffReport()
    {
    }

    public function createWithAppliedFixersReport()
    {
    }

    public function createWithTimeAndMemoryReport()
    {
    }

    public function createComplexReport()
    {
    }

    protected function createReporter()
    {
        return new NullReporter();
    }

    protected function getFormat()
    {
        return 'null';
    }

    protected function createNoErrorReport()
    {
    }

    protected function assertFormat($expected, $input)
    {
        $this->markTestSkipped('NullReporterTest skipped');
    }
}
