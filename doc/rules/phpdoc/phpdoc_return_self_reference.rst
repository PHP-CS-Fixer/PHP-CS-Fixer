=====================================
Rule ``phpdoc_return_self_reference``
=====================================

The type of ``@return`` annotations of methods returning a reference to itself
must the configured one.

Configuration
-------------

``replacements``
~~~~~~~~~~~~~~~~

Mapping between replaced return types with new ones.

Allowed types: ``array``

Default value: ``['this' => '$this', '@this' => '$this', '$self' => 'self', '@self' => 'self', '$static' => 'static', '@static' => 'static']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
        /**
   -     * @return this
   +     * @return $this
         */
        public function test1()
        {
            return $this;
        }

        /**
   -     * @return $self
   +     * @return self
         */
        public function test2()
        {
            return $this;
        }
    }

Example #2
~~~~~~~~~~

With configuration: ``['replacements' => ['this' => 'self']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    class Sample
    {
        /**
   -     * @return this
   +     * @return self
         */
        public function test1()
        {
            return $this;
        }

        /**
         * @return $self
         */
        public function test2()
        {
            return $this;
        }
    }

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_
- `@Symfony <./../../ruleSets/Symfony.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocReturnSelfReferenceFixer <./../../../src/Fixer/Phpdoc/PhpdocReturnSelfReferenceFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocReturnSelfReferenceFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocReturnSelfReferenceFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
