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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer
 */
final class PhpdocOrderFixerTest extends AbstractFixerTestCase
{
    public function testEmptyOrderConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('The option "order" value is invalid. Minimum two tags are required.');

        $this->fixer->configure(['order' => []]);
    }

    public function testInvalidOrderConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage('The option "order" value is invalid. Minimum two tags are required.');

        $this->fixer->configure(['order' => ['param']]);
    }

    public function testNoChanges(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Do some cool stuff.
                 *
                 * @param EngineInterface $templating
                 * @param string          $name
                 *
                 * @throws Exception
                 *
                 * @return void|bar
                 */

            EOD;
        $this->doTest($expected);
    }

    /**
     * @dataProvider provideDifferentOrderCases
     *
     * @param array<string, mixed> $config
     */
    public function testOnlyParams(array $config): void
    {
        $this->fixer->configure($config);

        $expected = <<<'EOD'
            <?php
                /**
                 * @param EngineInterface $templating
                 * @param string          $name
                 */

            EOD;
        $this->doTest($expected);
    }

    /**
     * @dataProvider provideDifferentOrderCases
     *
     * @param array<string, mixed> $config
     */
    public function testOnlyReturns(array $config): void
    {
        $this->fixer->configure($config);

        $expected = <<<'EOD'
            <?php
                /**
                 *
                 * @return void|bar
                 *
                 */

            EOD;
        $this->doTest($expected);
    }

    /**
     * @dataProvider provideDifferentOrderCases
     *
     * @param array<string, mixed> $config
     */
    public function testEmpty(array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest('/***/');
    }

    /**
     * @dataProvider provideDifferentOrderCases
     *
     * @param array<string, mixed> $config
     */
    public function testNoAnnotations(array $config): void
    {
        $this->fixer->configure($config);

        $expected = <<<'EOD'
            <?php
                /**
                 *
                 *
                 *
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testFixBasicCase(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @param string $foo
                 * @throws Exception
                 * @return bool
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @throws Exception
                 * @return bool
                 * @param string $foo
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixCompleteCase(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 *
                 * Long description
                 * goes here.
                 *
                 * @internal
                 *
                 *
                 * @custom Test!
                 *         asldnaksdkjasdasd
                 *
                 *
                 *
                 * @param string $foo
                 * @param bool   $bar Bar
                 * @throws Exception|RuntimeException dfsdf
                 *         jkaskdnaksdnkasndansdnansdajsdnkasd
                 * @return bool Return false on failure.
                 * @return int  Return the number of changes.
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 *
                 * Long description
                 * goes here.
                 *
                 * @internal
                 *
                 * @throws Exception|RuntimeException dfsdf
                 *         jkaskdnaksdnkasndansdnansdajsdnkasd
                 *
                 * @custom Test!
                 *         asldnaksdkjasdasd
                 *
                 *
                 * @return bool Return false on failure.
                 * @return int  Return the number of changes.
                 *
                 * @param string $foo
                 * @param bool   $bar Bar
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testExampleFromSymfony(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Renders a template.
                 *
                 * @param mixed $name       A template name
                 * @param array $parameters An array of parameters to pass to the template
                 *
                 * @throws \InvalidArgumentException if the template does not exist
                 * @throws \RuntimeException         if the template cannot be rendered
                 * @return string The evaluated template as a string
                 *
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Renders a template.
                 *
                 * @param mixed $name       A template name
                 * @param array $parameters An array of parameters to pass to the template
                 *
                 * @return string The evaluated template as a string
                 *
                 * @throws \InvalidArgumentException if the template does not exist
                 * @throws \RuntimeException         if the template cannot be rendered
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testNoChangesWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $expected = <<<'EOD'
            <?php
                /**
                 * Do some cool stuff.
                 *
                 * @param EngineInterface $templating
                 * @param string          $name
                 *
                 * @return void|bar
                 *
                 * @throws Exception
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testFixBasicCaseWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $expected = <<<'EOD'
            <?php
                /**
                 * @param string $foo
                 * @return bool
                 * @throws Exception
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * @throws Exception
                 * @return bool
                 * @param string $foo
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixCompleteCaseWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 *
                 * Long description
                 * goes here.
                 *
                 * @internal
                 *
                 *
                 * @custom Test!
                 *         asldnaksdkjasdasd
                 *
                 *
                 *
                 * @param string $foo
                 * @param bool   $bar Bar
                 * @return bool Return false on failure.
                 * @return int  Return the number of changes.
                 * @throws Exception|RuntimeException dfsdf
                 *         jkaskdnaksdnkasndansdnansdajsdnkasd
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 *
                 * Long description
                 * goes here.
                 *
                 * @internal
                 *
                 * @throws Exception|RuntimeException dfsdf
                 *         jkaskdnaksdnkasndansdnansdajsdnkasd
                 *
                 * @custom Test!
                 *         asldnaksdkjasdasd
                 *
                 *
                 * @return bool Return false on failure.
                 * @return int  Return the number of changes.
                 *
                 * @param string $foo
                 * @param bool   $bar Bar
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testExampleFromSymfonyWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $input = <<<'EOD'
            <?php
                /**
                 * Renders a template.
                 *
                 * @param mixed $name       A template name
                 * @param array $parameters An array of parameters to pass to the template
                 *
                 * @return string The evaluated template as a string
                 *
                 * @throws \InvalidArgumentException if the template does not exist
                 * @throws \RuntimeException         if the template cannot be rendered
                 */

            EOD;

        $this->doTest($input);
    }

    /**
     * @return iterable<array{array<string, mixed>}>
     */
    public static function provideDifferentOrderCases(): iterable
    {
        yield [['order' => ['param', 'throw', 'return']]];

        yield [['order' => ['param', 'return', 'throw']]];
    }

    /**
     * @dataProvider provideFixBasicCaseWithDifferentOrdersCases
     *
     * @param array<string, mixed> $config
     */
    public function testFixBasicCaseWithDifferentOrders(string $expected, ?string $input = null, ?array $config = null): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixBasicCaseWithDifferentOrdersCases(): iterable
    {
        $input = <<<'EOD'
            <?php
                /**
                 * @throws Exception
                 * @return bool
                 * @param string $foo
                 */

            EOD;

        yield [
            <<<'EOD'
                <?php
                    /**
                     * @return bool
                     * @throws Exception
                     * @param string $foo
                     */

                EOD,
            $input,
            ['order' => ['return', 'throws', 'param']],
        ];

        yield [
            <<<'EOD'
                <?php
                    /**
                     * @throws Exception
                     * @return bool
                     * @param string $foo
                     */

                EOD,
            null,
            ['order' => ['throws', 'return', 'param']],
        ];
    }

    public function testFixCompleteCaseWithCustomOrder(): void
    {
        $this->fixer->configure(['order' => [
            'throws',
            'return',
            'param',
            'custom',
            'internal',
        ]]);

        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 *
                 * Long description
                 * goes here.
                 *
                 *
                 * @throws Exception|RuntimeException dfsdf
                 *         jkaskdnaksdnkasndansdnansdajsdnkasd
                 *
                 *
                 *
                 * @return bool Return false on failure.
                 * @return int  Return the number of changes.
                 *
                 * @param string $foo
                 * @param bool   $bar Bar
                 * @custom Test!
                 *         asldnaksdkjasdasd
                 * @internal
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello there!
                 *
                 * Long description
                 * goes here.
                 *
                 * @internal
                 *
                 * @throws Exception|RuntimeException dfsdf
                 *         jkaskdnaksdnkasndansdnansdajsdnkasd
                 *
                 * @custom Test!
                 *         asldnaksdkjasdasd
                 *
                 *
                 * @return bool Return false on failure.
                 * @return int  Return the number of changes.
                 *
                 * @param string $foo
                 * @param bool   $bar Bar
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixCompleteCasesWithCustomOrdersCases
     *
     * @param array<string, mixed> $config
     */
    public function testFixCompleteCasesWithCustomOrders(string $expected, string $input, array $config): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: ?string, 2?: array<string, mixed>}>
     */
    public static function provideFixCompleteCasesWithCustomOrdersCases(): iterable
    {
        yield 'intepacuthre' => [
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     **/

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            ['order' => ['internal', 'template', 'param', 'custom', 'throws', 'return']],
        ];

        yield 'pare' => [
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            ['order' => ['param', 'return']],
        ];

        yield 'pareth' => [
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            ['order' => ['param', 'return', 'throws']],
        ];

        yield 'pathre' => [
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     **/

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            ['order' => ['param', 'throws', 'return']],
        ];

        yield 'tepathre' => [
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     **/

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @template T of Extension\Extension
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            ['order' => ['template', 'param', 'throws', 'return']],
        ];

        yield 'tepathre2' => [
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @template T of Extension\Extension
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     **/

                EOD,
            <<<'EOD'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @internal
                     * @param string $foo
                     * @param bool   $bar Bar
                     * @param class-string<T> $id
                     * @return bool Return false on failure
                     * @return int  Return the number of changes.
                     * @template T of Extension\Extension
                     * @custom Test!
                     *         asldnaksdkjasdasd
                     * @throws Exception|RuntimeException dfsdf
                     *         jkaskdnaksdnkasndansdnansdajsdnkasd
                     **/

                EOD,
            ['order' => ['template', 'param', 'throws', 'return']],
        ];
    }
}
