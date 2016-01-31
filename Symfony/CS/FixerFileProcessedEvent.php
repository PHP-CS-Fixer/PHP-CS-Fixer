<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
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

    const STATUS_UNKNOWN = 0;
    const STATUS_INVALID = 1;
    const STATUS_SKIPPED = 2;
    const STATUS_NO_CHANGES = 3;
    const STATUS_FIXED = 4;
    const STATUS_EXCEPTION = 5;
    const STATUS_LINT = 6;

    /**
     * File statuses map.
     *
     * @var array
     */
    private static $statusMap = array(
        self::STATUS_UNKNOWN => array('symbol' => '?', 'description' => 'unknown'),
        self::STATUS_INVALID => array('symbol' => 'I', 'description' => 'invalid file syntax, file ignored'),
        self::STATUS_SKIPPED => array('symbol' => '',  'description' => ''),
        self::STATUS_NO_CHANGES => array('symbol' => '.', 'description' => 'no changes'),
        self::STATUS_FIXED => array('symbol' => 'F', 'description' => 'fixed'),
        self::STATUS_EXCEPTION => array('symbol' => 'E', 'description' => 'error'),
        self::STATUS_LINT => array('symbol' => 'E', 'description' => 'error'),
    );

    /**
     * File status.
     *
     * @var int
     */
    private $status = self::STATUS_UNKNOWN;

    /**
     * Create instance.
     *
     * @return FixerFileProcessedEvent
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Get status map.
     *
     * @return array
     */
    public static function getStatusMap()
    {
        return self::$statusMap;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get status as string.
     *
     * @return string
     */
    public function getStatusAsString()
    {
        return self::$statusMap[$this->status]['symbol'];
    }

    /**
     * Set status.
     *
     * @param int $status
     *
     * @return FixerFileProcessedEvent
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
