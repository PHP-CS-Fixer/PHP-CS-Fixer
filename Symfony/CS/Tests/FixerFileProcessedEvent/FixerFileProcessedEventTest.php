<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\FixerFileProcessedEvent;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\CS\Config\Config;
use Symfony\CS\Fixer;
use Symfony\CS\FixerFileProcessedEvent;

class FixerFileProcessedEventTest extends \PHPUnit_Framework_TestCase
{
    public function testFixerEventData()
    {
        $fixer = new Fixer();

        $eventDispatcher = new EventDispatcher();
        $fixer->setEventDispatcher($eventDispatcher);

        $fixer->addFixer(new \Symfony\CS\Fixer\PSR2\VisibilityFixer());

        $config = Config::create()->finder(new \DirectoryIterator(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'FixerTest'.DIRECTORY_SEPARATOR.'fix'));
        $config->fixers($fixer->getFixers());
        $config->setUsingCache(false);

        $logger = new CustomFixerLogger($eventDispatcher);
        $changed = $fixer->fix($config, true, true);

        $this->assertCount(1, $changed);

        $fixedFilesByStatuses = $logger->getFileStatuses();
        $this->assertCount(1, $fixedFilesByStatuses);

        $this->assertArrayHasKey(FixerFileProcessedEvent::STATUS_FIXED, $fixedFilesByStatuses);

        $fixedFiles = $fixedFilesByStatuses[FixerFileProcessedEvent::STATUS_FIXED];
        $this->assertCount(1, $fixedFiles);

        $this->assertNotEmpty($fixedFiles[0]);
        $this->assertEquals('somefile.php', $fixedFiles[0]);
    }
}
