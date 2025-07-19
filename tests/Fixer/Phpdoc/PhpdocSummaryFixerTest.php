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
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer>
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
final class PhpdocSummaryFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, bool $useTabsAndWindowsNewlines = false): void
    {
        if ($useTabsAndWindowsNewlines) {
            $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));
        }
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: string, 2?: true}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'with trailing space' => [
            '<?php
/**
 * Test.
 */',
            '<?php
/**
 * Test         '.'
 */',
        ];

        yield 'with period' => [
            <<<'EOF'
                <?php
                    /**
                     * Hello there.
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Hello there
                     */

                EOF,
        ];

        yield 'with question mark' => [<<<'EOF'
            <?php
                /**
                 * Hello?
                 */

            EOF];

        yield 'with exclamation mark' => [<<<'EOF'
            <?php
                /**
                 * Hello!
                 */

            EOF];

        yield 'with inverted question mark' => [<<<'EOF'
            <?php
                /**
                 * Hello¿
                 */

            EOF];

        yield 'with inverted exclamation mark' => [<<<'EOF'
            <?php
                /**
                 * Hello¡
                 */

            EOF];

        yield 'with unicode question mark' => [<<<'EOF'
            <?php
                /**
                 * ハロー？
                 */

            EOF];

        yield 'with unicode exclamation mark' => [<<<'EOF'
            <?php
                /**
                 * ハロー！
                 */

            EOF];

        yield 'with Japanese period' => [<<<'EOF'
            <?php
                /**
                 * ハロー。
                 */

            EOF];

        yield 'with inc blank' => [
            <<<'EOF'
                <?php
                    /**
                     * Hi.
                     *
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Hi
                     *
                     */

                EOF,
        ];

        yield 'multiline' => [<<<'EOF'
            <?php
                /**
                 * Hello
                 * there.
                 */

            EOF,
            <<<'EOF'
                <?php
                    /**
                     * Hello
                     * there
                     */

                EOF,
        ];

        yield 'with list' => [<<<'EOF'
            <?php
                /**
                 * Options:
                 *  * a: aaa
                 *  * b: bbb
                 *  * c: ccc
                 */

                /**
                 * Options:
                 *
                 *  * a: aaa
                 *  * b: bbb
                 *  * c: ccc
                 */
            EOF];

        yield 'with tags' => [
            <<<'EOF'
                <?php
                    /**
                     * Hello there.
                     *
                     * @param string $foo
                     *
                     * @return bool
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Hello there
                     *
                     * @param string $foo
                     *
                     * @return bool
                     */

                EOF,
        ];

        yield 'with long description' => [
            <<<'EOF'
                <?php
                    /**
                     * Hello there.
                     *
                     * Long description
                     * goes here.
                     *
                     * @return bool
                     */

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Hello there
                     *
                     * Long description
                     * goes here.
                     *
                     * @return bool
                     */

                EOF,
        ];

        yield 'crazy multiline comments' => [
            <<<'EOF'
                <?php
                    /**
                     * Clients accept an array of constructor parameters.
                     *
                     * Here's an example of creating a client using a URI template for the
                     * client's base_url and an array of default request options to apply
                     * to each request:
                     *
                     *     $client = new Client([
                     *         'base_url' => [
                     *              'https://www.foo.com/{version}/',
                     *              ['version' => '123']
                     *          ],
                     *         'defaults' => [
                     *             'timeout'         => 10,
                     *             'allow_redirects' => false,
                     *             'proxy'           => '192.168.16.1:10'
                     *         ]
                     *     ]);
                     *
                     * @param _AutogeneratedInputConfiguration $config Client configuration settings
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

                EOF,
            <<<'EOF'
                <?php
                    /**
                     * Clients accept an array of constructor parameters
                     *
                     * Here's an example of creating a client using a URI template for the
                     * client's base_url and an array of default request options to apply
                     * to each request:
                     *
                     *     $client = new Client([
                     *         'base_url' => [
                     *              'https://www.foo.com/{version}/',
                     *              ['version' => '123']
                     *          ],
                     *         'defaults' => [
                     *             'timeout'         => 10,
                     *             'allow_redirects' => false,
                     *             'proxy'           => '192.168.16.1:10'
                     *         ]
                     *     ]);
                     *
                     * @param _AutogeneratedInputConfiguration $config Client configuration settings
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

                EOF,
        ];

        yield 'with no description' => [<<<'EOF'
            <?php
                /**
                 * @return bool
                 */

            EOF];

        yield 'inheritdoc in braces' => [
            '<?php
    /**
     * {@inheritdoc}
     */
',
        ];

        yield 'inheritdoc' => [
            '<?php
    /**
     * @inheritDoc
     */
',
        ];

        yield 'empty doc block' => [<<<'EOF'
            <?php
                /**
                 *
                 */

            EOF];

        yield 'tabs and windows line endings' => [
            "<?php\r\n\t/**\r\n\t * Hello there.\r\n\t */",
            "<?php\r\n\t/**\r\n\t * Hello there\r\n\t */",
            true,
        ];
    }
}
