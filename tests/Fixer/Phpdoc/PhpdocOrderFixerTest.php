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

use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
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
     * @dataProvider provideAllAvailableOrderStyleCases
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
     * @dataProvider provideAllAvailableOrderStyleCases
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
     * @dataProvider provideAllAvailableOrderStyleCases
     */
    public function testEmpty(array $config): void
    {
        $this->fixer->configure($config);
        $this->doTest('/***/');
    }

    /**
     * @dataProvider provideAllAvailableOrderStyleCases
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

    public function testFixCompeteCase(): void
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

    public function testNoChangesithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => PhpdocOrderFixer::ORDER_LARAVEL]);

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
        $this->fixer->configure(['order' => PhpdocOrderFixer::ORDER_LARAVEL]);

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

    public function testFixCompeteCaseWithLaravelStyle(): void
    {
        $this->fixer->configure(['order' => PhpdocOrderFixer::ORDER_LARAVEL]);

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
        $this->fixer->configure(['order' => PhpdocOrderFixer::ORDER_LARAVEL]);

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

    public function provideAllAvailableOrderStyleCases(): array
    {
        return [
            [['order' => PhpdocOrderFixer::ORDER_DEFAULT]],
            [['order' => PhpdocOrderFixer::ORDER_DEFAULT]],
        ];
    }

    /**
     * @dataProvider provideBasicCodeWithDifferentOrdersCases
     *
     * @param mixed $config
     * @param mixed $expected
     * @param mixed $input
     */
    public function testFixBasicCaseWithDifferentOrders($config, $expected, $input): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideBasicCodeWithDifferentOrdersCases(): array
    {
        $input = <<<'EOF'
<?php
    /**
     * @throws Exception
     * @return bool
     * @param string $foo
     */

EOF;

        return [
            [
                ['order' => ['return', 'throws', 'param']],
                <<<'EOF'
<?php
    /**
     * @return bool
     * @throws Exception
     * @param string $foo
     */

EOF,
                $input,
            ],

            [
                ['order' => ['throws', 'return', 'param']],
                <<<'EOF'
<?php
    /**
     * @throws Exception
     * @return bool
     * @param string $foo
     */

EOF,
                null,
            ],
        ];
    }

    public function testFixCompeteCaseWithCustomOrder(): void
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
     * @dataProvider provideCompeteCasesWithCustomOrdersCases
     */
    public function testFixCompeteCasesWithCustomOrders(array $config, string $expected, string $input): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    public function provideCompeteCasesWithCustomOrdersCases(): array
    {
        $docBlockBricks = [
            'title' => "Hello there\n",
            'description' => "Long description\ngoes here.\n",
            '@internal' => '',
            '@throws' => "Exception|RuntimeException dfsdf\njkaskdnaksdnkasndansdnansdajsdnkasd",
            '@custom' => "Test!\nasldnaksdkjasdasd",
            '@return' => [
                'bool Return false on failure',
                'int  Return the number of changes.',
            ],
            '@param' => [
                'string $foo',
                'bool   $bar Bar',
                'class-string<T> $id',
            ],
            '@template' => 'T of Extension\Extension',
        ];

        return [
            [
                ['order' => ['internal', 'template', 'param', 'custom', 'throws', 'return']],
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'template', 'param', 'custom', 'throws', 'return']
                ),
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'param', 'custom', 'template', 'return', 'throws']
                ),
            ],
            [
                ['order' => ['param', 'return']],
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'param', 'return', 'custom', 'template', 'throws']
                ),
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'return', 'custom', 'template', 'param', 'throws']
                ),
            ],
            [
                ['order' => ['param', 'return', 'throws']],
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'custom', 'template', 'param', 'return', 'throws']
                ),
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'return', 'custom', 'template', 'param', 'throws']
                ),
            ],
            [
                ['order' => ['param', 'throws', 'return']],
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'custom', 'template', 'param', 'throws', 'return']
                ),
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'return', 'custom', 'template', 'param', 'throws']
                ),
            ],
            [
                ['order' => ['template', 'param', 'throws', 'return']],
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'template', 'param', 'throws', 'return', 'custom']
                ),
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'return', 'param', 'template', 'custom', 'throws']
                ),
            ],
            [
                ['order' => ['template', 'param', 'throws', 'return']],
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'template', 'param', 'throws', 'return', 'custom']
                ),
                self::glueBricks(
                    $docBlockBricks,
                    ['title', 'description', 'internal', 'param', 'return', 'template', 'custom', 'throws']
                ),
            ],
        ];
    }

    private static function glueBricks(array $bricks, array $order): string
    {
        $indent = '    ';
        $commentIndent = $indent.' *';
        $out = '';
        foreach ($order as $tag) {
            // not an annotation brick
            if (isset($bricks[$tag])) {
                $out .= "{$commentIndent} ".str_replace("\n", "\n{$commentIndent} ", $bricks[$tag])."\n";
            }
            // it's an annotation
            elseif (isset($bricks["@{$tag}"])) {
                $annotation = "@{$tag}";
                $brick = (array) $bricks[$annotation];
                foreach ($brick as $line) {
                    $out .= "{$commentIndent} {$annotation} ".str_replace("\n", "\n{$commentIndent}  ".str_repeat(' ', \strlen($annotation)), $line)."\n";
                }
            }
        }

        return "<?php\n{$indent}/**\n{$out}{$commentIndent}*/\n\n";
    }
}
