<?php
namespace Symfony\CS\Tests\Fixer;

use Symfony\CS\Fixer\CurlyBracketsNewlineFixer as Fixer;

class CurlyBracketsNewlineFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testClassDefinitions()
    {
        $fixer = new Fixer();

        $simple = "class TestClass  {";
        $simpleFixed = "class TestClass\n{";
        $this->assertEquals($simpleFixed, $fixer->fix($this->getFileMock(), $simple));
        $this->assertEquals($simpleFixed, $fixer->fix($this->getFileMock(), $simpleFixed));

        $extended = <<<TEST
class TestClass extends BaseTestClass implements TestInterface {
TEST;
        $extendedFixed = <<<TEST
class TestClass extends BaseTestClass implements TestInterface
{
TEST;
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extended));
        $this->assertEquals($extendedFixed, $fixer->fix($this->getFileMock(), $extendedFixed));

        $emptyClass = "class TestClass {}";
        $this->assertEquals($emptyClass, $fixer->fix($this->getFileMock(), $emptyClass));

        $extended = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface
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

    private function getFileMock()
    {
        return $this->getMockBuilder('\SplFileInfo')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
