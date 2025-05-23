<?php

namespace PhpCsFixer\PHPStan\Tests\Extension\data\preg_match;

use PhpCsFixer\Preg;
use function PHPStan\Testing\assertType;

function (string $name): void {
    Preg::match('/^([a-z])([a-z0-9_]*)$/', $name, $matches);
    assertType('array{0?: string, 1?: non-empty-string, 2?: string}', $matches);
};

function (string $name): void {
    if (Preg::match('/^([a-z])([a-z0-9_]*)$/', $name, $matches)) {
        assertType('array{string, non-empty-string, string}', $matches);
    } else {
        assertType('array{}', $matches);
    }
};

function (string $name): void {
    Preg::match('/^([a-z])([a-z0-9_]*)$/', $name, $matches, PREG_OFFSET_CAPTURE);
    assertType('array{0?: array{string, int<-1, max>}, 1?: array{non-empty-string, int<-1, max>}, 2?: array{string, int<-1, max>}}', $matches);
};

function (string $name): void {
    Preg::match('/^([a-z])([a-z0-9_]*)$/', $name, $matches, PREG_UNMATCHED_AS_NULL);
    assertType('array{0?: string, 1?: non-empty-string|null, 2?: string|null}', $matches);
};
