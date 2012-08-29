<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Config;

use Symfony\CS\Finder\DrupalFinder;

/**
 * @author Peter Drake <pdrake@gmail.com>
 */
class DrupalConfig extends Config
{
    public function __construct()
    {
        parent::__construct();

        $this->finder = new DrupalFinder();
        $this->fixers = array(
            'drupal_indentation',
            'controls_spaces',
            'drupal_braces',
            'elseif',
            'eof_ending',
            'extra_empty_lines',
            'include',
            'linefeed',
            'php_closing_tag',
            'phpdoc_params',
            'drupal_short_tag',
            'trailing_spaces',
            'unused_use',
            'visibility'
        );
    }

    public function getName()
    {
        return 'drupal';
    }

    public function getDescription()
    {
        return 'The configuration for a Drupal module';
    }

    public function getFileType(\SplFileInfo $file)
    {
        $file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        $extension_map = array(
            'php' => 'php',
            'inc' => 'php',
            'module' => 'php'
        );

        return (isset($extension_map[$file_extension]) ? $extension_map[$file_extension] : $file_extension);
    }
}
