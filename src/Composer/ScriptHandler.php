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

namespace PhpCsFixer\Composer;

// Including it breaks tests.
//use Composer\Script\Event;

/**
 * @author Dmitry Danilson <patchranger+github@gmail.com>
 */
class ScriptHandler
{
    /**
     * Enables automatic code style fixing by pre-commit hook.
     *
     * @param Event $event
     */
    public static function enableAutoCSFix($event)
    {
        if (file_exists('./.git')) {
            $phpCsFixerPath = file_exists('./bin/php-cs-fixer')
                ? './vendor/friendsofphp/php-cs-fixer/Symfony/CS'
                : '.';
            copy("{$phpCsFixerPath}/build/hooks/pre-commit", './.git/hooks/pre-commit');
            chmod('./.git/hooks/pre-commit', 0775);
        }
    }
}
