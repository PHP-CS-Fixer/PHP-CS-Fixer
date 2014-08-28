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
            'control_structure_braces',
            'drupal_braces',
            'elseif',
            'eof_ending',
            'extra_empty_lines',
            'include',
            'linefeed',
            'php_closing_tag',
            'phpdoc_params',
            'short_tag',
            'short_echo_tag',
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
        $fileExtension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        $extensionMap = array(
            'php' => 'php',
            'inc' => 'php',
            'module' => 'php'
        );

        return (isset($extensionMap[$fileExtension]) ? $extensionMap[$fileExtension] : $fileExtension);
    }
}
