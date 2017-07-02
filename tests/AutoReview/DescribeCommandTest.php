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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\DescribeCommand;
use PhpCsFixer\FixerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @coversNothing
 * @group auto-review
 */
final class DescribeCommandTest extends TestCase
{
    /**
     * @dataProvider provideDescribeCommandCases
     *
     * @param FixerFactory $factory
     * @param string       $fixerName
     */
    public function testDescribeCommand(FixerFactory $factory, $fixerName)
    {
        $command = new DescribeCommand($factory);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'name' => $fixerName,
        ]);

        $this->assertSame(0, $commandTester->getStatusCode());
    }

    public function provideDescribeCommandCases()
    {
        $factory = new FixerFactory();
        $factory->registerBuiltInFixers();

        $cases = [];

        foreach ($factory->getFixers() as $fixer) {
            $cases[] = [$factory, $fixer->getName()];
        }

        return $cases;
    }
}
