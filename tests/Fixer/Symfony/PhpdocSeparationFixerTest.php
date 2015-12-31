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

use Symfony\CS\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @internal
 */
final class PhpdocSeparationFixerTest extends AbstractFixerTestCase
{
    public function testFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     *
     * @return void
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param EngineInterface $templating
     * @return void
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMoreTags()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there!
     *
     * @internal
     *
     * @param string $foo
     *
     * @throws Exception
     *
     * @return bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there!
     * @internal
     * @param string $foo
     * @throws Exception
     *
     *
     *
     * @return bool
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixSpreadOut()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there!
     *
     * Long description
     * goes here.
     *
     * @param string $foo
     * @param bool   $bar Bar
     *
     * @throws Exception|RuntimeException
     *
     * @return bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there!
     *
     * Long description
     * goes here.
     * @param string $foo
     *
     *
     * @param bool   $bar Bar
     *
     *
     *
     * @throws Exception|RuntimeException
     *
     *
     *
     *
     * @return bool
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testMultiLineComments()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there!
     *
     * Long description
     * goes here.
     *
     * @param string $foo test 123
     *                    asdasdasd
     * @param bool  $bar qwerty
     *
     * @throws Exception|RuntimeException
     *
     * @return bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there!
     *
     * Long description
     * goes here.
     * @param string $foo test 123
     *                    asdasdasd
     * @param bool  $bar qwerty
     * @throws Exception|RuntimeException
     * @return bool
     */

EOF;

        $this->doTest($expected, $input);
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

        $this->doTest($expected);
    }

    public function testDoctrineExample()
    {
        $expected = <<<'EOF'
<?php
/**
 * PersistentObject base class that implements getter/setter methods for all mapped fields and associations
 * by overriding __call.
 *
 * This class is a forward compatible implementation of the PersistentObject trait.
 *
 * Limitations:
 *
 * 1. All persistent objects have to be associated with a single ObjectManager, multiple
 *    ObjectManagers are not supported. You can set the ObjectManager with `PersistentObject#setObjectManager()`.
 * 2. Setters and getters only work if a ClassMetadata instance was injected into the PersistentObject.
 *    This is either done on `postLoad` of an object or by accessing the global object manager.
 * 3. There are no hooks for setters/getters. Just implement the method yourself instead of relying on __call().
 * 4. Slower than handcoded implementations: An average of 7 method calls per access to a field and 11 for an association.
 * 5. Only the inverse side associations get autoset on the owning side as well. Setting objects on the owning side
 *    will not set the inverse side associations.
 *
 * @example
 *
 *  PersistentObject::setObjectManager($em);
 *
 *  class Foo extends PersistentObject
 *  {
 *      private $id;
 *  }
 *
 *  $foo = new Foo();
 *  $foo->getId(); // method exists through __call
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 */

EOF;

        $this->doTest($expected);
    }

    public function testSymfonyExample()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Constructor.
     *
     * Depending on how you want the storage driver to behave you probably
     * want to override this constructor entirely.
     *
     * List of options for $options array with their defaults.
     *
     * @see http://php.net/session.configuration for options
     *
     * but we omit 'session.' from the beginning of the keys for convenience.
     *
     * ("auto_start", is not supported as it tells PHP to start a session before
     * PHP starts to execute user-land code. Setting during runtime has no effect).
     *
     * cache_limiter, "nocache" (use "0" to prevent headers from being sent entirely).
     * cookie_domain, ""
     * cookie_httponly, ""
     * cookie_lifetime, "0"
     * cookie_path, "/"
     * cookie_secure, ""
     * entropy_file, ""
     * entropy_length, "0"
     * gc_divisor, "100"
     * gc_maxlifetime, "1440"
     * gc_probability, "1"
     * hash_bits_per_character, "4"
     * hash_function, "0"
     * name, "PHPSESSID"
     * referer_check, ""
     * serialize_handler, "php"
     * use_cookies, "1"
     * use_only_cookies, "1"
     * use_trans_sid, "0"
     * upload_progress.enabled, "1"
     * upload_progress.cleanup, "1"
     * upload_progress.prefix, "upload_progress_"
     * upload_progress.name, "PHP_SESSION_UPLOAD_PROGRESS"
     * upload_progress.freq, "1%"
     * upload_progress.min-freq, "1"
     * url_rewriter.tags, "a=href,area=href,frame=src,form=,fieldset="
     *
     * @param array                                                            $options Session configuration options.
     * @param AbstractProxy|NativeSessionHandler|\SessionHandlerInterface|null $handler
     * @param MetadataBag                                                      $metaBag MetadataBag.
     */

EOF;

        $this->doTest($expected);
    }

    public function testDeprecatedAndSeeTags()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hi!
     *
     * @author Bar Baz <foo@example.com>
     *
     * @deprecated As of some version.
     * @see Replacement
     *      described here.
     *
     * @param string $foo test 123
     * @param bool  $bar qwerty
     *
     * @return void
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hi!
     *
     * @author Bar Baz <foo@example.com>
     * @deprecated As of some version.
     *
     * @see Replacement
     *      described here.
     * @param string $foo test 123
     * @param bool  $bar qwerty
     *
     * @return void
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testPropertyTags()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @author Bar Baz <foo@example.com>
     *
     * @property int $foo
     * @property-read int $foo
     * @property-write int $bar
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @author Bar Baz <foo@example.com>
     * @property int $foo
     *
     * @property-read int $foo
     *
     * @property-write int $bar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testClassDocBlock()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

/**
 * This is a class that does classy things.
 *
 * @internal
 *
 * @package Foo
 * @subpackage Foo\Bar
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Graham Campbell <graham@mineuk.com>
 * @copyright Foo Bar
 * @license MIT
 */
class Bar {}

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

/**
 * This is a class that does classy things.
 * @internal
 * @package Foo
 *
 *
 * @subpackage Foo\Bar
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @author Graham Campbell <graham@mineuk.com>
 *
 * @copyright Foo Bar
 *
 *
 * @license MIT
 */
class Bar {}

EOF;

        $this->doTest($expected, $input);
    }

    public function testPoorAlignment()
    {
        $expected = <<<'EOF'
<?php

namespace Foo;

/**
*      This is a class that does classy things.
    *
*    @internal
*
 *          @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
    *@author Graham Campbell <graham@mineuk.com>
 */
class Bar {}

EOF;

        $input = <<<'EOF'
<?php

namespace Foo;

/**
*      This is a class that does classy things.
    *
*    @internal
   *
*
*
 *          @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
     *
                             *
    *@author Graham Campbell <graham@mineuk.com>
 */
class Bar {}

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNotMoveUnknownAnnotations()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Oh Noes!
     * Something when wrong!
     *
     *
     * @Hello\Test\Foo(asd)
     * @Method("GET")
     *
     * @param string $expected
     * @param string $input
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Oh Noes!
     * Something when wrong!
     *
     *
     * @Hello\Test\Foo(asd)
     * @Method("GET")
     *
     * @param string $expected
     *
     * @param string $input
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testInheritDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * {@inheritdoc}
     *
     * @param string $expected
     * @param string $input
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * {@inheritdoc}
     * @param string $expected
     * @param string $input
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->doTest($expected);
    }

    public function testLargerEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     *
     *
     *
     */

EOF;

        $this->doTest($expected);
    }
}
