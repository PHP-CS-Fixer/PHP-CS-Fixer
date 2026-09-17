=============================
Rule ``no_unset_on_property``
=============================

Properties should be set to ``null`` instead of using ``unset``.

Warning
-------

This rule is RISKY
~~~~~~~~~~~~~~~~~~

Risky when relying on attributes to be removed using ``unset`` rather than be
set to ``null``. Changing variables to ``null`` instead of unsetting means these
still show up when looping over class variables and reference properties remain
unbroken. Since PHP 7.4, this rule might introduce ``null`` assignments to
properties whose type declaration does not allow it.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -unset($this->a);
   +$this->a = null;

Rule sets
---------

The rule is part of the following rule set:

- `@PhpCsFixer:risky <./../../ruleSets/PhpCsFixerRisky.rst>`_

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\LanguageConstruct\\NoUnsetOnPropertyFixer <./../../../src/Fixer/LanguageConstruct/NoUnsetOnPropertyFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\LanguageConstruct\\NoUnsetOnPropertyFixerTest <./../../../tests/Fixer/LanguageConstruct/NoUnsetOnPropertyFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
