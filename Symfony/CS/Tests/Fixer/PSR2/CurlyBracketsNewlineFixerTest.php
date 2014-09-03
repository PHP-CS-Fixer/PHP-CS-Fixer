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
    /**
     * @dataProvider testSimpleTypeDefinitionsProvider
     */
    public function testSimpleTypeDefinitions($type)
    {
        $this->makeTest("$type TestType\n{", "$type TestType  {");
        $this->makeTest("$type TestType {}");
    }

    public function testSimpleTypeDefinitionsProvider()
    {
        return array(
            array('class'),
            array('interface'),
            array('trait'),
        );
    }

    public function testExtendedClassDefinitions()
    {
        $extended = <<<TEST
class TestClass extends BaseTestClass implements TestInterface {
TEST;
        $extendedFixed = <<<TEST
class TestClass extends BaseTestClass implements TestInterface
{
TEST;
        $this->makeTest($extendedFixed, $extended);

        $extended = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface, TestInterface2 {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface, TestInterface2
{
TEST;
        $this->makeTest($extendedFixed, $extended);

        $extended = <<<TEST
abstract class TestClass extends \\Base\\TestClass implements \\TestInterface {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends \\Base\\TestClass implements \\TestInterface
{
TEST;
        $this->makeTest($extendedFixed, $extended);
    }

    public function testControlStatements()
    {
        $if = "if (\$someTest)\n {";
        $ifFixed = 'if ($someTest) {';
        $this->makeTest($ifFixed, $if);

        $if = "if (test) // foo  \n{";
        $ifFixed = "if (test) { // foo";
        $this->makeTest($ifFixed, $if);

        $elseif = "else if (...)\n{";
        $elseifFixed = "else if (...) {";
        $this->makeTest($elseifFixed, $elseif);

        $func = "function download() {\n}";
        $funcFixed = "function download()\n{\n}";
        $this->makeTest($funcFixed, $func);

        $while = "    while (\$file = \$this->getFile())\n    {";
        $whileFixed = '    while ($file = $this->getFile()) {';
        $this->makeTest($whileFixed, $while);

        $switch = "switch(\$statement)   \n{";
        $switchFixed = 'switch($statement) {';
        $this->makeTest($switchFixed, $switch);

        $try = "try \n{\n ... \n} \n catch (Exception \$e)\n{";
        $tryFixed = "try {\n ... \n} catch (Exception \$e) {";
        $this->makeTest($tryFixed, $try);

        $tryInClassName = <<<'TEST'

        class FormFieldRegistry
        {
            private $fields = array();
TEST;
        $this->makeTest($tryInClassName);
    }

    public function testFunctionDeclaration()
    {
        $declaration = '    public function test()     {';
        $fixedDeclaration = "    public function test()\n    {";
        $this->makeTest($fixedDeclaration, $declaration);

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

    public function testDoWhile()
    {
        $doWhile = <<<'EOF'

    do
    {
        echo $test;
    }
    while ($test = $this->getTest());

EOF;
        $fixedDoWhile = <<<'EOF'

    do {
        echo $test;
    } while ($test = $this->getTest());

EOF;

        $this->makeTest($fixedDoWhile, $doWhile);
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
