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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocShortDescriptionFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                    /**
                     * Hello there.
                     */
                ',
                '<?php
                    /**
                     * Hello there
                     */
                ',
            ),
            array(
                '<?php
                    /**
                     * Hello there 2.
                     */
                ',
                '<?php
                    /**
                     *	Hello there 2
                     */
                ',
            ),
            array(
                '<?php
                    /**
                     * Hello there 3.
                     */
                ',
                '<?php
                    /**
                     *     Hello there 3
                     */
                ',
            ),
            array(
                '<?php
                    /**
                     * {@inheritdoc}
                     */
                ',
                '<?php
                    /**
                     *{@inheritdoc}
                     */
                ',
            ),
        );
    }

    public function testWithPeriod()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello.
     */

EOF;
        $this->makeTest($expected);
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

    public function testWithExclamationMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello!
     */

EOF;
        $this->makeTest($expected);
    }

    public function testWithInvertedQuestionMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello¿
     */

EOF;
        $this->makeTest($expected);
    }

    public function testWithInvertedExclamationMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello¡
     */

EOF;
        $this->makeTest($expected);
    }

    public function testWithUnicodeQuestionMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * ハロー？
     */

EOF;
        $this->makeTest($expected);
    }

    public function testWithUnicodeExclamationMark()
    {
        $expected = <<<'EOF'
<?php
    /**
     * ハロー！
     */

EOF;
        $this->makeTest($expected);
    }

    public function testWithJapanesePeriod()
    {
        $expected = <<<'EOF'
<?php
    /**
     * ハロー。
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
     *      Hi
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
     *    multi line here.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello
     *    multi line here
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

class A
{
    /**
     * {@inheritdoc}
     */
    public function A()
    {

    }

    /**
     * @inheritdoc
     */
    public function C()
    {
        // see example code @ https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md#61-making-inheritance-explicit-using-the-inheritdoc-tag
    }
}
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

    /**
    */

    /***/

    /**

        Hello
     */
EOF;

        $this->makeTest($expected);
    }
}
