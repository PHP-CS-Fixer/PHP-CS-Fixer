<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\PSR2;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

class CurlyBracketsNewlineFixerTest extends AbstractFixerTestBase
{
    public function testFunctionDeclaration()
    {
        $declaration = '    public function test()     {';
        $fixedDeclaration = "    public function test()\n    {";
        $this->makeTest($fixedDeclaration, $declaration);

        $func = "function download() {\n}";
        $funcFixed = "function download()\n{\n}";
        $this->makeTest($funcFixed, $func);

        $goodAnonymous = "filter(function () {\n    return true;\n})";
        $this->makeTest($goodAnonymous);

        $badAnonymous = "filter(function   () \n {\n});";
        $fixedBadAnonymous = "filter(function   () {\n});";
        $this->makeTest($fixedBadAnonymous, $badAnonymous);

        $correctMethod = <<<'EOF'
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
EOF;

        $this->makeTest($correctMethod);
    }

    /*
     * @see https://github.com/fabpot/PHP-CS-Fixer/issues/114
     */
    public function testIssue114()
    {
        $declarationWithDo = '    public function test($do)     {';
        $fixedDeclarationWithDo = "    public function test(\$do)\n    {";
        $this->makeTest($fixedDeclarationWithDo, $declarationWithDo);

        $declarationWithElse = '    public function test($else)     {';
        $fixedDeclarationWithElse = "    public function test(\$else)\n    {";
        $this->makeTest($fixedDeclarationWithElse, $declarationWithElse);

        $declarationWithTry = '    public function test($try)     {';
        $fixedDeclarationWithTry = "    public function test(\$try)\n    {";
        $this->makeTest($fixedDeclarationWithTry, $declarationWithTry);
    }
}
