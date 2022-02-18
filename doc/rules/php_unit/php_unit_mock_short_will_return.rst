========================================
Rule ``php_unit_mock_short_will_return``
========================================

Usage of PHPUnit's mock e.g. ``->will($this->returnValue(..))`` must be replaced
by its shorter equivalent such as ``->willReturn(...)``.

Warning
-------

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

Risky when PHPUnit classes are overridden or not accessible, or when project has
PHPUnit incompatibilities.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        public function testSomeTest()
        {
            $someMock = $this->createMock(Some::class);
   -        $someMock->method("some")->will($this->returnSelf());
   -        $someMock->method("some")->will($this->returnValue("example"));
   -        $someMock->method("some")->will($this->returnArgument(2));
   -        $someMock->method("some")->will($this->returnCallback("str_rot13"));
   -        $someMock->method("some")->will($this->returnValueMap(["a","b","c"]));
   +        $someMock->method("some")->willReturnSelf();
   +        $someMock->method("some")->willReturn("example");
   +        $someMock->method("some")->willReturnArgument(2);
   +        $someMock->method("some")->willReturnCallback("str_rot13");
   +        $someMock->method("some")->willReturnMap(["a","b","c"]);
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``php_unit_mock_short_will_return`` rule.

@Symfony:risky
  Using the `@Symfony:risky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``php_unit_mock_short_will_return`` rule.
