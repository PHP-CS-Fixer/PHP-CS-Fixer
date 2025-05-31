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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class TestCase extends BaseTestCase
{
    /** @var null|callable */
    private $previouslyDefinedErrorHandler;

    /** @var list<string> */
    private array $expectedDeprecations = [];

    /** @var list<string> */
    private array $actualDeprecations = [];

    protected function tearDown(): void
    {
        if (null !== $this->previouslyDefinedErrorHandler) {
            $this->actualDeprecations = array_unique($this->actualDeprecations);
            sort($this->actualDeprecations);
            $this->expectedDeprecations = array_unique($this->expectedDeprecations);
            sort($this->expectedDeprecations);
            self::assertSame($this->expectedDeprecations, $this->actualDeprecations);

            restore_error_handler();
        }

        parent::tearDown();
    }

    final public function testNotDefiningConstructor(): void
    {
        $reflection = new \ReflectionObject($this);

        self::assertNotSame(
            $reflection->getConstructor()->getDeclaringClass()->getName(),
            $reflection->getName(),
        );
    }

    /**
     * Mark test to expect given deprecation. Order or repetition count of expected vs actual deprecation usage can vary, but result sets must be identical.
     *
     * @TODO change access to protected and pass the parameter when PHPUnit 9 support is dropped
     */
    public function expectDeprecation(/* string $message */): void
    {
        $this->expectedDeprecations[] = func_get_arg(0);

        if (null === $this->previouslyDefinedErrorHandler) {
            $this->previouslyDefinedErrorHandler = set_error_handler(
                function (
                    int $code,
                    string $message
                ) {
                    if (E_USER_DEPRECATED === $code || E_DEPRECATED === $code) {
                        $this->actualDeprecations[] = $message;
                    }

                    return true;
                }
            );
        }
    }

    /**
     * @return non-empty-list<numeric-string>
     */
    final protected function getAllPhpVersionsUsedByCiForTests(): array
    {
        $yaml = Yaml::parseFile(__DIR__.'/../.github/workflows/ci.yml');

        $phpVersions = [];
        foreach ($yaml['jobs']['tests']['strategy']['matrix']['include'] as $job) {
            $phpVersions[] = $job['php-version'];
        }

        return array_unique($phpVersions); // @phpstan-ignore return.type (we know it's a list of parsed strings)
    }

    final protected static function getMaxPhpVersionFromEntryFile(): string
    {
        return self::convertPhpVersionIdToMajorMinorFormat((string) ((int) self::getPhpVersionFromEntryFileThatFollowsOperator([T_IS_GREATER_OR_EQUAL]) - 100));
    }

    final protected static function getMinPhpVersionFromEntryFile(): string
    {
        return self::convertPhpVersionIdToMajorMinorFormat(self::getPhpVersionFromEntryFileThatFollowsOperator('<'));
    }

    final protected static function convertPhpVersionIdToMajorMinorFormat(string $verId): string
    {
        $matchResult = Preg::match('/^(?<major>\d{1,2})_?(?<minor>\d{2})_?(?<patch>\d{2})$/', $verId, $capture);
        if (!$matchResult) {
            throw new \LogicException(\sprintf('Can\'t parse version "%s" id.', $verId));
        }

        return \sprintf('%d.%d', $capture['major'], $capture['minor']);
    }

    /**
     * @param array{int}|string $operatorToken
     */
    private static function getPhpVersionFromEntryFileThatFollowsOperator($operatorToken): string
    {
        $tokens = Tokens::fromCode((string) file_get_contents(__DIR__.'/../php-cs-fixer'));
        $sequence = $tokens->findSequence([
            [T_STRING, 'PHP_VERSION_ID'],
            $operatorToken,
            [T_INT_CAST],
            [T_CONSTANT_ENCAPSED_STRING],
        ]);

        if (null === $sequence) {
            throw new \LogicException("Can't find version - perhaps entry file was modified?");
        }

        return trim(end($sequence)->getContent(), '\'');
    }
}
