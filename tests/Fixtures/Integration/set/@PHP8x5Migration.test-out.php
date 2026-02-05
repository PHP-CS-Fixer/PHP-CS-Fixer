<?php

// https://wiki.php.net/rfc/deprecations_php_8_5#deprecate_semicolon_after_case_in_switch_statement
switch ($value) {
    case 'foo':
    case 'bar':
    case 'baz':
        echo 'foo, bar, or baz';
        break;
    default:
        echo 'Other';
}

// https://wiki.php.net/rfc/closures_in_const_expr
class ClosuresInAttibutes
{
    #[AttributeProperty(static function (): void {})]
    private string $s;
    #[Attribute11(static function (): void {})]
    #[Attribute12(named: static function (): void {})]
    public function f1() {}
    #[Attribute21(static function (): void {})]
    #[Attribute22(named1: static function (): void {}, named2: static function (): void {}, named3: static function (): void {})]
    #[Attribute23(named: static function (): void {})]
    public function f2() {}
    #[Attribute__invoke(static function (): void {})]
    public function __invoke() {}
    #[Attribute3(
        static function (): void {},
        named1: static function (): void {},
        named2: static function (): void {},
    )]
    public function f3() {}
}
