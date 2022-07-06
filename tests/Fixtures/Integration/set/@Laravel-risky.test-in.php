<?php

namespace Illuminate\Database\Eloquent;

use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection implements QueueableCollection
{
    /**
     * Function for testing Laravel risky code style fixer rule set.
     *
     * @param string $foo
     * @param string $bar
     * @param array $with
     * @return integer
     */
    static public function testCodeStyle($foo = '', $bar, $with = [])
    {
        if (sizeof($with)) {
            echo 'Once you pop, you can`t stop! '.$foo.$bar;
            array_pop($with);
            Collection::testCodeStyle('', $foo, $with);
        }
        return count($with);
    }

}