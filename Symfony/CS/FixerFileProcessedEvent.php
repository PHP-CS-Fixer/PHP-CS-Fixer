<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when file was processed by Fixer.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class FixerFileProcessedEvent extends Event
{
    /**
     * Event name.
     */
    const NAME = 'fixer.file_processed';

    /**
     * Flag indicated if file changed.
     *
     * @var bool
     */
    private $fileChanged = null;

    /**
     * check is file changed.
     *
     * @return bool
     */
    public function isFileChanged()
    {
        return $this->fileChanged;
    }

    /**
     * Set is file changed flag.
     *
     * @param bool $changed
     *
     * @return FixerFileProcessedEvent
     */
    public function setFileChanged($changed)
    {
        $this->fileChanged = $changed;

        return $this;
    }
}
