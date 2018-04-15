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

namespace PhpCsFixer\Tests\Smoke;

use Keradus\CliExecutor\CommandExecutor;
use PhpCsFixer\Utils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @coversNothing
 * @group covers-nothing
 * @large
 */
final class InstallViaComposerTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        try {
            CommandExecutor::create('php --version', __DIR__)->getResult();
        } catch (\RuntimeException $e) {
            self::markTestSkipped('Missing `php` env script. Details:'."\n".$e->getMessage());
        }

        try {
            CommandExecutor::create('composer --version', __DIR__)->getResult();
        } catch (\RuntimeException $e) {
            self::markTestSkipped('Missing `composer` env script. Details:'."\n".$e->getMessage());
        }
    }

    public function testInstallationIsPossible()
    {
        $fs = new Filesystem();

        $tmpPath = tempnam(sys_get_temp_dir(), 'cs_fixer_tmp_');
        unlink($tmpPath);
        $fs->mkdir($tmpPath);

        $initialComposerFileState = array(
            'repositories' => array(
                array(
                    'type' => 'path',
                    'url' => __DIR__.'/../..',
                ),
            ),
            'require' => array(
                'friendsofphp/php-cs-fixer' => '*@dev',
            ),
        );

        file_put_contents(
            $tmpPath.'/composer.json',
            json_encode($initialComposerFileState, Utils::calculateBitmask(array('JSON_PRETTY_PRINT')))
        );

        $this->assertSame(0, CommandExecutor::create('composer install -q', $tmpPath)->getResult()->getCode());
        $this->assertSame(0, CommandExecutor::create('composer dump-autoload --optimize', $tmpPath)->getResult()->getCode());
        $this->assertSame(0, CommandExecutor::create('php vendor/autoload.php', $tmpPath)->getResult()->getCode());
        $this->assertSame(0, CommandExecutor::create('vendor/bin/php-cs-fixer --version', $tmpPath)->getResult()->getCode());

        $fs->remove($tmpPath);
    }
}
