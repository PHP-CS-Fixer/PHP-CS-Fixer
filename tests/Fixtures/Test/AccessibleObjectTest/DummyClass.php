<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixtures\Test\AccessibleObjectTest;

/**
 * @author Dariusz Rumi�ski <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class DummyClass
{
    private $privateVar = 'privateVar_value';
    public $publicVar = 'publicVar_value';

    public function publicMethod()
    {
        return 'publicMethod_result';
    }

    private function privateMethod()
    {
        return 'privateMethod_result';
    }
}
