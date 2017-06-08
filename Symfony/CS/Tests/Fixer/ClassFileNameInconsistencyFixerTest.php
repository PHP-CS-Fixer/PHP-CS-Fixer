<?php

use Symfony\CS\Fixer\ClassFilenameInconsistencyFixer;

/**
 * @author Mark van der Velden <mark@dynom.nl>
 */
class ClassFileNameInconsistencyFixerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var String
     */
    private $fixtureDirectory;

    /**
     * @var ClassFilenameInconsistencyFixer
     */
    private $fixer;


    public function setUp()
    {
        $this->fixer = new ClassFilenameInconsistencyFixer;
        $this->fixtureDirectory = __DIR__ .'/../Fixtures/ClassFileNameInconsistencyTest/';
    }


    public function testGoodNamingConsistency()
    {
        $filePath = $this->fixtureDirectory .'Good.php';
        $this->fixer->fix(
            new \SplFileInfo($filePath),
            file_get_contents($filePath)
        );
    }


    /**
     * @dataProvider badFilePathProvider
     */
    public function testBadNamingConsistency($filePath)
    {
        // Suppressing warnings.
        ob_start();
        $this->fixer->fix(
            new \SplFileInfo($filePath),
            file_get_contents($filePath)
        );
        ob_end_clean();

        $this->assertTrue($this->fixer->producedAWarning());
    }


    /**
     * @return array
     */
    public function badFilePathProvider()
    {
        $this->fixtureDirectory = __DIR__ .'/../Fixtures/ClassFileNameInconsistencyTest/';

        return array(
            array($this->fixtureDirectory .'bad.php'),
            array($this->fixtureDirectory .'badThree.php'),
            array($this->fixtureDirectory .'badtwo.php'),
        );
    }
}