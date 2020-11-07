=============================================
Rule ``php_unit_set_up_tear_down_visibility``
=============================================

Changes the visibility of the ``setUp()`` and ``tearDown()`` functions of
PHPUnit to ``protected``, to match the PHPUnit TestCase.

.. warning:: Using this rule is risky.

   This fixer may change functions named ``setUp()`` or ``tearDown()`` outside
   of PHPUnit tests, when a class is wrongly seen as a PHPUnit test.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,13 +2,13 @@
    final class MyTest extends \PHPUnit_Framework_TestCase
    {
        private $hello;
   -    public function setUp()
   +    protected function setUp()
        {
            $this->hello = "hello";
        }

   -    public function tearDown()
   +    protected function tearDown()
        {
            $this->hello = null;
        }
    }

Rule sets
---------

The rule is part of the following rule set:

@PhpCsFixer:risky
  Using the `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``php_unit_set_up_tear_down_visibility`` rule.
