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

namespace PhpCsFixer\Event;

// Since PHP-CS-FIXER is PHP 5.6 compliant we can't always use Symfony Contracts (currently needs PHP ^7.1.3)
// This conditionnal inheritance will be useless when PHP-CS-FIXER no longer supports PHP versions
// inferior to Symfony/Contracts PHP minimal version
if (class_exists(\Symfony\Contracts\EventDispatcher\Event::class)) {
    class Event extends \Symfony\Contracts\EventDispatcher\Event
    {
    }
} else {
    class Event extends \Symfony\Component\EventDispatcher\Event
    {
    }
}
