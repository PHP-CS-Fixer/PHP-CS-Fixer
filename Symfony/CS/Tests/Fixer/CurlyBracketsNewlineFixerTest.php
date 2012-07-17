<?php
namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\CurlyBracketsNewlineFixer as Fixer;

class CurlyBracketsNewlineFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testSimpleTypeDefinitionsProvider
     */
    public function testSimpleTypeDefinitions($type)
    {
        $fixer = new Fixer();

        $simple = "$type TestType  {";
        $simpleFixed = "$type TestType\n{";
        $this->assertEquals($simpleFixed, $fixer->fix($this->getFileMock(), $simple));
        $this->assertEquals($simpleFixed, $fixer->fix($this->getFileMock(), $simpleFixed));

        $emptyType = "$type TestType {}";
        $this->assertEquals($emptyType, $fixer->fix($this->getFileMock(), $emptyType));
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
        $fixer = new Fixer();

        $extended = <<<TEST
class TestClass extends BaseTestClass implements TestInterface {
TEST;
        $extendedFixed = <<<TEST
class TestClass extends BaseTestClass implements TestInterface
{
TEST;
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extended));
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extendedFixed));

        $extended = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface, TestInterface2 {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface, TestInterface2
{
TEST;
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extended));
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extendedFixed));

        $extended = <<<TEST
abstract class TestClass extends \\Base\\TestClass implements \\TestInterface {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends \\Base\\TestClass implements \\TestInterface
{
TEST;

        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extended));
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extendedFixed));
    }

    public function testControlStatements()
    {
        $fixer = new Fixer();

        $if = "if (\$someTest)\n {";
        $ifFixed = 'if ($someTest) {';
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $if));
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $ifFixed));

        $if = "if (test) // foo  \n{";
        $ifFixed = "if (test) { // foo";
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $if));
        $this->assertEquals($ifFixed, $fixer->fix($this->getFileMock(), $ifFixed));

        $elseif = "else if (...)\n{";
        $elseifFixed = "else if (...) {";
        $this->assertEquals($elseifFixed, $fixer->fix($this->getFileMock(), $elseif));
        $this->assertEquals($elseifFixed, $fixer->fix($this->getFileMock(), $elseifFixed));

        $func = "function download() {\n}";
        $funcFixed = "function download()\n{\n}";
        $this->assertEquals($funcFixed, $fixer->fix($this->getFileMock(), $func));
        $this->assertEquals($funcFixed, $fixer->fix($this->getFileMock(), $funcFixed));

        $while = "    while (\$file = \$this->getFile())\n    {";
        $whileFixed = '    while ($file = $this->getFile()) {';
        $this->assertEquals($whileFixed, $fixer->fix($this->getFileMock(), $while));
        $this->assertEquals($whileFixed, $fixer->fix($this->getFileMock(), $whileFixed));

        $switch = "switch(\$statement)   \n{";
        $switchFixed = 'switch($statement) {';
        $this->assertEquals($switchFixed, $fixer->fix($this->getFileMock(), $switch));
        $this->assertEquals($switchFixed, $fixer->fix($this->getFileMock(), $switchFixed));

        $try = "try \n{\n ... \n} \n catch (Exception \$e)\n{";
        $tryFixed = "try {\n ... \n} catch (Exception \$e) {";
        $this->assertEquals($tryFixed, $fixer->fix($this->getFileMock(), $try));
        $this->assertEquals($tryFixed, $fixer->fix($this->getFileMock(), $tryFixed));

        $tryInClassName = <<<'TEST'

        class FormFieldRegistry
        {
            private $fields = array();
TEST;
        $this->assertEquals($tryInClassName, $fixer->fix($this->getFileMock(), $tryInClassName));
    }

    public function testFunctionDeclaration()
    {
        $fixer = new Fixer();

        $declaration = '    public function test()     {';
        $fixedDeclaration = "    public function test()\n    {";
        $this->assertEquals($fixedDeclaration, $fixer->fix($this->getFileMock(), $declaration));
        $this->assertEquals($fixedDeclaration, $fixer->fix($this->getFileMock(), $fixedDeclaration));

        $goodAnonymous = "filter(function () {\n    return true;\n})";
        $this->assertEquals($goodAnonymous, $fixer->fix($this->getFileMock(), $goodAnonymous));

        $badAnonymous = "filter(function   () \n {\n});";
        $fixedBadAnonymous = "filter(function   () {\n});";
        $this->assertEquals($fixedBadAnonymous, $fixer->fix($this->getFileMock(), $badAnonymous));
        $this->assertEquals($fixedBadAnonymous, $fixer->fix($this->getFileMock(), $fixedBadAnonymous));

        $correctMethod = <<<'EOF'
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
EOF;

        $this->assertEquals($correctMethod, $fixer->fix($this->getFileMock(), $correctMethod));
    }

    public function testDoWhile()
    {
        $fixer = new Fixer();

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
        $this->assertEquals($fixedDoWhile, $fixer->fix($this->getFileMock(), $doWhile));
        $this->assertEquals($fixedDoWhile, $fixer->fix($this->getFileMock(), $fixedDoWhile));
    }

    /*
     * @see https://github.com/fabpot/PHP-CS-Fixer/issues/114
     */
    public function testIssue114()
    {
        $fixer = new Fixer();

        $declarationWithDo = '    public function test($do)     {';
        $fixedDeclarationWithDo = "    public function test(\$do)\n    {";
        $this->assertEquals($fixedDeclarationWithDo, $fixer->fix($this->getFileMock(), $declarationWithDo));
        $this->assertEquals($fixedDeclarationWithDo, $fixer->fix($this->getFileMock(), $fixedDeclarationWithDo));

        $declarationWithElse = '    public function test($else)     {';
        $fixedDeclarationWithElse = "    public function test(\$else)\n    {";
        $this->assertEquals($fixedDeclarationWithElse, $fixer->fix($this->getFileMock(), $declarationWithElse));
        $this->assertEquals($fixedDeclarationWithElse, $fixer->fix($this->getFileMock(), $fixedDeclarationWithElse));

        $declarationWithTry = '    public function test($try)     {';
        $fixedDeclarationWithTry = "    public function test(\$try)\n    {";
        $this->assertEquals($fixedDeclarationWithTry, $fixer->fix($this->getFileMock(), $declarationWithTry));
        $this->assertEquals($fixedDeclarationWithTry, $fixer->fix($this->getFileMock(), $fixedDeclarationWithTry));
    }

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
