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
use Symfony\CS\ConfigInterface;

/**
 * @author Fabien Potencier <fabien@symfony.com>, Peter Drake <pdrake@gmail.com>
 */
class ShortEchoTagFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        // [Structure] Never use short echo tags (<?=)
        return preg_replace('/<\?\=(\s)/', '<?php print$1', $content);

        return $content;
    }

    public function getLevel()
    {
        return false;
    }

    public function getPriority()
    {
        return 0;
    }

    public function supports(\SplFileInfo $file, ConfigInterface $config)
    {
        return 'php' === $config->getFileType($file);
    }

    public function getName()
    {
        return 'short_echo_tag';
    }

    public function getDescription()
    {
        return 'PHP code must use the long tags with print; it must not use short echo tags (<?= ?>).';
    }
}
