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

namespace PhpCsFixer\Tests\AutoReview\PhpStan;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final readonly class FixerTestNamingConventionRule implements Rule
{
    private const METHOD_TEST_APPLY_FIX = 'testApplyFix';

    public function __construct(private Lexer $phpDocLexer, private PhpDocParser $phpDocParser) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // Ignore every class that does not extend our base test case for fixers
        if (null === $node->extends
            || $node->isAnonymous()
            || AbstractFixerTestCase::class !== (string) $node->extends
        ) {
            return [];
        }

        $errors = [];
        $methods = $node->getMethods() ?? [];
        $methodNames = array_map(static fn (ClassMethod $method) => (string) $method->name, $methods);

        // Every fixers' test must have `testApplyFix` method
        if ([] === $methodNames || !\in_array(self::METHOD_TEST_APPLY_FIX, $methodNames, true)) {
            $errors[] = RuleErrorBuilder::message(sprintf(
                'Base test `%s` is missing',
                self::METHOD_TEST_APPLY_FIX
            ))
                ->identifier('php_cs_fixer.tests_convention.base_test_missing')
                ->line($node->getLine())
                ->build()
            ;
        }

        foreach ($methods as $method) {
            // Ensure basic naming convention
            if ($method->isPublic() && !preg_match('/^(test|provide)+.*/', (string) $method->name)) {
                $errors[] = RuleErrorBuilder::message(sprintf(
                    'Method name `%s` is invalid',
                    (string) $method->name
                ))
                    ->identifier('php_cs_fixer.tests_convention.invalid_method_name')
                    ->line($method->getLine())
                    ->addTip('Only `test*` and `provide*` methods are allowed')
                    ->build()
                ;
            }

            $methodDoc = $method->getDocComment();
            if (null !== $methodDoc) {
                $phpDocString = $methodDoc->getText();
                $tokens = new TokenIterator($this->phpDocLexer->tokenize($phpDocString));
                $phpDocNode = $this->phpDocParser->parse($tokens);

                foreach ($phpDocNode->getTagsByName('@requires') as $tag) {
                    if (!str_starts_with(strtolower((string) $tag->value), 'php ')) {
                        continue;
                    }

                    preg_match(
                        '/PHP\s+(?<operator>[\<\>=\!]{1,2})?\s?(?<version>[0-9\.]+)/',
                        (string) $tag->value,
                        $matches
                    );
                    $expectedMethodName = sprintf(
                        '%sOnPhp%s%s',
                        self::METHOD_TEST_APPLY_FIX,
                        $this->descriptionForVersionOperator($matches['operator'] ?? '>='),
                        // TODO use X_Y_Z format instead of XYZ (make versions more clear)
                        str_replace('.', '', $matches['version'])
                    );

                    if ((string) $method->name !== $expectedMethodName) {
                        $errors[] = RuleErrorBuilder::message(sprintf(
                            'Version-specific test should be named `%s`, but is named `%s`',
                            $expectedMethodName,
                            (string) $method->name
                        ))
                            ->identifier('php_cs_fixer.tests_convention.invalid_version_specific_method_name')
                            ->line($method->getLine())
                            ->build()
                        ;
                    }
                }
            }
        }

        return $errors;
    }

    private function descriptionForVersionOperator(string $operator): string
    {
        switch ($operator) {
            case '<': return 'LowerThan';

            case '<=': return 'LowerOrEqualTo';

            case '>': return 'HigherThan';

            case '=':
            case '==':
                return 'EqualTo';

            case '!=':
            case '<>':
                return 'OtherThan';

            case '>=':
            case '':
            default:
                return 'HigherOrEqualTo';
        }
    }
}
