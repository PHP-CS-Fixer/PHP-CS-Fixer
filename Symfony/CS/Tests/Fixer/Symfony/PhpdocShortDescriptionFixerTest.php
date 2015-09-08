<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class PhpdocShortDescriptionFixerTest extends AbstractFixerTestBase
{
    public function testFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testWithQuestionMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello?
     */

EOF;
        $this->makeTest($expected);
    }

    public function testWithExclimationMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello¡
     */

EOF;
        $this->makeTest($expected);
    }

    public function testFixIncBlank()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hi.
     *
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hi
     *
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixMultiline()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello
     * there.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello
     * there
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testWithTags()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there.
     *
     * @param string $foo
     *
     * @return bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there
     *
     * @param string $foo
     *
     * @return bool
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testWithLongDescription()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there.
     *
     * Long description
     * goes here.
     *
     * @return bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there
     *
     * Long description
     * goes here.
     *
     * @return bool
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testCrazyMultiLineComments()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Clients accept an array of constructor parameters.
     *
     * Here's an example of creating a client using an URI template for the
     * client's base_url and an array of default request options to apply
     * to each request:
     *
     *     $client = new Client([
     *         'base_url' => [
     *              'http://www.foo.com/{version}/',
     *              ['version' => '123']
     *          ],
     *         'defaults' => [
     *             'timeout'         => 10,
     *             'allow_redirects' => false,
     *             'proxy'           => '192.168.16.1:10'
     *         ]
     *     ]);
     *
     * @param array $config Client configuration settings
     *     - base_url: Base URL of the client that is merged into relative URLs.
     *       Can be a string or an array that contains a URI template followed
     *       by an associative array of expansion variables to inject into the
     *       URI template.
     *     - handler: callable RingPHP handler used to transfer requests
     *     - message_factory: Factory used to create request and response object
     *     - defaults: Default request options to apply to each request
     *     - emitter: Event emitter used for request events
     *     - fsm: (internal use only) The request finite state machine. A
     *       function that accepts a transaction and optional final state. The
     *       function is responsible for transitioning a request through its
     *       lifecycle events.
     * @param string $foo
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Clients accept an array of constructor parameters
     *
     * Here's an example of creating a client using an URI template for the
     * client's base_url and an array of default request options to apply
     * to each request:
     *
     *     $client = new Client([
     *         'base_url' => [
     *              'http://www.foo.com/{version}/',
     *              ['version' => '123']
     *          ],
     *         'defaults' => [
     *             'timeout'         => 10,
     *             'allow_redirects' => false,
     *             'proxy'           => '192.168.16.1:10'
     *         ]
     *     ]);
     *
     * @param array $config Client configuration settings
     *     - base_url: Base URL of the client that is merged into relative URLs.
     *       Can be a string or an array that contains a URI template followed
     *       by an associative array of expansion variables to inject into the
     *       URI template.
     *     - handler: callable RingPHP handler used to transfer requests
     *     - message_factory: Factory used to create request and response object
     *     - defaults: Default request options to apply to each request
     *     - emitter: Event emitter used for request events
     *     - fsm: (internal use only) The request finite state machine. A
     *       function that accepts a transaction and optional final state. The
     *       function is responsible for transitioning a request through its
     *       lifecycle events.
     * @param string $foo
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testWithNoDescription()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return bool
     */

EOF;

        $this->makeTest($expected);
    }

    public function testWithInheritDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * {@inheritdoc}
     */

EOF;

        $this->makeTest($expected);
    }

    public function testEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->makeTest($expected);
    }
}
