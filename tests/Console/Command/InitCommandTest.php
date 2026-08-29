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

use PhpCsFixer\Console\Command\InitCommand;
use PhpCsFixer\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

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
     * @param list<string> $expectedRules   rules expected in the prepared configuration
     * @param list<string> $unexpectedRules rules expected to _not_ be in the prepared configuration
     *
     * @dataProvider provideConfigurationIsPreparedCases
     */
    #[DataProvider('provideConfigurationIsPreparedCases')]
    public function testConfigurationIsPrepared(
        array $inputs,
        array $expectedRules,
        array $unexpectedRules,
        bool $isCallTypeQuestionExpected
    ): void {
        $output = new BufferedOutput();
        $configuration = self::prepareConfigurationFileContent($inputs, $output);

        foreach ($expectedRules as $expectedRule) {
            self::assertStringContainsString($expectedRule, $configuration);
        }

        foreach ($unexpectedRules as $unexpectedRule) {
            self::assertStringNotContainsString($unexpectedRule, $configuration);
        }

        self::assertSame(
            $isCallTypeQuestionExpected,
            str_contains($output->fetch(), 'Which call type do you use for PHPUnit methods?'),
        );
    }

    /**
     * @return iterable<string, array{list<string>, list<string>, list<string>, bool}>
     */
    public static function provideConfigurationIsPreparedCases(): iterable
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

    /**
     * @param list<string> $inputs answers to the questions asked by the command
     */
    private static function prepareConfigurationFileContent(array $inputs, BufferedOutput $output): string
    {
        $input = new ArrayInput([]);
        $input->setInteractive(true);
        $input->setStream(self::createInputStream($inputs));

        $io = new SymfonyStyle($input, $output);

        return \Closure::bind(
            static fn (InitCommand $command): string => $command->prepareConfigurationFileContent($io),
            null,
            InitCommand::class,
        )(new InitCommand());
    }

    /**
     * @param list<string> $inputs
     *
     * @return resource
     */
    private static function createInputStream(array $inputs)
    {
        $stream = fopen('php://memory', 'r+');

        if (false === $stream) {
            throw new \RuntimeException('Unable to open the input stream.');
        }

        fwrite($stream, implode(\PHP_EOL, $inputs).\PHP_EOL);
        rewind($stream);

        return $stream;
    }
}
