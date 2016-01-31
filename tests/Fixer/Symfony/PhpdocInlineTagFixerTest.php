<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * Test PhpdocInlineTagFixer.
 *
 * @internal
 */
final class PhpdocInlineTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideTestFixInlineDocCases
     */
    public function testFixInlineDoc($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixInlineDocCases()
    {
        $cases = array(
            array(
                '<?php
    /**
     * {link} { LINK }
     * { test }
     * {@inheritdoc rire éclatant des écoliers qui décontenança®¶ñ¿}
     * test other comment
     * {@inheritdoc test} a
     * {@inheritdoc test} b
     * {@inheritdoc test} c
     * {@inheritdoc foo bar.} d
     * {@inheritdoc foo bar.} e
     * {@inheritdoc test} f
     * end comment {@inheritdoc here we are done} @spacepossum {1}
     */
',
                '<?php
    /**
     * {link} { LINK }
     * { test }
     * {@inheritDoc rire éclatant des écoliers qui décontenança®¶ñ¿ }
     * test other comment
     * @{inheritdoc test} a
     * {{@inheritdoc    test}} b
     * {@ inheritdoc   test} c
     * { @inheritdoc 	foo bar.  } d
     * {@ 	inheritdoc foo bar.	} e
     * @{{inheritdoc test}} f
     * end comment {@inheritdoc here we are done} @spacepossum {1}
     */
',
            ),
        );

        foreach (array('example', 'id', 'internal', 'inheritdoc', 'link', 'source', 'toc', 'tutorial') as $tag) {
            $cases[] = array(
                sprintf("<?php\n     /**\n      * {@%s}a\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * @{%s}a\n      */\n", $tag),
            );
            $cases[] = array(
                sprintf("<?php\n     /**\n      * {@%s} b\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * {{@%s}} b\n      */\n", $tag),
            );
            $cases[] = array(
                sprintf("<?php\n     /**\n      * c {@%s}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c @{{%s}}\n      */\n", $tag),
            );
            $cases[] = array(
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c @{{%s test}}\n      */\n", $tag),
            );
            // test unbalanced { tags
            $cases[] = array(
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c {@%s test}}\n      */\n", $tag),
            );
            $cases[] = array(
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c {{@%s test}\n      */\n", $tag),
            );
            $cases[] = array(
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c {@%s test}}\n      */\n", $tag),
            );
            $cases[] = array(
                sprintf("<?php\n     /**\n      * c {@%s test}\n      */\n", $tag),
                sprintf("<?php\n     /**\n      * c @{{%s test}}}\n      */\n", $tag),
            );
        }

        // don't touch custom tags
        $tag = 'foo';
        $cases[] = array(
            sprintf("<?php\n     /**\n      * @{%s}a\n      */\n", $tag),
        );
        $cases[] = array(
            sprintf("<?php\n     /**\n      * {{@%s}} b\n      */\n", $tag),
        );
        $cases[] = array(
            sprintf("<?php\n     /**\n      * c @{{%s}}\n      */\n", $tag),
        );

        // don't auto inline tags with the exception of inheritdoc
        foreach (array('example', 'id', 'internal', 'foo', 'link', 'source', 'toc', 'tutorial') as $tag) {
            $cases[] = array(
                sprintf("<?php\n     /**\n      * @%s\n      */\n", $tag),
            );
        }

        // don't touch well formatted tags
        foreach (array('example', 'id', 'internal', 'inheritdoc', 'link', 'source', 'toc', 'tutorial') as $tag) {
            $cases[] = array(
                sprintf("<?php\n     /**\n      * {@%s}\n      */\n", $tag),
            );
        }

        // common typos
        $cases[] = array(
            '<?php
    /**
     * Typo {@inheritdoc} {@example} {@id} {@source} {@tutorial} {links}
     * inheritdocs
     */
',
            '<?php
    /**
     * Typo {@inheritdocs} {@exampleS} { @ids} { @sources } {{{ @tutorials }} {links}
     * inheritdocs
     */
',

        );

        // invalid syntax
        $cases[] = array(
            '<?php
    /**
     * {@link http://www.ietf.org/rfc/rfc1035.text)
     */
    $someVar = "hello";',
        );

        return $cases;
    }

    /**
     * @dataProvider provideTestFixInheritDocCases
     */
    public function testFixInheritDoc($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixInheritDocCases()
    {
        return array(
            array(
                '<?php
    /**
     * {@inheritdoc} should this be inside the tag?
     * {@inheritdoc}
     * {@inheritdoc}
     * {@inheritdoc}
     * inheritdoc
     */
',
                // missing { } test for inheritdoc
                '<?php
    /**
     * @inheritdoc should this be inside the tag?
     * @inheritdoc
     * @inheritdocs
     * {@inheritdocs}
     * inheritdoc
     */
',
                ),
        );
    }
}
