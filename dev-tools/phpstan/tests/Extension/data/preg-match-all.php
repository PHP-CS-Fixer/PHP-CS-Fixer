<?php

namespace PhpCsFixer\PHPStan\Tests\Extension\data\preg_match_all;

use PhpCsFixer\Preg;
use function PHPStan\Testing\assertType;

function (string $name): void {
    Preg::matchAll('/^([a-z])([a-z0-9_]*)$/', $name, $matches);
    assertType('array{list<string>, list<non-empty-string>, list<string>}', $matches);
};

function (string $name): void {
    Preg::matchAll('/^([a-z])([a-z0-9_]*)$/', $name, $matches, PREG_OFFSET_CAPTURE);
    assertType('array{list<array{string, int<-1, max>}>, list<array{non-empty-string, int<-1, max>}>, list<array{string, int<-1, max>}>}', $matches);
};

function (string $name): void {
    Preg::matchAll('/^([a-z])([a-z0-9_]*)$/', $name, $matches, PREG_UNMATCHED_AS_NULL);
    assertType('array{list<string>, list<non-empty-string>, list<string>}', $matches);
};
