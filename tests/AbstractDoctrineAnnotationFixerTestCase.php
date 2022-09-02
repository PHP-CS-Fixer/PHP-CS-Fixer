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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 */
abstract class AbstractDoctrineAnnotationFixerTestCase extends AbstractFixerTestCase
{
    /**
     * @param array<mixed> $configuration
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testConfigureWithInvalidConfiguration(array $configuration): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);

        $this->fixer->configure($configuration);
    }

    public function provideInvalidConfigurationCases(): array
    {
        return [
            [['foo' => 'bar']],
            [['ignored_tags' => 'foo']],
        ];
    }

    /**
     * @param list<array{0: string, 1?: string}> $commentCases
     *
     * @return list<array{0: string, 1?: string}>
     */
    protected function createTestCases(array $commentCases): array
    {
        $cases = [];
        foreach ($commentCases as $commentCase) {
            $cases[] = [
                $this->withClassDocBlock($commentCase[0]),
                isset($commentCase[1]) ? $this->withClassDocBlock($commentCase[1]) : null,
            ];

            $cases[] = [
                $this->withPropertyDocBlock($commentCase[0]),
                isset($commentCase[1]) ? $this->withPropertyDocBlock($commentCase[1]) : null,
            ];

            $cases[] = [
                $this->withMethodDocBlock($commentCase[0]),
                isset($commentCase[1]) ? $this->withMethodDocBlock($commentCase[1]) : null,
            ];

            $cases[] = [
                $this->withWrongElementDocBlock($commentCase[0]),
            ];
        }

        return $cases;
    }

    private function withClassDocBlock(string $comment): string
    {
        return $this->with('<?php

%s
class FooClass
{
}', $comment, false);
    }

    private function withPropertyDocBlock(string $comment): string
    {
        return $this->with('<?php

class FooClass
{
    %s
    private $foo;
}', $comment, true);
    }

    private function withMethodDocBlock(string $comment): string
    {
        return $this->with('<?php

class FooClass
{
    %s
    public function foo()
    {
    }
}', $comment, true);
    }

    private function withWrongElementDocBlock(string $comment): string
    {
        return $this->with('<?php

%s
$foo = bar();', $comment, false);
    }

    private function with(string $php, string $comment, bool $indent): string
    {
        $comment = trim($comment);

        if ($indent) {
            $comment = str_replace("\n", "\n    ", $comment);
        }

        return sprintf($php, preg_replace('/^\n+/', '', $comment));
    }
}
