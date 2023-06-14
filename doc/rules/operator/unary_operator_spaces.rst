==============================
Rule ``unary_operator_spaces``
==============================

Unary operators should be surrounded by space as configured.

Description
-----------

When using the ``no_spaces`` option, the leading whitespace in unary successor
operators will only be removed. Likewise, the trailing whitespace in unary
predecessor operators will only be removed. The ``leading_space`` option will
force a leading whitespace but the opposite side of the operator will be
unchanged. Conversely, the ``trailing_space`` option will force a trailing
whitespace but the opposite side of the operator will be unchanged. Use the
``leading_and_trailing_spaces`` option to force whitespaces on both sides of the
unary operator.

Configuration
-------------

``default``
~~~~~~~~~~~

Default fix strategy.

Allowed values: ``'leading_and_trailing_spaces'``, ``'leading_space'``, ``'no_spaces'``, ``'trailing_space'`` and ``null``

Default value: ``'no_spaces'``

``operators``
~~~~~~~~~~~~~

Dictionary of ``unary operator`` => ``fix strategy`` values that differ from the
default strategy. Supported operators are ``++``, ``--``, ``+``, ``-``, ``&``,
``!``, ``~``, ``@`` and ``...``.

Allowed types: ``array``

Default value: ``[]``

Examples
--------

Example #1
~~~~~~~~~~

*Default* configuration.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -$sample ++;
   --- $sample;
   -$sample = ! ! $a;
   -$sample = ~  $c;
   -function & foo() {}
   +$sample++;
   +--$sample;
   +$sample = !!$a;
   +$sample = ~$c;
   +function &foo() {}

Example #2
~~~~~~~~~~

With configuration: ``['default' => 'leading_space', 'operators' => ['&' => 'no_spaces']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (!$isBar()) {
   -    $sample++;
   +if ( !$isBar()) {
   +    $sample ++;

   -    for (; $sample <= 0;--$sample) {
   -        $a =& foo();
   -        $b =~$c;
   +    for (; $sample <= 0; --$sample) {
   +        $a =&foo();
   +        $b = ~$c;
        }
    }

Example #3
~~~~~~~~~~

With configuration: ``['default' => 'trailing_space', 'operators' => ['!' => 'leading_space']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -++$sample;
   ---$sample;
   +++ $sample;
   +-- $sample;
    $sample = !foo();
   -$sample = ~$b;
   -function &foo() {}
   +$sample = ~ $b;
   +function & foo() {}

Example #4
~~~~~~~~~~

With configuration: ``['operators' => ['!' => 'leading_and_trailing_spaces']]``.

.. code-block:: diff

   --- Original
   +++ New
    <?php
   -if (!$bar) {
   +if ( ! $bar) {
        echo "Help!";

   -    return !$a;
   +    return ! $a;
    }

Rule sets
---------

The rule is part of the following rule sets:

@PhpCsFixer
  Using the `@PhpCsFixer <./../../ruleSets/PhpCsFixer.rst>`_ rule set will enable the ``unary_operator_spaces`` rule with the default config.

@Symfony
  Using the `@Symfony <./../../ruleSets/Symfony.rst>`_ rule set will enable the ``unary_operator_spaces`` rule with the default config.
