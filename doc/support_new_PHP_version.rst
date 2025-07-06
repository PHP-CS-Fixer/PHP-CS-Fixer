===========================
Support for new PHP version
===========================

PHP CS Fixer is highly coupled with PHP built-in Tokenizer.
We recommend PHP CS Fixer to run on same PHP version as platform PHP version of codebase that is about to be fixed.

The fact that PHP CS Fixer can run under given PHP version doesn't mean it will properly analyse and fix codebase
written in that version. Running the Fixer in that setup can cause harm and break the code.
Due to this, we prevent running PHP CS Fixer on any PHP that we didn't explicitly declare compatibility with
(with option to opt-in into risky execution on newest PHP runtime).

Making PHP CS Fixer to support newly released PHP version may be a big task, depending on changes introduced in the
new PHP version around language syntax and internal PHP built-in Tokenizer. Tokenizer executed under different PHP
runtime may produce different result of same code source (even if code source is valid syntax under each of those runtimes).

In this document, we will use following shortcuts to refer a version:
- vX - highest supported PHP version, e.g., v8.3.x
- vY - not-yet supported PHP version, e.g., v8.4.x

Any new syntax in vY is a granted BC breaker for internal PHP Tokenizer.
Lack of new syntax in vY does not guarantee BC compatibility for internal PHP Tokenizer.

1st phase
=========

1st phase is about checking if PHP CS Fixer executed under vY runtime work properly for <= vX compatible source code.

We do it by adding vY to our own test matrix.

This does not guarantee that PHP CS Fixer will work properly for vY source code, but it's a good start to prevent our own code incompatibilities or runtime.

2nd phase - compatibility support
=================================

2nd phase is about teaching PHP CS Fixer to not only run under vY runtime, but also to understand vY syntax.

Here, we want to first understand explicit or implicit Tokenizer changes:
- explicit change is when new PHP token is introduced - in such case we cover it under `Forward Compatibility Tokens <./../src/Tokenizer/FCT.php>`_ to simplify cross-php-version handling.
- implicit change is when PHP token is now having more possible contexts - in such case we separate it with dedicated `Custom Tokens <./../src/Tokenizer/CT.php>`_. Doing it after declaring the official support for vY would be a BC breaker.

Then, we want to ensure compatibility. With high amount of rules, it's not-likely to check every single combination of rules and vY syntax, so we focus to run newly introduced syntax as integration test against ruleset of "@PhpCsFixer: true, PHPvYMigration:true" (`example <./../tests/Fixtures/Integration/php_compat/>`_).

On that moment, we claim initial support for vY is completed and PHP CS Fixer can run on vY runtime as-is.
Any newly discovered incompatibility should be reported as a bug.

3rd phase - Coding Standards support
====================================

Having initial support for vY only means that PHP CS Fixer can run on vY runtime and understand vY syntax.
It does not provide any explicit handling on style around vY syntax - that can be added on any moment later,
and it's never-ending, open-ended process.
