<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer;

use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

/**
 * @internal
 */
final class DocBlock
{
    public static function create(string $content): PhpDocNode
    {
        static $lexer = null;
        static $phpDocParser = null;

        if (null === $phpDocParser) {
            $lexer = new Lexer();
            $constExprParser = new ConstExprParser();
            $typeParser = new TypeParser($constExprParser);
            $phpDocParser = new PhpDocParser($typeParser, $constExprParser);
        }

        return $phpDocParser->parse(new TokenIterator($lexer->tokenize($content)));
    }
}
