==========================
Rule ``align_enum_values``
==========================

Align enum values on the ``=`` operator.

Examples
--------

Example #1
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php enum MyEnum: string
    {
   -    case AB = 'ab';
   -    case C = 'c';
   +    case AB    = 'ab';
   +    case C     = 'c';
        case DEFGH = 'defgh';
    }

Example #2
~~~~~~~~~~

.. code-block:: diff

   --- Original
   +++ New
    <?php enum MyEnum: int
    {
   -    case BENJI          = 1;
   -    case ELIZABETH      = 2;
   -    case CORA           = 4;
   +    case BENJI     = 1;
   +    case ELIZABETH = 2;
   +    case CORA      = 4;
    }
