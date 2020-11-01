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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;

/**
 * @deprecated since 2.16, replaced by PhpdocOrderByValueFixer
 *
 * @todo To be removed at 3.0
 *
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpUnitOrderedCoversFixer extends AbstractProxyFixer implements DeprecatedFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Order `@covers` annotation of PHPUnit tests.',
            [
                new CodeSample(
                    '<?php
/**
 * @covers Foo
 * @covers Bar
 */
final class MyTest extends \PHPUnit_Framework_TestCase
{}
'
                ),
            ]
        );
    }

    public function getSuccessorsNames()
    {
        return array_keys($this->proxyFixers);
    }

    protected function createProxyFixers()
    {
        $fixer = new PhpdocOrderByValueFixer();

        $fixer->configure([
            'annotations' => [
                'covers',
            ],
        ]);

        return [
            $fixer,
        ];
    }
}
