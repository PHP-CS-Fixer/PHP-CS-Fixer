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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer
 */
final class PhpdocLineSpanFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    /**
     * @return array
     */
    public function provideFixCases()
    {
        return [
            'It does not change doc blocks if not needed' => [
                '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
            ],
            'It does change doc blocks to multi by default' => [
                '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
                '<?php

class Foo
{
    /** Important */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /** @return void */
    public function hello() {}
}
',
            ],
            'It does change doc blocks to single if configured to do so' => [
                '<?php

class Foo
{
    /** Important */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /** @return void */
    public function hello() {}
}
',
                '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
                [
                    'property' => 'single',
                    'const' => 'single',
                    'method' => 'single',
                ],
            ],
            'It does not changes doc blocks from single if configured to do so' => [
                '<?php

class Foo
{
    /** Important */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /** @return void */
    public function hello() {}
}
',
                null,
                [
                    'property' => 'single',
                    'const' => 'single',
                    'method' => 'single',
                ],
            ],
            'It can be configured to change certain elements to single line' => [
                '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /** @var bool */
    public $variable = true;

    /** @var bool */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
                '<?php

class Foo
{
    /**
     * Important
     */
    const FOO_BAR = "foobar";

    /**
     * @var bool
     */
    public $variable = true;

    /**
     * @var bool
     */
    private $var = false;


    /**
     * @return void
     */
    public function hello() {}
}
',
                [
                    'property' => 'single',
                ],
            ],
            'It wont change a doc block to single line if it has multiple useful lines' => [
                '<?php

class Foo
{
    /**
     * Important
     * Really important
     */
    const FOO_BAR = "foobar";
}
',
                null,
                [
                    'const' => 'single',
                ],
            ],
            'It updates doc blocks correctly, even with more indentation' => [
                '<?php

if (false) {
    class Foo
    {
        /** @var bool */
        public $var = true;

        /**
         * @return void
         */
        public function hello () {}
    }
}
',
                '<?php

if (false) {
    class Foo
    {
        /**
         * @var bool
         */
        public $var = true;

        /** @return void */
        public function hello () {}
    }
}
',
                [
                    'property' => 'single',
                ],
            ],
            'It can convert empty doc blocks' => [
                '<?php

class Foo
{
    /**
     *
     */
    const FOO = "foobar";

    /**  */
    private $foo;
}',
                '<?php

class Foo
{
    /**  */
    const FOO = "foobar";

    /**
     *
     */
    private $foo;
}',
                [
                    'property' => 'single',
                ],
            ],
            'It can update doc blocks of static properties' => [
                '<?php

class Bar
{
    /**
     * Important
     */
    public static $variable = "acme";
}
',
                '<?php

class Bar
{
    /** Important */
    public static $variable = "acme";
}
',
            ],
            'It can update doc blocks of properties that use the var keyword instead of public' => [
                '<?php

class Bar
{
    /**
     * Important
     */
    var $variable = "acme";
}
',
                '<?php

class Bar
{
    /** Important */
    var $variable = "acme";
}
',
            ],
            'It can update doc blocks of static that do not declare visibility' => [
                '<?php

class Bar
{
    /**
     * Important
     */
    static $variable = "acme";
}
',
                '<?php

class Bar
{
    /** Important */
    static $variable = "acme";
}
',
            ],
        ];
    }

    /**
     * @requires PHP 7.1
     * @dataProvider provideFix71Cases
     *
     * @param string $expected
     * @param string $input
     */
    public function testFix71($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFix71Cases()
    {
        return [
            'It can handle constants with visibility' => [
                '<?php

class Foo
{
    /**
     *
     */
    public const FOO = "foobar";

    /**  */
    private $foo;
}',
                '<?php

class Foo
{
    /**  */
    public const FOO = "foobar";

    /**
     *
     */
    private $foo;
}',
                [
                    'property' => 'single',
                ],
            ],
        ];
    }

    /**
     * @requires PHP 7.4
     * @dataProvider provideFixPhp74Cases
     *
     * @param string $expected
     * @param string $input
     */
    public function testFixPhp74($expected, $input = null, array $config = [])
    {
        $this->fixer->configure($config);
        $this->doTest($expected, $input);
    }

    public function provideFixPhp74Cases()
    {
        return [
            'It can handle properties with type declaration' => [
                '<?php

class Foo
{
    /**  */
    private ?string $foo;
}',
                '<?php

class Foo
{
    /**
     *
     */
    private ?string $foo;
}',
                [
                    'property' => 'single',
                ],
            ],
        ];
    }
}
