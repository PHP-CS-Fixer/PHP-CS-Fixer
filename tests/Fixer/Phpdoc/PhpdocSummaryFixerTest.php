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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer
 */
final class PhpdocSummaryFixerTest extends AbstractFixerTestCase
{
    public function testFixWithTrailingSpace(): void
    {
        $expected = '<?php
/**
 * Test.
 */';

        $input = '<?php
/**
 * Test         '.'
 */';
        $this->doTest($expected, $input);
    }

    public function testFix(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there.
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello there
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testWithPeriod(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello.
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithQuestionMark(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello?
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithExclamationMark(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello!
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithInvertedQuestionMark(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello¿
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithInvertedExclamationMark(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello¡
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithUnicodeQuestionMark(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * ハロー？
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithUnicodeExclamationMark(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * ハロー！
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testWithJapanesePeriod(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * ハロー。
                 */

            EOD;
        $this->doTest($expected);
    }

    public function testFixIncBlank(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hi.
                 *
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hi
                 *
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testFixMultiline(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello
                 * there.
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello
                 * there
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testWithList(): void
    {
        $expected = <<<'EOD'
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
            EOD;

        $this->doTest($expected);
    }

    public function testWithTags(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there.
                 *
                 * @param string $foo
                 *
                 * @return bool
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello there
                 *
                 * @param string $foo
                 *
                 * @return bool
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testWithLongDescription(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * Hello there.
                 *
                 * Long description
                 * goes here.
                 *
                 * @return bool
                 */

            EOD;

        $input = <<<'EOD'
            <?php
                /**
                 * Hello there
                 *
                 * Long description
                 * goes here.
                 *
                 * @return bool
                 */

            EOD;

        $this->doTest($expected, $input);
    }

    public function testCrazyMultiLineComments(): void
    {
        $expected = <<<'EOD'
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

            EOD;

        $input = <<<'EOD'
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

            EOD;

        $this->doTest($expected, $input);
    }

    public function testWithNoDescription(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 * @return bool
                 */

            EOD;

        $this->doTest($expected);
    }

    /**
     * @dataProvider provideWithInheritDocCases
     */
    public function testWithInheritDoc(string $expected): void
    {
        $this->doTest($expected);
    }

    public static function provideWithInheritDocCases(): iterable
    {
        yield [
            '<?php
    /**
     * {@inheritdoc}
     */
',
        ];

        yield [
            '<?php
    /**
     * @inheritDoc
     */
',
        ];
    }

    public function testEmptyDocBlock(): void
    {
        $expected = <<<'EOD'
            <?php
                /**
                 *
                 */

            EOD;

        $this->doTest($expected);
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideMessyWhitespacesCases(): iterable
    {
        yield [
            "<?php\r\n\t/**\r\n\t * Hello there.\r\n\t */",
            "<?php\r\n\t/**\r\n\t * Hello there\r\n\t */",
        ];
    }
}
