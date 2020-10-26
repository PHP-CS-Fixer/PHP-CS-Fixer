===============================================
Rule ``php_unit_dedicate_assert_internal_type``
===============================================

PHPUnit assertions like ``assertIsArray`` should be used over
``assertInternalType``.

.. warning:: Using this rule is risky.

   Risky when PHPUnit methods are overridden or when project has PHPUnit
   incompatibilities.

Configuration
-------------

``target``
~~~~~~~~~~

Target version of PHPUnit.

Allowed values: ``'7.5'``, ``'newest'``

Default value: ``'newest'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -3,7 +3,7 @@
    {
        public function testMe()
        {
   -        $this->assertInternalType("array", $var);
   -        $this->assertInternalType("boolean", $var);
   +        $this->assertIsArray($var);
   +        $this->assertIsBool($var);
        }
    }

Rule sets
---------

The rule is part of the following rule set:

@PHPUnit75Migration:risky
  Using the ``@PHPUnit75Migration:risky`` rule set will enable the ``php_unit_dedicate_assert_internal_type`` rule with the config below:

  ``['target' => '7.5']``
