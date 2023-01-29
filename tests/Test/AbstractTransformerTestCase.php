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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Tests\TestCase;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TransformerInterface;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
abstract class AbstractTransformerTestCase extends TestCase
{
    /**
     * @var null|TransformerInterface
     */
    protected $transformer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer = $this->createTransformer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->transformer = null;
    }

    public function testGetPriority(): void
    {
        static::assertIsInt($this->transformer->getPriority(), $this->transformer->getName());
    }

    public function testGetName(): void
    {
        $name = $this->transformer->getName();

        static::assertMatchesRegularExpression('/^[a-z]+[a-z_]*[a-z]$/', $name);
    }

    public function testGetCustomTokens(): void
    {
        $name = $this->transformer->getName();
        $customTokens = $this->transformer->getCustomTokens();

        static::assertIsArray($customTokens, $name);

        foreach ($customTokens as $customToken) {
            static::assertIsInt($customToken, $name);
        }
    }

    public function testGetRequiredPhpVersionId(): void
    {
        $name = $this->transformer->getName();
        $requiredPhpVersionId = $this->transformer->getRequiredPhpVersionId();

        static::assertIsInt($requiredPhpVersionId, $name);
        static::assertGreaterThanOrEqual(5_00_00, $requiredPhpVersionId, $name);
    }

    public function testTransformersIsFinal(): void
    {
        $transformerRef = new \ReflectionClass($this->transformer);

        static::assertTrue(
            $transformerRef->isFinal(),
            sprintf('Transformer "%s" must be declared "final."', $this->transformer->getName())
        );
    }

    public function testTransformDoesNotChangeSimpleCode(): void
    {
        if (\PHP_VERSION_ID < $this->transformer->getRequiredPhpVersionId()) {
            $this->expectNotToPerformAssertions();

            return;
        }

        Tokens::clearCache();
        $tokens = Tokens::fromCode('<?php ');

        foreach ($tokens as $index => $token) {
            $this->transformer->process($tokens, $token, $index);
        }

        static::assertFalse($tokens->isChanged());
    }

    /**
     * @param array<int, int|string> $expectedTokens
     * @param list<int>              $observedKindsOrPrototypes
     */
    protected function doTest(string $source, array $expectedTokens, array $observedKindsOrPrototypes = []): void
    {
        Tokens::clearCache();
        $tokens = new TokensWithObservedTransformers();
        $tokens->setCode($source);

        static::assertSame(
            \count($expectedTokens),
            $this->countTokenPrototypes(
                $tokens,
                array_map(
                    static function ($kindOrPrototype) {
                        return \is_int($kindOrPrototype) ? [$kindOrPrototype] : $kindOrPrototype;
                    },
                    array_unique(array_merge($observedKindsOrPrototypes, $expectedTokens))
                )
            ),
            'Number of expected tokens does not match actual token count.'
        );

        $transformerName = $this->transformer->getName();
        $customTokensOfTransformer = $this->transformer->getCustomTokens();

        foreach ($customTokensOfTransformer as $customTokenOfTransformer) {
            static::assertTrue(CT::has($customTokenOfTransformer), sprintf('Unknown custom token id "%d" in "%s".', $transformerName, $customTokenOfTransformer));
            static::assertStringStartsWith('CT::', CT::getName($customTokenOfTransformer));
        }

        $customTokensOfTransformerList = implode(', ', array_map(
            static fn (int $ct): string => CT::getName($ct),
            $customTokensOfTransformer,
        ));

        foreach ($tokens->observedModificationsPerTransformer as $appliedTransformerName => $modificationsOfTransformer) {
            foreach ($modificationsOfTransformer as $modification) {
                $customTokenName = Token::getNameForId($modification);

                if ($appliedTransformerName === $transformerName) {
                    static::assertContains(
                        $modification,
                        $customTokensOfTransformer,
                        sprintf(
                            'Transformation into "%s" must be allowed in self-documentation of the Transformer, currently allowed custom tokens are: %s',
                            $customTokenName,
                            $customTokensOfTransformerList
                        )
                    );
                } else {
                    static::assertNotContains(
                        $modification,
                        $customTokensOfTransformer,
                        sprintf(
                            'Transformation into "%s" must NOT be applied by other Transformer than "%s".',
                            $customTokenName,
                            $transformerName
                        )
                    );
                }
            }
        }

        foreach ($expectedTokens as $index => $tokenIdOrContent) {
            if (\is_string($tokenIdOrContent)) {
                static::assertTrue($tokens[$index]->equals($tokenIdOrContent), sprintf('The token at index %d should be %s, got %s', $index, json_encode($tokenIdOrContent), $tokens[$index]->toJson()));

                continue;
            }

            static::assertSame(
                CT::has($tokenIdOrContent) ? CT::getName($tokenIdOrContent) : token_name($tokenIdOrContent),
                $tokens[$index]->getName(),
                sprintf('Token name should be the same. Got token "%s" at index %d.', $tokens[$index]->toJson(), $index)
            );

            static::assertSame(
                $tokenIdOrContent,
                $tokens[$index]->getId(),
                sprintf('Token id should be the same. Got token "%s" at index %d.', $tokens[$index]->toJson(), $index)
            );
        }
    }

    /**
     * @param list<array{0: int, 1?: string}> $prototypes
     */
    private function countTokenPrototypes(Tokens $tokens, array $prototypes): int
    {
        $count = 0;

        foreach ($tokens as $token) {
            if ($token->equalsAny($prototypes)) {
                ++$count;
            }
        }

        return $count;
    }

    private function createTransformer(): TransformerInterface
    {
        $transformerClassName = preg_replace('/^(PhpCsFixer)\\\\Tests(\\\\.+)Test$/', '$1$2', static::class);

        return new $transformerClassName();
    }
}
