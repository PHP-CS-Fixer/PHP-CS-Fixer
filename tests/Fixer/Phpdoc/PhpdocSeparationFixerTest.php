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

use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Jakub Kwaśniewski <jakub@zero-85.pl>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer
 */
final class PhpdocSeparationFixerTest extends AbstractFixerTestCase
{
    public function testFix(): void
    {
        $this->doTest('<?php
/** @param EngineInterface $templating
*@return void
*/');

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

    public function testFixMoreTags(): void
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

    public function testFixSpreadOut(): void
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

    public function testMultiLineComments(): void
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

        $this->doTest($expected);
    }

    public function testDoctrineExample(): void
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

    public function testSymfonyExample(): void
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
     * @see https://php.net/session.configuration for options
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

    public function testDeprecatedAndSeeTags(): void
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

    public function testPropertyTags(): void
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

    public function testClassDocBlock(): void
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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
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

    public function testPoorAlignment(): void
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
    *@author Graham Campbell <hello@gjcampbell.co.uk>
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
    *@author Graham Campbell <hello@gjcampbell.co.uk>
 */
class Bar {}

EOF;

        $this->doTest($expected, $input);
    }

    public function testMoveUnknownAnnotations(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @expectedException Exception
     *
     * @expectedExceptionMessage Oh Noes!
     * Something when wrong!
     *
     * @Hello\Test\Foo(asd)
     *
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

    /**
     * @dataProvider provideInheritDocCases
     */
    public function testInheritDoc(string $expected, string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideInheritDocCases(): array
    {
        return [
            [
                '<?php
    /**
     * {@inheritdoc}
     *
     * @param string $expected
     * @param string $input
     */
',
                '<?php
    /**
     * {@inheritdoc}
     * @param string $expected
     * @param string $input
     */
',
            ],
            [
                '<?php
    /**
     * {@inheritDoc}
     *
     * @param string $expected
     * @param string $input
     */
',
                '<?php
    /**
     * {@inheritDoc}
     * @param string $expected
     * @param string $input
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

    public function testLargerEmptyDocBlock(): void
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

    public function testOneLineDocBlock(): void
    {
        $expected = <<<'EOF'
<?php
    /** Foo */
    const Foo = 1;

EOF;

        $this->doTest($expected);
    }

    public function testMessyWhitespaces(): void
    {
        $expected = "<?php\t/**\r\n\t * @param string \$text\r\n\t *\r\n\t * @return string\r\n\t */";
        $input = "<?php\t/**\r\n\t * @param string \$text\r\n\t * @return string\r\n\t */";

        $this->doTest($expected, $input);
    }

    public function testWithSpacing(): void
    {
        $expected = '<?php
    /**
     * Foo
     *
     * @bar 123
     *
     * {@inheritdoc}       '.'
     *
     *   @param string $expected
     * @param string $input
     */';

        $input = '<?php
    /**
     * Foo
     * @bar 123
     *
     * {@inheritdoc}       '.'
     *   @param string $expected
     * @param string $input
     */';

        $this->doTest($expected, $input);
    }

    public function testTagInTwoGroupsConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage(
            'The option "groups" value is invalid. '.
            'The "param" tag belongs to more than one group.'
        );

        $this->fixer->configure(['groups' => [['param', 'return'], ['param', 'throws']]]);
    }

    public function testTagSpecifiedTwoTimesInGroupConfiguration(): void
    {
        $this->expectException(InvalidFixerConfigurationException::class);
        $this->expectExceptionMessage(
            'The option "groups" value is invalid. '.
            'The "param" tag is specified more than once.'
        );

        $this->fixer->configure(['groups' => [['param', 'return', 'param', 'throws']]]);
    }

    public function testLaravelGroups(): void
    {
        $this->fixer->configure(['groups' => [
            ['param', 'return'],
            ['throws'],
            ['deprecated', 'link', 'see', 'since'],
            ['author', 'copyright', 'license'],
            ['category', 'package', 'subpackage'],
            ['property', 'property-read', 'property-write'],
        ]]);

        $expected = <<<'EOF'
<?php
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param  string  $field
     * @param  array  $extraConditions
     * @return \Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @param  string  $field
     * @param  array  $extraConditions
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testVariousGroups(): void
    {
        $this->fixer->configure([
            'groups' => [
                ['deprecated', 'link', 'see', 'since', 'author', 'copyright', 'license'],
                ['category', 'package', 'subpackage'],
                ['property', 'property-read', 'property-write'],
                ['return', 'param'],
            ],
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @link https://example.com/link
     * @see https://doc.example.com/link
     * @copyright by John Doe 2001
     * @author John Doe
     *
     * @property-custom string $prop
     *
     * @param  string  $field
     * @param  array  $extraConditions
     * @return \Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @link https://example.com/link
     *
     *
     * @see https://doc.example.com/link
     * @copyright by John Doe 2001
     * @author John Doe
     * @property-custom string $prop
     * @param  string  $field
     * @param  array  $extraConditions
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testVariousAdditionalGroups(): void
    {
        $this->fixer->configure([
            'groups' => [
                ['deprecated', 'link', 'see', 'since', 'author', 'copyright', 'license'],
                ['category', 'package', 'subpackage'],
                ['property', 'property-read', 'property-write'],
                ['return', 'param'],
            ],
        ]);

        $expected = <<<'EOF'
<?php
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @link https://example.com/link
     * @see https://doc.example.com/link
     * @copyright by John Doe 2001
     * @author John Doe
     *
     * @param  string  $field
     * @param  array  $extraConditions
     * @return \Symfony\Component\HttpFoundation\Response|null
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Attempt to authenticate using HTTP Basic Auth.
     *
     * @link https://example.com/link
     *
     *
     * @see https://doc.example.com/link
     * @copyright by John Doe 2001
     * @author John Doe
     * @param  string  $field
     * @param  array  $extraConditions
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */

EOF;

        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideDocCodeCases
     *
     * @param array<string, mixed> $config
     */
    public function testDocCode(string $expected, ?string $input = null, ?array $config = null): void
    {
        if (null !== $config) {
            $this->fixer->configure($config);
        }

        $this->doTest($expected, $input);
    }

    /**
     * @return array<array<null|array<string, mixed>|string>>
     */
    public static function provideDocCodeCases(): iterable
    {
        $input = <<<'EOF'
<?php
/**
 * Hello there!
 *
 * @author John Doe
 * @custom Test!
 * @throws Exception|RuntimeException foo
 * @param string $foo
 * @param bool   $bar Bar
 *
 * @return int  Return the number of changes.
 */

EOF;

        yield 'laravel' => [
            <<<'EOF'
<?php
/**
 * Hello there!
 *
 * @author John Doe
 *
 * @custom Test!
 *
 * @throws Exception|RuntimeException foo
 *
 * @param string $foo
 * @param bool   $bar Bar
 * @return int  Return the number of changes.
 */

EOF,
            $input,
            ['groups' => [
                ['param', 'return'],
                ['throws'],
                ['deprecated', 'link', 'see', 'since'],
                ['author', 'copyright', 'license'],
                ['category', 'package', 'subpackage'],
                ['property', 'property-read', 'property-write'],
            ]],
        ];

        yield 'all_tags' => [
            <<<'EOF'
<?php
/**
 * Hello there!
 *
 * @author John Doe
 * @custom Test!
 * @throws Exception|RuntimeException foo
 *
 * @param string $foo
 * @param bool   $bar Bar
 * @return int  Return the number of changes.
 */

EOF,
            $input,
            ['groups' => [['author', 'throws', 'custom'], ['return', 'param']]],
        ];

        yield 'default_groups_standard_tags' => [
            <<<'EOF'
<?php
/**
 * Hello there!
 *
 * @author John Doe
 *
 * @throws Exception|RuntimeException foo
 *
 * @custom Test!
 *
 * @param string $foo
 * @param bool   $bar Bar
 *
 * @return int  Return the number of changes.
 */

EOF,
            <<<'EOF'
<?php
/**
 * Hello there!
 * @author John Doe
 * @throws Exception|RuntimeException foo
 * @custom Test!
 * @param string $foo
 * @param bool   $bar Bar
 * @return int  Return the number of changes.
 */

EOF,
        ];

        yield 'default_groups_all_tags' => [
            <<<'EOF'
<?php
/**
 * Hello there!
 *
 * @author John Doe
 *
 * @throws Exception|RuntimeException foo
 *
 * @custom Test!
 *
 * @param string $foo
 * @param bool   $bar Bar
 *
 * @return int  Return the number of changes.
 */

EOF,
            <<<'EOF'
<?php
/**
 * Hello there!
 * @author John Doe
 * @throws Exception|RuntimeException foo
 * @custom Test!
 * @param string $foo
 * @param bool   $bar Bar
 * @return int  Return the number of changes.
 */

EOF,
        ];

        yield 'Separated unlisted tags with default config' => [
            <<<'EOF'
<?php
/**
 * @not-in-any-group1
 *
 * @not-in-any-group2
 *
 * @not-in-any-group3
 */

EOF,
            <<<'EOF'
<?php
/**
 * @not-in-any-group1
 * @not-in-any-group2
 * @not-in-any-group3
 */

EOF,
        ];

        yield 'Skip unlisted tags' => [
            <<<'EOF'
<?php
/**
 * @in-group-1
 * @in-group-1-too
 *
 * @not-in-any-group1
 *
 * @not-in-any-group2
 * @not-in-any-group3
 */

EOF,
            <<<'EOF'
<?php
/**
 * @in-group-1
 *
 * @in-group-1-too
 * @not-in-any-group1
 *
 * @not-in-any-group2
 * @not-in-any-group3
 */

EOF,
            [
                'groups' => [['in-group-1', 'in-group-1-too']],
                'skip_unlisted_annotations' => true,
            ],
        ];

        yield 'Doctrine annotations' => [
            <<<'EOF'
<?php
/**
 * @ORM\Id
 * @ORM\Column(type="integer")
 * @ORM\GeneratedValue
 */

EOF,
            <<<'EOF'
<?php
/**
 * @ORM\Id
 *
 * @ORM\Column(type="integer")
 *
 * @ORM\GeneratedValue
 */

EOF,
            ['groups' => [
                ['ORM\Id', 'ORM\Column', 'ORM\GeneratedValue'],
            ]],
        ];

        yield 'With wildcard' => [
            <<<'EOF'
<?php
/**
 * @ORM\Id
 * @ORM\Column(type="integer")
 * @ORM\GeneratedValue
 *
 * @Assert\NotNull
 * @Assert\Type("string")
 */

EOF,
            <<<'EOF'
<?php
/**
 * @ORM\Id
 *
 * @ORM\Column(type="integer")
 *
 * @ORM\GeneratedValue
 * @Assert\NotNull
 *
 * @Assert\Type("string")
 */

EOF,
            ['groups' => [
                ['ORM\*'],
                ['Assert\*'],
            ]],
        ];
    }
}
