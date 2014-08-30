<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\All;

use Symfony\CS\Fixer\All\TrailingCommaInMultiLineArrayFixer as Fixer;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class TrailingCommaInMultiLineArrayFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input)
    {
        $fixer = new Fixer();
        $file = $this->getTestFile();

        $this->assertSame($expected, $fixer->fix($file, $input));
    }

    public function provideExamples()
    {
        return array(
            array('<?php $x = array();', '<?php $x = array();'),
            array('<?php $x = array(());', '<?php $x = array(());'),
            array('<?php $x = array("foo");', '<?php $x = array("foo");'),
            array('<?php $x = array("foo", );', '<?php $x = array("foo", );'),
            array("<?php \$x = array(\n'foo',\n);", "<?php \$x = array(\n'foo'\n);"),
            array("<?php \$x = array('foo',\n);", "<?php \$x = array('foo',\n);"),
            array("<?php \$x = array('foo',\n);", "<?php \$x = array('foo'\n);"),
            array("<?php \$x = array('foo', /* boo */\n);", "<?php \$x = array('foo' /* boo */\n);"),
            array("<?php \$x = array('foo',\n/* boo */\n);", "<?php \$x = array('foo'\n/* boo */\n);"),
            array("<?php \$x = array(\narray('foo',\n),\n);", "<?php \$x = array(\narray('foo'\n)\n);"),
            array("<?php \$x = array(\narray('foo'),\n);", "<?php \$x = array(\narray('foo')\n);"),

            array(
                "<?php

                \$x = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? true : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                );

                \$y = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? true : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                );",
                "<?php

                \$x = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? true : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    )
                                )
                            )
                        )
                    )
                );

                \$y = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? true : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    )
                                )
                            )
                        )
                    )
                );",
            ),

            // Short syntax
            array('<?php $x = array([]);', '<?php $x = array([]);'),
            array('<?php $x = [[]];', '<?php $x = [[]];'),
            array('<?php $x = ["foo",];', '<?php $x = ["foo",];'),
            array('<?php $x = bar(["foo",]);', '<?php $x = bar(["foo",]);'),
            array("<?php \$x = bar(['foo',\n]]);", "<?php \$x = bar(['foo'\n]]);"),
            array("<?php \$x = ['foo', \n];", "<?php \$x = ['foo', \n];"),
            array('<?php $x = array([],);', '<?php $x = array([],);'),
            array('<?php $x = [[],];', '<?php $x = [[],];'),
            array('<?php $x = [$y[],];', '<?php $x = [$y[],];'),

            array(
                "<?php

                \$x = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? [true] : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                );

                \$y = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? [true] : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    ),
                                ),
                            ),
                        ),
                    ),
                );",
                "<?php

                \$x = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? [true] : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    )
                                )
                            )
                        )
                    )
                );

                \$y = array(
                    'foo',
                    'bar',
                    array(
                        'foo',
                        'bar',
                        array(
                            'foo',
                            'bar',
                            array(
                                'foo',
                                ('bar' ? [true] : !(false)),
                                array(
                                    'foo',
                                    'bar',
                                    array(
                                        'foo',
                                        ('bar'),
                                    )
                                )
                            )
                        )
                    )
                );",
            ),
        );
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
