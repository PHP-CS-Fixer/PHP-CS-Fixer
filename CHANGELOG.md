CHANGELOG for PHP CS Fixer
==========================

This file contains changelogs for stable releases only.

Changelog for v1.7
------------------

* feature #1113 Added PreIncrementFixer (gharlan)
* feature #1144 Added PhpdocNoAccessFixer (GrahamCampbell)
* feature #1116 Added SelfAccessorFixer (gharlan)
* feature #1064 OperatorsSpacesFixer enhancements (gharlan)
* bug #1151 Prevent token collection corruption by fixers (stof, keradus)
* bug #1152 LintManager - fix handling of temporary file (keradus)
* bug #1139 NamespaceNoLeadingWhitespaceFixer - remove need for ctype extension (keradus)
* bug #1117 Tokens - fix iterator used with foreach by reference (keradus)
* minor #1148 code grooming (keradus)
* minor #1142 We are actually PSR-4, not PSR-0 (GrahamCampbell)
* minor #1131 Phpdocs and typos (SpacePossum)
* minor #1069 state min HHVM version (keradus)
* minor #1129 [DX] Help developers choose the right branch (SpacePossum)
* minor #1138 PhpClosingTagFixer - simplify flow, no need for loop (keradus)
* minor #1123 Reference mismatches fixed, SCA (kalessil)
* minor #1109 SingleQuoteFixer - made fixer more accurate (gharlan)
* minor #1110 code grooming (kalessil)

Changelog for v1.6.2
--------------------

* bug #1149 UnusedUseFixer - must be run before LineAfterNamespaceFixer, fix token collection corruption (keradus)
* minor #1145 AbstractLinesBeforeNamespaceFixer - fix docs for fixLinesBeforeNamespace (GrahamCampbell)

Changelog for v1.6.1
--------------------

* bug #1108 UnusedUseFixer - fix false positive when name is used as part of another namespace (gharlan)
* bug #1114 Fixed PhpdocParamsFixer with malformed doc block (gharlan)
* minor #1135 PhpdocTrimFixer - fix doc typo (localheinz)
* minor #1093 Travis - test lowest dependencies (boekkooi)

Changelog for v1.6
------------------

* feature #1089 Added NewlineAfterOpenTagFixer and BlanklineAfterOpenTagFixer (ceeram, keradus)
* feature #1090 Added TrimArraySpacesFixer (jaredh159, keradus)
* feature #1058 Added SingleQuoteFixer (gharlan)
* feature #1059 Added LongArraySyntaxFixer (gharlan)
* feature #1037 Added PhpdocScalarFixer (GrahamCampbell, keradus)
* feature #1028 Add ListCommasFixer (keradus)
* bug #1047 Utils::camelCaseToUnderscore - fix regexp (odin-delrio)
* minor #1073 ShortTagFixer enhancement (gharlan)
* minor #1079 Use LongArraySyntaxFixer for this repo (gharlan)
* minor #1070 Tokens::isMonolithicPhp - remove unused T_CLOSE_TAG search (keradus)
* minor #1049 OrderedUseFixer - grooming (keradus)

Changelog for v1.5.2
--------------------

* bug #1025 Fixer - ignore symlinks (kix)
* bug #1071 Psr0Fixer - fix bug for fixing file with long extension like .class.php (keradus)
* bug #1080 ShortTagFixer - fix false positive (gharlan)
* bug #1066 Php4ConstructorFixer - fix causing infinite recursion (mbeccati)
* bug #1056 VisibilityFixer - fix T_VAR with multiple props (localheinz, keradus)
* bug #1065 Php4ConstructorFixer - fix detection of a PHP4 parent constructor variant (mbeccati)
* bug #1060 Tokens::isShortArray: tests and bugfixes (gharlan)
* bug #1057 unused_use: fix false positive when name is only used as variable name (gharlan)

Changelog for v1.5.1
--------------------

* bug #1054 VisibilityFixer - fix var with array value assigned (localheinz, keradus)
* bug #1048 MultilineArrayTrailingCommaFixer, SingleArrayNoTrailingCommaFixer - using heredoc inside array not cousing to treat it as multiline array (keradus)
* bug #1043 PhpdocToCommentFixer - also check other control structures, besides foreach (ceeram)
* bug #1045 OrderedUseFixer - fix namespace order for trailing digits (rusitschka)
* bug #1035 PhpdocToCommentFixer - Add static as valid keyword for structural element (ceeram)
* bug #1020 BracesFixer - fix missing braces for nested if elseif else (malengrin)
* minor #1036 Added php7 to travis build (fonsecas72)
* minor #1026 Fix typo in ShortArraySyntaxFixer (tommygnr)
* minor #1024 code grooming (keradus)

Changelog for v1.5
------------------

* feature #887 Added More Phpdoc Fixers (GrahamCampbell, keradus)
* feature #1002 Add HeaderCommentFixer (ajgarlag)
* feature #974 Add EregToPregFixer (mbeccati)
* feature #970 Added Php4ConstructorFixer (mbeccati)
* feature #997 Add PhpdocToCommentFixer (ceeram, keradus)
* feature #932 Add NoBlankLinesAfterClassOpeningFixer (ceeram)
* feature #879 Add SingleBlankLineBeforeNamespaceFixer and NoBlankLinesBeforeNamespaceFixer (GrahamCampbell)
* feature #860 Add single_line_after_imports fixer (ceeram)
* minor #1014 Fixed a few file headers (GrahamCampbell)
* minor #1011 Fix HHVM as it works different than PHP (keradus)
* minor #1010 Fix invalid UTF-8 char in docs (ajgarlag)
* minor #1003 Fix header comment in php files (ajgarlag)
* minor #1005 Add Utils::calculateBitmask method (keradus)
* minor #973 Add Tokens::findSequence (mbeccati)
* minor #991 Longer explanation of how to use blacklist (bmitch, networkscraper)
* minor #972 Add case sensitive option to the tokenizer (mbeccati)
* minor #986 Add benchmark script (dericofilho)
* minor #985 Fix typo in COOKBOOK-FIXERS.md (mattleff)
* minor #978 Token - fix docs (keradus)
* minor #957 Fix Fixers methods order (GrahamCampbell)
* minor #944 Enable caching of composer downloads on Travis (stof)
* minor #941 EncodingFixer - enhance tests (keradus)
* minor #938 Psr0Fixer - remove unneded assignment (keradus)
* minor #936 FixerTest - test description consistency (keradus)
* minor #933 NoEmptyLinesAfterPhpdocsFixer - remove unneeded code, clarify description (ceeram)
* minor #934 StdinFileInfo::getFilename - Replace phpdoc with normal comment and add back empty line before return (ceeram)
* minor #927 Exclude the resources folder from coverage reports (GrahamCampbell)
* minor #926 Update Token::isGivenKind phpdoc (GrahamCampbell)
* minor #925 Improved AbstractFixerTestBase (GrahamCampbell)
* minor #922 AbstractFixerTestBase::makeTest - test if input is different than expected (keradus)
* minor #904 Refactoring Utils (GrahamCampbell)
* minor #901 Improved Readme Formatting (GrahamCampbell)
* minor #898 Tokens::getImportUseIndexes - simplify function (keradus)
* minor #897 phpunit.xml.dist - split testsuite (keradus)

Changelog for v1.4.2
--------------------

* bug #994 Fix detecting of short arrays (keradus)
* bug #995 DuplicateSemicolonFixer - ignore duplicated semicolons inside T_FOR (keradus)

Changelog for v1.4.1
--------------------

* bug #990 MultilineArrayTrailingCommaFixer - fix case with short array on return (keradus)
* bug #975 NoEmptyLinesAfterPhpdocsFixer - fix only when documentation documents sth (keradus)
* bug #976 PhpdocIndentFixer - fix error when there is a comment between docblock and next meaningful token (keradus, ceeram)

Changelog for v1.4
------------------

* feature #841 PhpdocParamsFixer: added aligning var/type annotations (GrahamCampbell)
* bug #965 Fix detection of lambda function that returns a reference (keradus)
* bug #962 PhpdocIndentFixer - fix bug when documentation is on the end of braces block (keradus)
* bug #961 Fixer - fix handling of empty file (keradus)
* bug #960 IncludeFixer - fix bug when include is part of condition statement (keradus)
* bug #954 AlignDoubleArrowFixer - fix new buggy case (keradus)
* bug #955 ParenthesisFixer - fix case with list call with trailing comma (keradus)
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
* minor #963 State the minimum version of PHPUnit in CONTRIBUTING.md (SpacePossum)
* minor #943 Improve the cookbook to use relative links (stof)
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
