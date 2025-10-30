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

use PhpCsFixer\ComposerJsonReader;
use PhpCsFixer\Documentation\FixerDocumentGenerator;

require_once dirname(__DIR__).'/vendor/autoload.php';

// Call this to:
// - populate the cache of `FixerDocumentGenerator`,
// - trigger all the deprecations while pre-calculating internal cache of `FixerDocumentGenerator`,
// so it will happen on a random test.
//
// The used argument is a random rule - it doesn't matter which one we call.
FixerDocumentGenerator::getSetsOfRule('ordered_imports'); // @phpstan-ignore-line

// drop the `ComposerJsonReader` instance created while populating `FixerDocumentGenerator` cache
Closure::bind(static function (): void { ComposerJsonReader::$singleton = null; }, null, ComposerJsonReader::class)();
