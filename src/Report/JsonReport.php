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

namespace PhpCsFixer\Report;

use PhpCsFixer\ReportInterface;

/**
 * @author Boris Gorbylev <ekho@ekho.name>
 *
 * @internal
 */
final class JsonReport implements ReportInterface
{
    /** @var array */
    private $changed = array();

    /** @var bool */
    private $addAppliedFixers = false;

    /** @var int */
    private $time;

    /** @var int */
    private $memory;

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * {@inheritdoc}
     */
    public function setChanged(array $changed)
    {
        $this->changed = $changed;
    }

    /**
     * @param bool $addAppliedFixers
     *
     * @return $this
     */
    public function setAddAppliedFixers($addAppliedFixers)
    {
        $this->addAppliedFixers = $addAppliedFixers;

        return $this;
    }

    /**
     * @param int $time
     *
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @param int $memory
     *
     * @return $this
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $jFiles = array();

        foreach ($this->changed as $file => $fixResult) {
            $jfile = array('name' => $file);

            if ($this->addAppliedFixers) {
                $jfile['appliedFixers'] = $fixResult['appliedFixers'];
            }

            if (!empty($fixResult['diff'])) {
                $jfile['diff'] = $fixResult['diff'];
            }

            $jFiles[] = $jfile;
        }

        $json = array(
            'files' => $jFiles,
        );

        if ($this->time !== null) {
            $json['time'] = array(
                'total' => round($this->time / 1000, 3),
            );
        }

        if ($this->memory !== null) {
            $json['memory'] = round($this->memory / 1024 / 1024, 3);
        }

        return json_encode($json);
    }
}
