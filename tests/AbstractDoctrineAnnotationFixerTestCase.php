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

namespace PhpCsFixer\Tests;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * Base class for Doctrine annotation fixer tests.
 *
 * @internal
 */
abstract class AbstractDoctrineAnnotationFixerTestCase extends AbstractFixerTestCase
{
    /**
     * @param array $configuration
     *
     * @dataProvider getInvalidConfigurationCases
     */
    public function testConfigureWithInvalidConfiguration(array $configuration)
    {
        $this->setExpectedException('PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException');

        $this->fixer->configure($configuration);
    }

    /**
     * @return array
     */
    public function getInvalidConfigurationCases()
    {
        return array(
            array(array('foo' => 'bar')),
            array(array('ignored_tags' => 'foo')),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function doTest($expected, $input = null, \SplFileInfo $file = null)
    {
        parent::doTest($this->withClassDocBlock($expected), $this->withClassDocBlock($input), $file);

        parent::doTest($this->withPropertyDocBlock($expected), $this->withPropertyDocBlock($input), $file);

        parent::doTest($this->withMethodDocBlock($expected), $this->withMethodDocBlock($input), $file);

        parent::doTest($this->withWrongElementDocBlock($expected), null, $file);
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withClassDocBlock($comment)
    {
        return $this->with('<?php

%s
class FooClass
{
}', $comment, false);
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withPropertyDocBlock($comment)
    {
        return $this->with('<?php

class FooClass
{
    %s
    private $foo;
}', $comment, true);
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withMethodDocBlock($comment)
    {
        return $this->with('<?php

class FooClass
{
    %s
    public function foo()
    {
    }
}', $comment, true);
    }

    /**
     * @param string $comment
     *
     * @return string
     */
    private function withWrongElementDocBlock($comment)
    {
        return $this->with('<?php

%s
$foo = bar();', $comment, false);
    }

    /**
     * @param string      $php
     * @param string|null $comment
     * @param bool        $indent
     *
     * @return string|null
     */
    private function with($php, $comment, $indent)
    {
        if (null === $comment) {
            return null;
        }

        if ($indent) {
            $comment = str_replace("\n", "\n    ", $comment);
        }

        return sprintf($php, preg_replace('/^\n+/', '', $comment));
    }
}
