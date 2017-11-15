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

/**
 * Input for @see \PhpCsFixer\TestsFixerProxyTest
 *
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
namespace PhpCsFixer\Tests\Fixtures\FixerProxy;

class DeprecatedFixer
{
    public function __construct()
    {
        @trigger_error('I am deprecated, please do not use me', E_USER_DEPRECATED);
    }
}
