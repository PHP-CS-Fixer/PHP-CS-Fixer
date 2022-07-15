==========================
Rule ``no_unused_imports``
==========================

Unused ``use`` statements must be removed.

Configuration
-------------

``case_sensitive_in_comment``
~~~~~~~~~~~~~~~

Whether usage detection should be case sensitive

Allowed types: ``bool``

Default value: ``false``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use \DateTime;
   -use \Exception;
    use \Users;

    //users
    new DateTime();

Example #2
~~~~~~~~~~

With configuration: ``['case_sensitive_in_comment' => true]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
    use \DateTime;
   -use \Exception;
   -use \Users;

    //users
    new DateTime();

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixerRisky <./../../ruleSets/PhpCsFixerRisky.rst>`_ rule set will enable the ``no_unused_imports`` rule.

@Symfony
  Using the `@SymfonyRisky <./../../ruleSets/SymfonyRisky.rst>`_ rule set will enable the ``no_unused_imports`` rule.
