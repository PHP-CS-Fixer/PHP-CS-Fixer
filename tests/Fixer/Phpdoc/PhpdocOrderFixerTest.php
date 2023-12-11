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
        $expected = <<<'EOF'
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

            EOF;
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

        $expected = <<<'EOF'
            <?php
                /**
                 * @param EngineInterface $templating
                 * @param string          $name
                 */

            EOF;
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

        $expected = <<<'EOF'
            <?php
                /**
                 *
                 * @return void|bar
                 *
                 */

            EOF;
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

        $expected = <<<'EOF'
            <?php
                /**
                 *
                 *
                 *
                 */

            EOF;
        $this->doTest($expected);
    }

    public function testFixBasicCase(): void
    {
        $expected = <<<'EOF'
            <?php
                /**
                 * @param string $foo
                 * @throws Exception
                 * @return bool
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 * @throws Exception
                 * @return bool
                 * @param string $foo
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixCompleteCase(): void
    {
        $expected = <<<'EOF'
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

            EOF;

        $input = <<<'EOF'
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

            EOF;

        $this->doTest($expected, $input);
    }

    public function testExampleFromSymfony(): void
    {
        $expected = <<<'EOF'
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

            EOF;

        $input = <<<'EOF'
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

            EOF;

        $this->doTest($expected, $input);
    }

    public function testNoChangesWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $expected = <<<'EOF'
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

            EOF;
        $this->doTest($expected);
    }

    public function testFixBasicCaseWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $expected = <<<'EOF'
            <?php
                /**
                 * @param string $foo
                 * @return bool
                 * @throws Exception
                 */

            EOF;

        $input = <<<'EOF'
            <?php
                /**
                 * @throws Exception
                 * @return bool
                 * @param string $foo
                 */

            EOF;

        $this->doTest($expected, $input);
    }

    public function testFixCompleteCaseWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $expected = <<<'EOF'
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

            EOF;

        $input = <<<'EOF'
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

            EOF;

        $this->doTest($expected, $input);
    }

    public function testExampleFromSymfonyWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => ['param', 'return', 'throws']]);

        $input = <<<'EOF'
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

            EOF;

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
        $input = <<<'EOF'
            <?php
                /**
                 * @throws Exception
                 * @return bool
                 * @param string $foo
                 */

            EOF;

        yield [
            <<<'EOF'
                <?php
                    /**
                     * @return bool
                     * @throws Exception
                     * @param string $foo
                     */

                EOF,
            $input,
            ['order' => ['return', 'throws', 'param']],
        ];

        yield [
            <<<'EOF'
                <?php
                    /**
                     * @throws Exception
                     * @return bool
                     * @param string $foo
                     */

                EOF,
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

        $expected = <<<'EOF'
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

            EOF;

        $input = <<<'EOF'
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

            EOF;

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
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
            ['order' => ['internal', 'template', 'param', 'custom', 'throws', 'return']],
        ];

        yield 'pare' => [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
            ['order' => ['param', 'return']],
        ];

        yield 'pareth' => [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
            ['order' => ['param', 'return', 'throws']],
        ];

        yield 'pathre' => [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
            ['order' => ['param', 'throws', 'return']],
        ];

        yield 'tepathre' => [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
            ['order' => ['template', 'param', 'throws', 'return']],
        ];

        yield 'tepathre2' => [
            <<<'EOF'
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

                EOF,
            <<<'EOF'
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

                EOF,
            ['order' => ['template', 'param', 'throws', 'return']],
        ];
    }
}
