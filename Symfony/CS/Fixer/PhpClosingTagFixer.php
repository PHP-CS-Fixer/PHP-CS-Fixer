<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * @author Jakub Zalas <jakub@zalas.pl>
 */
class PhpClosingTagFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        if (strpos($content, '<?php') === 0) {
            return preg_replace('/( *)\?>\s*$/s', '', $content);
        }

        return $content;
    }

    public function getLevel()
    {
        // defined in PSR-2 2.2
        return FixerInterface::PSR2_LEVEL;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName()
    {
        return 'php_closing_tag';
    }

    public function getDescription()
    {
        return 'The closing ?> tag MUST be omitted from files containing only PHP.';
    }
}
