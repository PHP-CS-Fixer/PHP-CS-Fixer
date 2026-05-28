========================
Rule ``phpdoc_tag_type``
========================

Forces PHPDoc tags to be either regular annotations or inline.

Warning
-------

This rule is CONFIGURABLE
~~~~~~~~~~~~~~~~~~~~~~~~~

You can configure this rule using the following option: ``tags``.

Configuration
-------------

``tags``
~~~~~~~~

The list of tags to fix.

Allowed types: ``array<string, 'annotation'|'inline'>``

Default value: ``['api' => 'annotation', 'author' => 'annotation', 'copyright' => 'annotation', 'deprecated' => 'annotation', 'example' => 'annotation', 'global' => 'annotation', 'inheritDoc' => 'annotation', 'internal' => 'annotation', 'license' => 'annotation', 'method' => 'annotation', 'package' => 'annotation', 'param' => 'annotation', 'property' => 'annotation', 'return' => 'annotation', 'see' => 'annotation', 'since' => 'annotation', 'throws' => 'annotation', 'todo' => 'annotation', 'uses' => 'annotation', 'var' => 'annotation', 'version' => 'annotation']``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * {@api}
   + * @api
     */

Example #2
~~~~~~~~~~

With configuration: ``['tags' => ['inheritdoc' => 'inline']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    /**
   - * @inheritdoc
   + * {@inheritdoc}
     */

Rule sets
---------

The rule is part of the following rule sets:

- `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ with config:

  ``['tags' => ['inheritDoc' => 'inline']]``

- `@Symfony <./../../ruleSets/Symfony.rst>`_ with config:

  ``['tags' => ['inheritDoc' => 'inline']]``

References
----------

- Fixer class: `PhpCsFixer\\Fixer\\Phpdoc\\PhpdocTagTypeFixer <./../../../src/Fixer/Phpdoc/PhpdocTagTypeFixer.php>`_
- Test class: `PhpCsFixer\\Tests\\Fixer\\Phpdoc\\PhpdocTagTypeFixerTest <./../../../tests/Fixer/Phpdoc/PhpdocTagTypeFixerTest.php>`_

The test class defines officially supported behaviour. Each test case is a part of our backward compatibility promise.
