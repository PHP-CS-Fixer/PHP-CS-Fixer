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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @author Gert de Pagter
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer
 */
final class GeneralPhpdocAnnotationRemoveFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'An Annotation gets removed' => [
            <<<'EOD'
                <?php
                /**
                 * @internal
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @internal
                 * @param string $name
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
            ['annotations' => ['param']],
        ];

        yield 'It removes multiple annotations' => [
            <<<'EOD'
                <?php
                /**
                 * @author me
                 * @internal
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @author me
                 * @internal
                 * @param string $name
                 * @return string
                 * @throws \Exception
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
            ['annotations' => ['param', 'return', 'throws']],
        ];

        yield 'It does nothing if no configuration is given' => [
            <<<'EOD'
                <?php
                /**
                 * @author me
                 * @internal
                 * @param string $name
                 * @return string
                 * @throws \Exception
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
        ];

        yield 'It works on multiple functions' => [
            <<<'EOD'
                <?php
                /**
                 * @param string $name
                 * @throws \Exception
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                /**
                 */
                function goodBye()
                {
                    return 0;
                }
                function noComment()
                {
                    callOtherFunction();
                }
                EOD,
            <<<'EOD'
                <?php
                /**
                 * @author me
                 * @internal
                 * @param string $name
                 * @return string
                 * @throws \Exception
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                /**
                 * @internal
                 * @author Piet-Henk
                 * @return int
                 */
                function goodBye()
                {
                    return 0;
                }
                function noComment()
                {
                    callOtherFunction();
                }
                EOD,
            ['annotations' => ['author', 'return', 'internal']],
        ];

        yield 'Nothing happens to non doc-block comments' => [
            <<<'EOD'
                <?php
                /*
                 * @internal
                 * @param string $name
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
            null,
            ['annotations' => ['internal', 'param', 'return']],
        ];

        yield 'Nothing happens if to be deleted annotations are not present' => [
            <<<'EOD'
                <?php
                /**
                 * @internal
                 * @param string $name
                 */
                function hello($name)
                {
                    return "hello " . $name;
                }
                EOD,
            null,
            ['annotations' => ['author', 'test', 'return', 'deprecated']],
        ];

        yield [
            <<<'EOD'
                <?php

                while ($something = myFunction($foo)) {}

                EOD,
            <<<'EOD'
                <?php
                /** @noinspection PhpAssignmentInConditionInspection */
                while ($something = myFunction($foo)) {}

                EOD,
            ['annotations' => ['noinspection']],
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                * @internal
                * @AuThOr Jane Doe
                */
                function foo() {}
                EOD,
            <<<'EOD'
                <?php
                /**
                * @internal
                * @author John Doe
                * @AuThOr Jane Doe
                */
                function foo() {}
                EOD,
            ['annotations' => ['author'], 'case_sensitive' => true],
        ];

        yield [
            <<<'EOD'
                <?php
                /**
                * @internal
                */
                function foo() {}
                EOD,
            <<<'EOD'
                <?php
                /**
                * @internal
                * @author John Doe
                * @AuThOr Jane Doe
                */
                function foo() {}
                EOD,
            ['annotations' => ['author'], 'case_sensitive' => false],
        ];
    }
}
