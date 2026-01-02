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

namespace PhpCsFixer\Tests\AutoReview;

use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 *
 * @group auto-review
 * @group covers-nothing
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class DuplicatedTestsTest extends TestCase
{
    /**
     * @dataProvider \PhpCsFixer\Tests\AutoReview\ProjectCodeTest::provideTestClassCases
     *
     * @param class-string $className
     */
    public function testThatTestMethodsAreNotDuplicatedBasedOnContent(string $className): void
    {
        $alreadyFoundMethods = [];
        $duplicates = [];
        foreach (self::getMethodsForDuplicateCheck($className) as $method) {
            if (!str_starts_with($method->getName(), 'test')) {
                continue;
            }

            $startLine = (int) $method->getStartLine();
            $length = (int) $method->getEndLine() - $startLine;
            if (3 === $length) { // open and closing brace are included - this checks for single line methods
                continue;
            }

            /** @var list<string> $source */
            $source = file((string) $method->getFileName());

            $candidateContent = implode('', \array_slice($source, $startLine, $length));
            if (str_contains($candidateContent, '$this->doTest(')) {
                continue;
            }

            $foundInDuplicates = false;
            foreach ($alreadyFoundMethods as $methodKey => $methodContent) {
                if ($candidateContent === $methodContent) {
                    $duplicates[] = \sprintf('%s is duplicate of %s', $methodKey, $method->getName());
                    $foundInDuplicates = true;
                }
            }
            if (!$foundInDuplicates) {
                $alreadyFoundMethods[$method->getName()] = $candidateContent;
            }
        }

        self::assertSame(
            [],
            $duplicates,
            \sprintf(
                "Duplicated methods found in %s:\n - %s",
                $className,
                implode("\n - ", $duplicates),
            ),
        );
    }

    /**
     * @dataProvider \PhpCsFixer\Tests\AutoReview\ProjectCodeTest::provideTestClassCases
     *
     * @param class-string $className
     */
    public function testThatTestMethodsAreNotDuplicatedBasedOnName(string $className): void
    {
        $alreadyFoundMethods = [];
        $duplicates = [];
        foreach (self::getMethodsForDuplicateCheck($className) as $method) {
            foreach ($alreadyFoundMethods as $alreadyFoundMethod) {
                if (!str_starts_with($method->getName(), $alreadyFoundMethod)) {
                    continue;
                }

                $suffix = substr($method->getName(), \strlen($alreadyFoundMethod));

                if (!Preg::match('/^\d{2,}/', $suffix)) {
                    continue;
                }

                $duplicates[] = \sprintf(
                    'Method "%s" must be shorter, call "%s".',
                    $method->getName(),
                    $alreadyFoundMethod,
                );
            }
            $alreadyFoundMethods[] = $method->getName();
        }

        self::assertSame(
            [],
            $duplicates,
            \sprintf(
                "Duplicated methods found in %s:\n - %s",
                $className,
                implode("\n - ", $duplicates),
            ),
        );
    }

    /**
     * @param class-string $className
     *
     * @return list<\ReflectionMethod>
     */
    private static function getMethodsForDuplicateCheck(string $className): array
    {
        static $methodsForDuplicateCheckCache = [];

        if (!isset($methodsForDuplicateCheckCache[$className])) {
            $class = new \ReflectionClass($className);

            $methodsForDuplicateCheck = array_filter(
                $class->getMethods(\ReflectionMethod::IS_PUBLIC),
                static fn (\ReflectionMethod $method) => str_starts_with($method->getName(), 'test')
                && $method->getDeclaringClass()->getName() === $className
                /*
                 * Why 4?
                 * Open and closing brace are included, this checks for:
                 *  - single line methods
                 *  - single line methods with configs
                 */
                && 4 < (int) $method->getEndLine() - (int) $method->getStartLine(),
            );

            usort(
                $methodsForDuplicateCheck,
                static fn (\ReflectionMethod $method1, \ReflectionMethod $method2) => $method1->getName() <=> $method2->getName(),
            );

            $methodsForDuplicateCheckCache[$className] = $methodsForDuplicateCheck;
        }

        return $methodsForDuplicateCheckCache[$className];
    }
}
