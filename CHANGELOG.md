CHANGELOG for PHP CS Fixer
==========================

This file contains changelogs for stable releases only.

Changelog for v1.4
------------------

* feature #841 PhpdocParamsFixer: added aligning var/type annotations (GrahamCampbell)
* bug #950 Tokens::isLambda - fix detection near comments (keradus)
* bug #951 Tokens::getImportUseIndexes - fix detection near comments (keradus)
* bug #949 Tokens::isShortArray - fix detection near comments (keradus)
* bug #948 NewWithBracesFixer - fix case with multidimensional array (keradus)
* bug #945 Skip files containing __halt_compiler() on PHP 5.3 (stof)
* bug #946 BracesFixer - fix typo in exception name (keradus)
* bug #940 Tokens::setCode - apply missing transformation (keradus)
* bug #908 BracesFixer - fix invalide inserting brace for control structure without brace and lambda inside of it (keradus)
* bug #903 NoEmptyLinesAfterPhpdocsFixer - fix bug with Windows style lines (GrahamCampbell)
* bug #895 [PSR-2] Preserve blank line after control structure opening brace (marcaube)
* bug #892 Fixed the double arrow multiline whitespace fixer (GrahamCampbell)
* bug #874 BracesFixer - fix bug of removing empty lines after class' opening { (ceeram)
* bug #868 BracesFixer - fix missing braces when statement is not followed by ; (keradus)
* bug #861 Updated PhpdocParamsFixer not to change line endings (keradus, GrahamCampbell)
* bug #837 FixCommand - stop corrupting xml/json format (keradus)
* bug #846 Made phpdoc_params run after phpdoc_indent (GrahamCampbell)
* bug #834 Correctly handle tab indentation (ceeram)
* bug #822 PhpdocIndentFixer - Ignore inline docblocks (ceeram)
* bug #813 MultilineArrayTrailingCommaFixer - do not move array end to new line (keradus)
* bug #817 LowercaseConstantsFixer - ignore class' constants TRUE/FALSE/NULL (keradus)
* bug #821 JoinFunctionFixer - stop changing declaration method name (ceeram)
* minor #921 Add changelog file (keradus)
* minor #909 BracesFixerTest - no \n line in \r\n test (keradus)
* minor #864 Added NoEmptyLinesAfterPhpdocsFixer (GrahamCampbell)
* minor #871 Added missing author (GrahamCampbell)
* minor #852 Fixed the coveralls version constraint (GrahamCampbell)
* minor #863 Tweaked testRetainsNewLineCharacters (GrahamCampbell)
* minor #849 Removed old alias (GrahamCampbell)
* minor #843 integer should be int (GrahamCampbell)
* minor #830 Remove whitespace before opening tag (ceeram)
* minor #835 code grooming (keradus)
* minor #828 PhpdocIndentFixerTest - code grooming (keradus)
* minor #827 UnusedUseFixer - code grooming (keradus)
* minor #825 improve code coverage (keradus)
* minor #810 improve code coverage (keradus)
* minor #811 ShortArraySyntaxFixer - remove not needed if statement (keradus)

Changelog for v1.3
------------------

* feature #790 Add docblock indent fixer (ceeram)
* feature #771 Add JoinFunctionFixer (keradus)
* bug #798 Add DynamicVarBrace Transformer for properly handling ${$foo} syntax (keradus)
* bug #796 LowercaseConstantsFixer - rewrite to handle new test cases (keradus)
* bug #789 T_CASE is not succeeded by parentheses (dericofilho)
* minor #814 Minor improvements to the phpdoc_params fixer (GrahamCampbell)
* minor #815 Minor fixes (GrahamCampbell)
* minor #782 Cookbook on how to make a new fixer (dericofilho)
* minor #806 Fix Tokens::detectBlockType call (keradus)
* minor #758 travis - disable sudo (keradus)
* minor #808 Tokens - remove commented code (keradus)
* minor #802 Address Sensiolabs Insight's warning of code cloning. (dericofilho)
* minor #803 README.rst - fix \` into \`\` (keradus)

Changelog for v1.2
------------------

* feature #706 Remove lead slash (dericofilho)
* feature #740 Add EmptyReturnFixer (GrahamCampbell)
* bug #775 PhpClosingTagFixer - fix case with T_OPEN_TAG_WITH_ECHO (keradus)
* bug #756 Fix broken cases for AlignDoubleArrowFixer (dericofilho)
* bug #763 MethodArgumentSpaceFixer - fix receiving data in list context with omitted values (keradus)
* bug #759 Fix Tokens::isArrayMultiLine (stof, keradus)
* bug #754 LowercaseKeywordsFixer - __HALT_COMPILER must not be lowercased (keradus)
* bug #753 Fix for double arrow misalignment in deeply nested arrays. (dericofilho)
* bug #752 OrderedUseFixer should be case-insensitive (rusitschka)
* minor #779 Fixed a docblock type (GrahamCampbell)
* minor #765 Typehinting in FileCacheManager, remove unused variable in Tokens (keradus)
* minor #764 SelfUpdateCommand - get local version only if remote version was successfully obtained (keradus)
* minor #761 aling => (keradus)
* minor #757 Some minor code simplify and extra test (keradus)
* minor #713 Download php-cs-fixer.phar without sudo (michaelsauter)
* minor #742 Various Minor Improvements (GrahamCampbell)

Changelog for v1.1
------------------

* feature #749 remove the --no-progress option (replaced by the standard -v) (fabpot, keradus)
* feature #728 AlignDoubleArrowFixer - standardize whitespace after => (keradus)
* feature #647 Add DoubleArrowMultilineWhitespacesFixer (dericofilho, keradus)
* bug #746 SpacesBeforeSemicolonFixerTest - fix bug with semicolon after comment (keradus)
* bug #741 Fix caching when composer is installed in custom path (cmodijk)
* bug #725 DuplicateSemicolonFixer - fix clearing whitespace after duplicated semicolon (keradus)
* bug #730 Cache busting when fixers list changes (Seldaek)
* bug #722 Fix lint for STDIN-files (ossinkine)
* bug #715 TrailingSpacesFixer - fix bug with french UTF-8 chars (keradus)
* bug #718 Fix package name for composer cache (Seldaek)
* bug #711 correct vendor name (keradus)
* minor #745 Show progress by default and allow to disable it (keradus)
* minor #731 Add a way to disable all default filters and really provide a whitelist (Seldaek)
* minor #737 Extract tool info into new class, self-update command works now only for PHAR version (keradus)
* minor #739 fix fabbot issues (keradus)
* minor #726 update CONTRIBUTING.md for installing dependencies (keradus)
* minor #736 Fix fabbot issues (GrahamCampbell)
* minor #727 Fixed typos (pborreli)
* minor #719 Add update instructions for composer and caching docs (Seldaek)

Changelog for v1.0
------------------

First stable release.
