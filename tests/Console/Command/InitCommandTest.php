<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Console\Command;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Console\Command\InitCommand;
use PhpCsFixer\Tests\Test\TestCaseUtils;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Console\Command\InitCommand
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
#[CoversClass(InitCommand::class)]
final class InitCommandTest extends TestCase
{
    /**
     * @param list<string> $inputs          answers to the questions asked by the command
     * @param list<string> $expectedRules   rules expected in the created configuration file
     * @param list<string> $unexpectedRules rules expected to _not_ be in the created configuration file
     *
     * @dataProvider provideConfigurationIsCreatedCases
     */
    #[DataProvider('provideConfigurationIsCreatedCases')]
    public function testConfigurationIsCreated(
        array $inputs,
        array $expectedRules,
        array $unexpectedRules,
        bool $isCallTypeQuestionExpected
    ): void {
        $commandTester = self::executeInTemporaryDirectory($inputs, $configuration);

        self::assertSame(Command::SUCCESS, $commandTester->getStatusCode());

        foreach ($expectedRules as $expectedRule) {
            self::assertStringContainsString($expectedRule, $configuration);
        }

        foreach ($unexpectedRules as $unexpectedRule) {
            self::assertStringNotContainsString($unexpectedRule, $configuration);
        }

        self::assertSame(
            $isCallTypeQuestionExpected,
            str_contains($commandTester->getDisplay(), 'Which call type do you use for PHPUnit methods?'),
        );
    }

    /**
     * @return iterable<string, array{list<string>, list<string>, list<string>, bool}>
     */
    public static function provideConfigurationIsCreatedCases(): iterable
    {
        yield 'a call type is picked' => [
            ['yes', 'yes', 'none', 'this', 'yes'],
            ["'php_unit_test_case_static_method_calls' => ['call_type' => 'this']"],
            [],
            true,
        ];

        yield 'another call type is picked' => [
            ['yes', 'yes', 'none', 'self', 'yes'],
            ["'php_unit_test_case_static_method_calls' => ['call_type' => 'self']"],
            [],
            true,
        ];

        yield 'no call type is enforced' => [
            ['yes', 'yes', 'none', 'none', 'yes'],
            ["'@auto' => true", "'@auto:risky' => true"],
            ['php_unit_test_case_static_method_calls', "'none'"],
            true,
        ];

        yield 'an extra ruleset is picked next to the automatic ones' => [
            ['yes', 'yes', '@Symfony', 'this', 'yes'],
            [
                "'@auto' => true",
                "'@Symfony' => true",
                "'php_unit_test_case_static_method_calls' => ['call_type' => 'this']",
            ],
            [],
            true,
        ];

        yield 'risky rules are not allowed' => [
            ['no', 'yes', 'none', 'yes'],
            ['setRiskyAllowed(false)', "'@auto' => true"],
            ['php_unit_test_case_static_method_calls', "'@auto:risky'"],
            false,
        ];
    }

    public function testConfigurationIsNotOverwritten(): void
    {
        $commandTester = self::executeInTemporaryDirectory(
            ['yes', 'yes', 'none', 'this', 'yes'],
            $configuration,
            'already there',
        );

        self::assertSame(Command::FAILURE, $commandTester->getStatusCode());
        self::assertSame('already there', $configuration);
    }

    /**
     * @param list<string> $inputs                answers to the questions asked by the command
     * @param null|string  $configuration         content of the created configuration file
     * @param null|string  $existingConfiguration content of the configuration file to create upfront, if any
     */
    private static function executeInTemporaryDirectory(
        array $inputs,
        ?string &$configuration,
        ?string $existingConfiguration = null
    ): CommandTester {
        $originalDirectory = getcwd();
        if (false === $originalDirectory) {
            throw new \RuntimeException('Unable to determine current working directory.');
        }

        $temporaryDirectory = TestCaseUtils::createTemporaryDirectory();

        try {
            // the command creates its file in the current working directory
            chdir($temporaryDirectory);

            if (null !== $existingConfiguration) {
                file_put_contents('.php-cs-fixer.dist.php', $existingConfiguration);
            }

            $application = new Application();
            $application->add(new InitCommand());

            $commandTester = new CommandTester($application->find('init'));
            $commandTester->setInputs($inputs);
            $commandTester->execute([]);

            $readResult = @file_get_contents('.php-cs-fixer.dist.php');
            $configuration = false === $readResult ? null : $readResult;

            return $commandTester;
        } finally {
            if (file_exists('.php-cs-fixer.dist.php')) {
                unlink('.php-cs-fixer.dist.php');
            }

            chdir($originalDirectory);
            rmdir($temporaryDirectory);
        }
    }
}
