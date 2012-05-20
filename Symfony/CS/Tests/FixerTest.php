<?php

namespace Symfony\CS\Tests;

use Symfony\CS\Fixer;

class FixerTest extends \PHPUnit_Framework_TestCase
{
    public function testPriority()
    {
        $fixer = new Fixer();

        $f1 = $this->getMock('Symfony\CS\FixerInterface');
        $f1->expects($this->any())->method('getPriority')->will($this->returnValue(0));

        $f2 = $this->getMock('Symfony\CS\FixerInterface');
        $f2->expects($this->any())->method('getPriority')->will($this->returnValue(-10));

        $f3 = $this->getMock('Symfony\CS\FixerInterface');
        $f3->expects($this->any())->method('getPriority')->will($this->returnValue(10));

        $fixer->addFixer($f1);
        $fixer->addFixer($f2);
        $fixer->addFixer($f3);

        $fixer->fix(new \DirectoryIterator(__DIR__), true);

        // check the fixers order
        $r = new \ReflectionObject($fixer);
        $p = $r->getProperty('fixers');
        $p->setAccessible(true);

        $this->assertSame(array($f3, $f1, $f2), $p->getValue($fixer));
    }
}
