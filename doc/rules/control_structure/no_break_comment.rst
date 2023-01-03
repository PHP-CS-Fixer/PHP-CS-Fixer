=========================
Rule ``no_break_comment``
=========================

There must be a comment when fall-through is intentional in a non-empty case
body.

Description
-----------

Adds a "no break" comment before fall-through cases, and removes it if there is
no fall-through.

Configuration
-------------

``comment_text``
~~~~~~~~~~~~~~~~

The text to use in the added comment and to detect it.

Allowed types: ``string``

Default value: ``'no break'``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($foo) {
        case 1:
            foo();
   +        // no break
        case 2:
            bar();
   -        // no break
            break;
        case 3:
            baz();
    }

Example #2
~~~~~~~~~~

With configuration: ``['comment_text' => 'some comment']``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    switch ($foo) {
        case 1:
            foo();
   +        // some comment
        case 2:
            foo();
    }

Rule sets
---------

The rule is part of the following rule sets:

@PER
  Using the `@PER <./../../ruleSets/PER.rst>`_ rule set will enable the ``no_break_comment`` rule with the default config.

@PSR2
  Using the `@PSR2 <./../../ruleSets/PSR2.rst>`_ rule set will enable the ``no_break_comment`` rule with the default config.

@PSR12
  Using the `@PSR12 <./../../ruleSets/PSR12.rst>`_ rule set will enable the ``no_break_comment`` rule with the default config.

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``no_break_comment`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``no_break_comment`` rule with the default config.
