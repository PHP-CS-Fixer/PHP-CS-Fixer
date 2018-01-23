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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 */
abstract class AbstractDoctrineAnnotationFixerTestCase extends AbstractFixerTestCase
{
    /**
     * @param array $configuration
     *
     * @dataProvider provideInvalidConfigurationCases
     */
    public function testConfigureWithInvalidConfiguration(array $configuration)
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);

        $this->fixer->configure($configuration);
    }

    /**
     * @return array
     */
    public function provideInvalidConfigurationCases()
    {
        return [
            [['foo' => 'bar']],
            [['ignored_tags' => 'foo']],
        ];
    }

    /**
     * @param array<array<string>> $commentCases
     *
     * @return array
     */
    protected function createTestCases(array $commentCases)
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

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withClassDocBlock($comment)
    {
        return $this->with('<?php

%s
class FooClass
{
}', $comment, false);
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withPropertyDocBlock($comment)
    {
        return $this->with('<?php

class FooClass
{
    %s
    private $foo;
}', $comment, true);
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withMethodDocBlock($comment)
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

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withWrongElementDocBlock($comment)
    {
        return $this->with('<?php

%s
$foo = bar();', $comment, false);
    }

    /**
     * @param string $php
     * @param string $comment
     * @param bool   $indent
     *
     * @return string
     */
    private function with($php, $comment, $indent)
    {
        $comment = trim($comment);

        if ($indent) {
            $comment = str_replace("\n", "\n    ", $comment);
        }

        return sprintf($php, preg_replace('/^\n+/', '', $comment));
    }
}
