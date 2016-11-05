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

use PhpCsFixer\Tests\TokenCaster;
use PhpCsFixer\Tokenizer\Token;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\VarDumper;

require_once __DIR__.'/../vendor/autoload.php';

if (class_exists('\Symfony\Component\VarDumper\VarDumper')) {
    $cloner = new VarCloner();
    $cloner->addCasters(array(Token::class => TokenCaster::class.'::castToken'));
    $dumper = new CliDumper();
    VarDumper::setHandler(
        function ($var) use ($cloner, $dumper) {
            $dumper->dump($cloner->cloneVar($var));
        }
    );
}
