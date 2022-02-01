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

        $this->doTest($expected, $input);
    }

    public function testWithPeriod(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello.
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithQuestionMark(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello?
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithExclamationMark(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello!
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithInvertedQuestionMark(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello¿
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithInvertedExclamationMark(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello¡
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithUnicodeQuestionMark(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * ハロー？
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithUnicodeExclamationMark(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * ハロー！
     */

EOF;
        $this->doTest($expected);
    }

    public function testWithJapanesePeriod(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * ハロー。
     */

EOF;
        $this->doTest($expected);
    }

    public function testFixIncBlank(): void
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

        $this->doTest($expected, $input);
    }

    public function testFixMultiline(): void
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

        $this->doTest($expected, $input);
    }

    public function testWithTags(): void
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

        $this->doTest($expected, $input);
    }

    public function testWithLongDescription(): void
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

        $this->doTest($expected, $input);
    }

    public function testCrazyMultiLineComments(): void
    {
        $expected = <<<'EOF'
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

EOF;

        $input = <<<'EOF'
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

EOF;

        $this->doTest($expected, $input);
    }

    public function testWithNoDescription(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return bool
     */

EOF;

        $this->doTest($expected);
    }

    /**
     * @dataProvider provideInheritDocCases
     */
    public function testWithInheritDoc(string $expected): void
    {
        $this->doTest($expected);
    }

    public function provideInheritDocCases(): array
    {
        return [
            [
                '<?php
    /**
     * {@inheritdoc}
     */
',
            ],
            [
                '<?php
    /**
     * @inheritDoc
     */
',
            ],
        ];
    }

    public function testEmptyDocBlock(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

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

    public function provideMessyWhitespacesCases(): array
    {
        return [
            [
                "<?php\r\n\t/**\r\n\t * Hello there.\r\n\t */",
                "<?php\r\n\t/**\r\n\t * Hello there\r\n\t */",
            ],
        ];
    }
}
