<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocOrderFixerTest extends AbstractFixerTestBase
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
        $this->makeTest($expected);
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
        $this->makeTest($expected);
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
        $this->makeTest($expected);
    }

    public function testEmpty()
    {
        $this->makeTest('/***/');
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
        $this->makeTest($expected);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
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

        $this->makeTest($expected, $input);
    }
}
