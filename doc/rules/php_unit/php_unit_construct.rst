===========================
Rule ``php_unit_construct``
===========================

PHPUnit assertion method calls like ``->assertSame(true, $foo)`` should be
written with dedicated method like ``->assertTrue($foo)``.

.. warning:: Using this rule is risky.

   Fixer could be risky if one is overriding PHPUnit's native methods.

Configuration
-------------

``assertions``
~~~~~~~~~~~~~~

List of assertion methods to fix.

Allowed values: a subset of ``['assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame']``

Default value: ``['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
   @@ -1,9 +1,9 @@
    <?php
    final class FooTest extends \PHPUnit_Framework_TestCase {
        public function testSomething() {
   -        $this->assertEquals(false, $b);
   -        $this->assertSame(true, $a);
   -        $this->assertNotEquals(null, $c);
   -        $this->assertNotSame(null, $d);
   +        $this->assertFalse($b);
   +        $this->assertTrue($a);
   +        $this->assertNotNull($c);
   +        $this->assertNotNull($d);
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['assertions' => ['assertSame', 'assertNotSame']]``.

.. code-block:: diff

   --- Original
   +++ New
   @@ -2,8 +2,8 @@
    final class FooTest extends \PHPUnit_Framework_TestCase {
        public function testSomething() {
            $this->assertEquals(false, $b);
   -        $this->assertSame(true, $a);
   +        $this->assertTrue($a);
            $this->assertNotEquals(null, $c);
   -        $this->assertNotSame(null, $d);
   +        $this->assertNotNull($d);
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

@Symfony:risky
  Using the ``@Symfony:risky`` rule set will enable the ``php_unit_construct`` rule with the default config.

@PhpCsFixer:risky
  Using the ``@PhpCsFixer:risky`` rule set will enable the ``php_unit_construct`` rule with the default config.
