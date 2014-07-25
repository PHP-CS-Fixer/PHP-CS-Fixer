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
        $this->assertSame($simpleFixed, $fixer->fix($this->getTestFile(), $simple));
        $this->assertSame($simpleFixed, $fixer->fix($this->getTestFile(), $simpleFixed));

        $emptyType = "$type TestType {}";
        $this->assertSame($emptyType, $fixer->fix($this->getTestFile(), $emptyType));
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
        $this->assertSame($extendedFixed, $fixer->fix($this->getTestFile(), $extended));
        $this->assertSame($extendedFixed, $fixer->fix($this->getTestFile(), $extendedFixed));

        $extended = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface, TestInterface2 {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends BaseTestClass implements TestInterface, TestInterface2
{
TEST;
        $this->assertSame($extendedFixed, $fixer->fix($this->getTestFile(), $extended));
        $this->assertSame($extendedFixed, $fixer->fix($this->getTestFile(), $extendedFixed));

        $extended = <<<TEST
abstract class TestClass extends \\Base\\TestClass implements \\TestInterface {
TEST;
        $extendedFixed = <<<TEST
abstract class TestClass extends \\Base\\TestClass implements \\TestInterface
{
TEST;

        $this->assertSame($extendedFixed, $fixer->fix($this->getTestFile(), $extended));
        $this->assertSame($extendedFixed, $fixer->fix($this->getTestFile(), $extendedFixed));
    }

    public function testControlStatements()
    {
        $fixer = new Fixer();

        $if = "if (\$someTest)\n {";
        $ifFixed = 'if ($someTest) {';
        $this->assertSame($ifFixed, $fixer->fix($this->getTestFile(), $if));
        $this->assertSame($ifFixed, $fixer->fix($this->getTestFile(), $ifFixed));

        $if = "if (test) // foo  \n{";
        $ifFixed = "if (test) { // foo";
        $this->assertSame($ifFixed, $fixer->fix($this->getTestFile(), $if));
        $this->assertSame($ifFixed, $fixer->fix($this->getTestFile(), $ifFixed));

        $elseif = "else if (...)\n{";
        $elseifFixed = "else if (...) {";
        $this->assertSame($elseifFixed, $fixer->fix($this->getTestFile(), $elseif));
        $this->assertSame($elseifFixed, $fixer->fix($this->getTestFile(), $elseifFixed));

        $func = "function download() {\n}";
        $funcFixed = "function download()\n{\n}";
        $this->assertSame($funcFixed, $fixer->fix($this->getTestFile(), $func));
        $this->assertSame($funcFixed, $fixer->fix($this->getTestFile(), $funcFixed));

        $while = "    while (\$file = \$this->getFile())\n    {";
        $whileFixed = '    while ($file = $this->getFile()) {';
        $this->assertSame($whileFixed, $fixer->fix($this->getTestFile(), $while));
        $this->assertSame($whileFixed, $fixer->fix($this->getTestFile(), $whileFixed));

        $switch = "switch(\$statement)   \n{";
        $switchFixed = 'switch($statement) {';
        $this->assertSame($switchFixed, $fixer->fix($this->getTestFile(), $switch));
        $this->assertSame($switchFixed, $fixer->fix($this->getTestFile(), $switchFixed));

        $try = "try \n{\n ... \n} \n catch (Exception \$e)\n{";
        $tryFixed = "try {\n ... \n} catch (Exception \$e) {";
        $this->assertSame($tryFixed, $fixer->fix($this->getTestFile(), $try));
        $this->assertSame($tryFixed, $fixer->fix($this->getTestFile(), $tryFixed));

        $tryInClassName = <<<'TEST'

        class FormFieldRegistry
        {
            private $fields = array();
TEST;
        $this->assertSame($tryInClassName, $fixer->fix($this->getTestFile(), $tryInClassName));
    }

    public function testFunctionDeclaration()
    {
        $fixer = new Fixer();

        $declaration = '    public function test()     {';
        $fixedDeclaration = "    public function test()\n    {";
        $this->assertSame($fixedDeclaration, $fixer->fix($this->getTestFile(), $declaration));
        $this->assertSame($fixedDeclaration, $fixer->fix($this->getTestFile(), $fixedDeclaration));

        $goodAnonymous = "filter(function () {\n    return true;\n})";
        $this->assertSame($goodAnonymous, $fixer->fix($this->getTestFile(), $goodAnonymous));

        $badAnonymous = "filter(function   () \n {\n});";
        $fixedBadAnonymous = "filter(function   () {\n});";
        $this->assertSame($fixedBadAnonymous, $fixer->fix($this->getTestFile(), $badAnonymous));
        $this->assertSame($fixedBadAnonymous, $fixer->fix($this->getTestFile(), $fixedBadAnonymous));

        $correctMethod = <<<'EOF'
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
EOF;

        $this->assertSame($correctMethod, $fixer->fix($this->getTestFile(), $correctMethod));
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
        $this->assertSame($fixedDoWhile, $fixer->fix($this->getTestFile(), $doWhile));
        $this->assertSame($fixedDoWhile, $fixer->fix($this->getTestFile(), $fixedDoWhile));
    }

    /*
     * @see https://github.com/fabpot/PHP-CS-Fixer/issues/114
     */
    public function testIssue114()
    {
        $fixer = new Fixer();

        $declarationWithDo = '    public function test($do)     {';
        $fixedDeclarationWithDo = "    public function test(\$do)\n    {";
        $this->assertSame($fixedDeclarationWithDo, $fixer->fix($this->getTestFile(), $declarationWithDo));
        $this->assertSame($fixedDeclarationWithDo, $fixer->fix($this->getTestFile(), $fixedDeclarationWithDo));

        $declarationWithElse = '    public function test($else)     {';
        $fixedDeclarationWithElse = "    public function test(\$else)\n    {";
        $this->assertSame($fixedDeclarationWithElse, $fixer->fix($this->getTestFile(), $declarationWithElse));
        $this->assertSame($fixedDeclarationWithElse, $fixer->fix($this->getTestFile(), $fixedDeclarationWithElse));

        $declarationWithTry = '    public function test($try)     {';
        $fixedDeclarationWithTry = "    public function test(\$try)\n    {";
        $this->assertSame($fixedDeclarationWithTry, $fixer->fix($this->getTestFile(), $declarationWithTry));
        $this->assertSame($fixedDeclarationWithTry, $fixer->fix($this->getTestFile(), $fixedDeclarationWithTry));
    }

    private function getTestFile($filename = __FILE__)
    {
        static $files = array();

        if (!isset($files[$filename])) {
            $files[$filename] = new \SplFileInfo($filename);
        }

        return $files[$filename];
    }
}
