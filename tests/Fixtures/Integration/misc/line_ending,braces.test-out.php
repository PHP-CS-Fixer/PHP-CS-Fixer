<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Coding standards demonstration.
 */
class FooBar
{
	const SOME_CONST = 42;

	private $fooBar;

	/**
	 * @param string $dummy Some argument description
	 */
	public function __construct($dummy)
	{
		$this->fooBar = $this->transformText($dummy);
	}
}
