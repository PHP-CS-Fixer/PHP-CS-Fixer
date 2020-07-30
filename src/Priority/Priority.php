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

namespace PhpCsFixer\Priority;

class Priority
{
    private $priority = 0;
    private $higherPriorities = [];

    public function getPriority()
    {
        return $this->priority;
    }

    public function addLowerPriority(self $lowerPriority)
    {
        $lowerPriority->higherPriorities[] = $this;
        $this->updatePriority($lowerPriority->getPriority());
    }

    public function updatePriority($priority)
    {
        $this->priority = max($this->priority, $priority + 1);
        foreach ($this->higherPriorities as $priority) {
            $priority->updatePriority($this->priority);
        }
    }
}
