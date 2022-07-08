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
        $this->fixer->configure(['style' => PhpdocOrderFixer::ORDER_STYLE_SYMFONY]);

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

    public function testFixBasicCaseWithSymfonyStyle(): void
    {
        $this->fixer->configure(['style' => PhpdocOrderFixer::ORDER_STYLE_SYMFONY]);

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

    public function testFixCompeteCaseWithSymfonyStyle(): void
    {
        $this->fixer->configure(['style' => PhpdocOrderFixer::ORDER_STYLE_SYMFONY]);

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

    public function testExampleFromSymfonyWithSymfonyStyle(): void
    {
        $this->fixer->configure(['style' => PhpdocOrderFixer::ORDER_STYLE_SYMFONY]);

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
            [['style' => PhpdocOrderFixer::ORDER_STYLE_PHPCS]],
            [['style' => PhpdocOrderFixer::ORDER_STYLE_SYMFONY]],
        ];
    }
}
