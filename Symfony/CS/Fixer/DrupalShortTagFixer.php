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
class DrupalShortTagFixer implements FixerInterface
{
    public function fix(\SplFileInfo $file, $content)
    {
        $content = $this->shortTagsFix($content);
        $content = $this->shortEchoTagsFix($content);

        return $content;
    }

    public function getLevel()
    {
        return FALSE;
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
        return 'drupal_short_tag';
    }

    public function getDescription()
    {
        return 'PHP code must use the long <?php ?> tags or long tags with print (in lieu of short-echo <?= ?> tags); it must not use short tags.';
    }

    private function shortTagsFix($content)
    {
      // [Structure] Never use short tags (<?)
      return preg_replace('/<\?(\s)/', '<?php$1', $content);
    }

    private function shortEchoTagsFix($content)
    {
      // [Structure] Never use short echo tags (<?=)
      return preg_replace('/<\?\=(\s)/', '<?php print$1', $content);
    }
}
