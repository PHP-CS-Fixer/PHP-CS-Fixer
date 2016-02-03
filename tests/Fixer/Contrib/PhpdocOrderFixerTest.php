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

namespace PhpCsFixer\Tests\Fixer\Contrib;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class PhpdocOrderFixerTest extends AbstractFixerTestCase
{
    public function testNoChanges()
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

    public function testOnlyParams()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @param string          $name
     */

EOF;
        $this->doTest($expected);
    }

    public function testOnlyReturns()
    {
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

    public function testEmpty()
    {
        $this->doTest('/***/');
    }

    public function testNoAnnotations()
    {
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

    public function testFixBasicCase()
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

    public function testFixCompeteCase()
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

    public function testExampleFromSymfony()
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
}
