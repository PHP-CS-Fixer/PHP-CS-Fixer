CHANGELOG for PHP CS Fixer
==========================

This file contains changelogs for stable releases only.

Changelog for v3.92.0
---------------------

* feat: exception for rules via `@php-cs-fixer-ignore` annotation (#9280)
* feat: exception for rules via `Rule Customisation Policy` (#9107)
* feat: `PhpUnitTestCaseStaticMethodCallsFixer` - add handling of `getStubBuilder` (#9276)
* feat: `PhpUnitTestCaseStaticMethodCallsFixer` - add `target` option (#8498)
* chore: '.php-cs-fixer.dist.php' - remove no longer needed rule, 'expectedDeprecation' annotation does not exist for long time (#9266)
* chore: fix `arguments.count` error from PHPStan (#9258)
* chore: fix `generator.valueType` error from PHPStan (#9259)
* chore: fix `plus.*NonNumeric` errors from PHPStan (#9260)
* chore: Runner - better input types handling (#9286)
* chore: watch out for non-final classes (#9262)
* chore: `Config::getFinder()` - fix typehinting (#9288)
* deps: bump alpine from 3.22 to 3.23 (#9269)
* deps: bump phpcompatibility/php-compatibility from 10.0.0-alpha1 to 10.0.0-alpha2 in /dev-tools (#9271)
* deps: bump the phpstan group in /dev-tools with 3 updates (#9270)
* deps: dev-tools - upgrade deep deps (#9272)
* docs: exception for rules in dedicated doc files, for easier discoverability (#9281)
* docs: upgrade docs to not use legacy set (#9282)
* DX: make all `testFix*` methods have first parameter `$expected` (#9289)
* refactor: fix violation of 'no_useless_concat_operator' (#9267)
* refactor: `ProjectCodeTest` - refactor `testExpectedInputOrder` test (#9290)
* Revert (partially) "feat: Finder to find dot-files by default in v4/future-mode (#9187)" (#9287)
* test: RuleSetTest - check against non-deprecated variant of PHPUnit sets (#9265)
* UX: exception for rules via `Rule Customisation Policy` - better error message for wrong customisers (#9279)
* UX: exception for rules via `Rule Customisation Policy` - prevent policy without version (#9278)

Changelog for v3.91.3
---------------------

* Revert "feat: Symfony - add stringable_for_to_string to set" (#9268)

Changelog for v3.91.2
---------------------

* fix: fix support call-site generic variance (#9253)
* chore: adjust autoloader to exclude all Internal classes in classmap (#9252)
* CI: set `PHP_CS_FIXER_IGNORE_ENV` for PHP 8.6 (#9254)

Changelog for v3.91.1
---------------------

* UX: show warning on PHP-mismatch only for 'fix' and 'check' commands (#9243)
* docs: unify rule/ruleset doc tags (#9250)
* refactor: don't expose internal commands/rules (#9245)
* refactor: memoize fixer names (#9219)
* refactor: use custom set for internal rules (#9247)
* test: show that `describe` command works well for custom sets (#9246)

Changelog for v3.91.0
---------------------

* UX: init command (#9215)
* feat: PHP 8.5 compatibility support (#9234)
* feat: Add `StringableForToStringFixer` (#9218)
* feat: support call-site generic variance (#9212)
* feat: Symfony - add stringable_for_to_string to set (#9235)
* chore: do not mark NamespaceAnalysis as internal, because it's part of public API interface (#9193)
* chore: ExampleRuleset - improve test fixture name (#9214)
* chore: traits - require interfaces or base classes (#9086)
* CI: Add PHPStan rule to validate public API surface completeness (#9196)
* CI: auto-select PHP_MAX for special jobs (#9231)
* CI: drop duplicated PHP 8.4 jobs (#9229)
* CI: fix SCA after Symfony 7.4.0 release (#9226)
* CI: no more need for solving PHP 8.4 deprecations in `/vendor/` anymore (#9230)
* CI: switch trivial jobs to ubuntu-slim (#9232)
* CI: upgrade `.php-cs-fixer.php-highest.php` to fully reflect PHP 8.4 (#9233)
* deps: bump actions/checkout from 5 to 6 (#9210)
* deps: bump crate-ci/typos from 1.39.2 to 1.40.0 (#9237)
* deps: bump shipmonk/composer-dependency-analyser from 1.8.3 to 1.8.4 in /dev-tools (#9238)
* deps: bump shipmonk/dead-code-detector from 0.13.5 to 0.14.0 in /dev-tools (#9239)
* deps: bump Symfony v8 to RC (#9221)
* deps: bump Symfony v8 to stable (#9222)
* deps: update xdebug (#9228)
* fix: move config template to non-excluded folder (#9241)
* fix: `DeclareStrictTypesFixer` - do not duplicate `strict_types` if it is already present, with other directive (#9225)
* fix: `FullyQualifiedStrictTypesFixer` - fix crash on `T_OPEN_TAG_WITH_ECHO` (#9217)
* test: allow CI to define both, numeric and non-numeric PHP versions/builds (#9236)
* test: do not allow to fail PHP 8.5 job (#9224)
* test: let's not dance on the edge of the JIT stack limit (#9211)
* test: run smoke tests on any OS (#9242)
* tests: update PHP 8.5 compatibility test (#9223)

Changelog for v3.90.0
---------------------

* feat: always have `@PHPXxYMigration(:risky)` sets for supported PHP versions (#9207)
* feat: Finder to find dot-files by default in v4/future-mode (#9187)
* fix: manage the space between operator and version on Composer JSON reader (#9203)
* fix: PhpdocAnnotationWithoutDotFixer lowercases `@param` type when description starts with all-uppercase word (#9169)
* UX: groom warning of PHP mismatch for runtime vs target project minimum supported version (#9202)
* UX: Rules override warning (#9188)
* UX: `describe` command - allow to visualize Set as tree (#9179)
* docs: rework `.php-cs-fixer.php` local config file docs (#9185)
* docs: update Set descriptions (#9183)
* chore: Declare support for Symfony 8 (#9172)
* chore: add missing v4 TODOs (#9197)
* chore: mark one remaining Analyzer to become internal (#9194)
* chore: mark v2 leftovers with v4 TODO (#9181)
* chore: no need for deprecation trigger in internal DataProviderAnalysis (#9192)
* deps: bump crate-ci/typos from 1.38.1 to 1.39.0 (#9175)
* deps: bump crate-ci/typos from 1.39.0 to 1.39.2 (#9205)
* deps: bump the phpstan group in /dev-tools with 2 updates (#9204)
* DX: different name for special configs (#9180)
* DX: improve types for `testInvalidConfiguration` methods (#9206)
* DX: `describe` command - allow to expose rules without sets via `@-` alias (#9178)
* refactor: move assertions from `tearDown` into `assertPostConditions` to fix `Test code or tested code did not remove its own error handlers` warning (#9182)
* refactor: prevent tests to use actual repository `.php-cs-fixer.dist.php` file (#9177)
* test: add more test(s) for ComposerJsonReader (#9200)
* test: ensure calling parent method from hook-alike methods (#9184)

Changelog for v3.89.2
---------------------

* fix: `NoUnneededBracesFixer` - do not crash on multiline group import (#9160)
* chore: Standardize repository to use British English spelling (#9154)
* CI: ensure enforced Symfony version is installed (#9174)
* deps: bump php-coveralls to v2.9 (#9173)
* refactor: FixerDocumentGenerator::getSetsOfRule - cache resolved sets (#9170)
* refactor: FixerDocumentGenerator::getSetsOfRule - use cache (#9171)

Changelog for v3.89.1
---------------------

* fix: ComposerJsonReader - fix handling asterisk (#9166)
* docs: cookbook - update to mention custom fixer option (#9164)
* DX: add links in `cookbook_fixers.rst` (#9162)
* deps: upgrade `phpcompatibility/*` to alpha of next MAJOR instead of using dev branch, plus other minor upgrades (#9163)

Changelog for v3.89.0
---------------------

* feat: autofix "no-return" phpdoc type to "never" (#9073)
* feat: support keeping strict_types=0 in DeclareStrictTypesFixer (#9121)
* feat: `NoUnneededBracesFixer` - remove unneeded braces from imports (#9118)
* fix: `FullyQualifiedStrictTypesFixer` - replacing the real FQCN with a non-existent one (#8962)
* fix: `PhpdocToCommentFixer` on property hooks (#9123)
* fix: `PhpUnitMethodCasingFixer` to not cause a duplicate method declaration (#9124)
* fix: `StaticLambdaFixer` - do not make function static when it contains anonymous class having `$this` in the constructor (#9088)
* fix: `UseArrowFunctionsFixer` - do not produce two consecutive whitespace tokens (#9151)
* UX: better gitlab reporting - add content field (#9152)
* UX: better gitlab reporting - more user-friendly description field (#9141)
* UX: better gitlab reporting for location.lines (#9149)
* UX: Warn if executed php version is higher than the minimum php version defined in composer.json (#9134)
* chore: add UX title pattern (#9132)
* chore: explicitly use php interpreter for paraunit (#9126)
* chore: extend composer scripts (#9127)
* chore: fix shellcheck installation for Darwin (#9125)
* chore: replace PHPDocs with `assert` calls (#9144)
* deps: bump crate-ci/typos from 1.36.3 to 1.38.1 (#9136)
* deps: bump phpstan/phpstan from 2.1.29 to 2.1.31 in /dev-tools in the phpstan group (#9129)
* deps: bump shipmonk/dead-code-detector from 0.13.3 to 0.13.4 in /dev-tools (#9135)
* deps: bump shipmonk/dead-code-detector from 0.13.4 to 0.13.5 in /dev-tools (#9146)
* deps: bump the phpstan group across 1 directory with 2 updates (#9101)
* deps: use `shipmonk/composer-dependency-analyser` instead of `icanhazstring/composer-unused` and `maglnet/composer-require-checker` (#9106)
* docs: fix docs for `@autoPHPUnitMigration:risky`set (#9147)
* docs: improve descriptions for `NoTrailingWhitespace*` fixers (#9117)
* docs: more explicit docs on --rules (#9114)
* docs: update usage documentation for describe `--expand` and `@` (#9119)
* DX: Fix `composer qa` script (#9138)
* refactor: `--option value` => `--option=value` (#9131)
* test: Fix result randomness in `RunnerTest` for MacOS (#9139)

Changelog for v3.88.2
---------------------

* docs: describe command - allow to describe config in use (#9103)
* docs: describe command - allow to expand sets (#9104)

Changelog for v3.88.1
---------------------

* chore: use accidentally missing `@auto:risky` (#9102)
* deps: bump crate-ci/typos from 1.36.2 to 1.36.3 (#9099)
* deps: bump icanhazstring/composer-unused from 0.9.4 to 0.9.5 in /dev-tools (#9100)
* DX: Ability to run `yamllint` locally (#9093)

Changelog for v3.88.0
---------------------

* feat: Support custom rulesets (#6083)
* feat: introduce `@auto`, `@auto:risky` sets (#9090)
* feat: introduce `@autoPHPMigration`, `@autoPHPMigration:risky`, `@autoPHPUnitMigration:risky` sets (#9068)
* feat: start using new defaults for rules under future mode (#9020)
* feat: rename rule sets from `MAJORMINOR` and `MAJOR.MINOR` into `MAJORxMINOR` (#9005)
* feat: rename visibility_required into modifier_keywords (#8995)
* feat: `symfony` ruleset: Add `@const` to `phpdoc_no_alias_tag` (#9016)
* fix: `BlankLinesBeforeNamespaceFixer` - handle namespace without newline before (#9059)
* docs: fix typo (#9060)
* docs: update CONTRIBUTING.md (#9074)
* docs: update README for updated PHP/PHPUnit set names (#9070)
* DX: Allow development on PHP 8.5 (#9092)
* chore: enforce list via `array_values()` (#9054)
* chore: ErrorOutput - line is optional (#9047)
* chore: fix cs in entry point file (#9064)
* chore: fix CS, somehow it missed the CI of `.php-cs-fixer.well-defined-arrays.php` (#8987)
* chore: fix some `argument.type` errors (#9056)
* chore: groom Automatic rulesets code (#9091)
* chore: phpstan - do not ignore all `internal` usage errors (#9063)
* chore: replace wrong `class-string` usages (#8968)
* chore: `getcwd()` - ignore `false` return value (#9057)
* chore: `ReporterFactory` - use `class-string` type (#9055)
* CI: check for typos (#9048)
* CI: don't prevent the build when remote coverage reporting service is down (#9095)
* CI: fix smoke tests after #9005 (#9061)
* CI: fix typos in `CHANGELOG.md` (#9052)
* CI: mutation tests - disable github annotations (#9062)
* CI: Test docs generation only once per CI pipeline (#9089)
* CI: `push` event only for master branch (#9046)
* CI: `push` event only for master branch (#9050)
* deps: bump box version (#9042)
* deps: bump ergebnis/composer-normalize from 2.48.1 to 2.48.2 in /dev-tools (#9045)
* deps: bump phpstan/phpstan from 2.1.25 to 2.1.28 in /dev-tools in the phpstan group (#9072)
* deps: unify alpine version for PHP 8.4 (#9077)
* deps: update phpstan and phpstan-baseline-per-identifier (#9051)
* deps: update squizlabs/php_codesniffer to v4.0 and other related packages (#9075)
* deps: upgrade alpine wherever possible (#9078)
* deps: upgrade infection/infection to ^0.31.0 (#9079)
* refactor: introduce concept of AutomaticRuleSet (#9067)
* refactor: rename `RuleSetDescriptionInterface` into `RuleSetDefinitionInterface` (#9094)
* test: ensure alpine version same between Dockerfile and compose.yaml (#9076)
* test: ensure alpine version same in compose.yaml and release.yml (#9071)
* test: extend tests to cover new migration rule set names (#9069)
* test: improve testing that objects cannot be serialized/unserialized (#9049)

Changelog for v3.87.2
---------------------

* fix: `phpdoc_to_param_type`, `phpdoc_to_return_type`, `phpdoc_to_property_type` - handle type aliasing before handling PHP version (#9031)
* docs: unify docs around deprecated sets (#9036)
* chore: fix typos (#9022)
* chore: provide few missing types for callbacks (#9026)
* deps: bump actions/stale from 9 to 10 (#9029)
* deps: pin checkbashisms and shellcheck to stable URL with pinned version (#9032)
* DX: suggest `--sequential` when facing fixing error (#9023)
* refactor: Future - centralize class for future-looking logic (#9021)
* test: Mark `DocumentationCommandTest::testGeneratingDocumentation()` as large test (#9028)

Changelog for v3.87.1
---------------------

* chore: `AbstractProxyFixer` - require non-empty list of proxy fixers (#9010)
* deps: update justinrainbow/json-schema (#9019)

Changelog for v3.87.0
---------------------

* feat: add `PhpdocTagNoNamedArgumentsFixer` (#8906)
* feat: deprecate using config from passed 'path' CLI argument (#8923)
* feat: introduce `@PHP85Migration` set (#8941)
* feat: upgrade `@PhpCsFixer` set towards new defaults of selected rules (#8981)
* fix: `PhpdocOrderFixer` - do not allow duplicated tags in configuration (#8945)
* fix: `PhpdocOrderFixer` when `phpstan-` / `psalm-` order is specified (#8853)
* docs: README.md updates (#9013)
* docs: update README.md (#9015)
* docs: update `README.md` and `CONTRIBUTING.md` (#8974)
* DX: remove methods for kind checks (#8954)
* DX: unify class/interface/trait naming (#8957)
* chore: Add CS commit to .git-blame-ignore-revs
* chore: Add TODO for ENUM conversion in ProgressOutputType (#8991)
* chore: always use `JSON_THROW_ON_ERROR` (#8993)
* chore: apply (partially) `multiline_string_to_heredoc` (#9002)
* chore: apply phpdoc_tag_no_named_arguments (#8969)
* chore: configure phpdoc_tag_no_named_arguments (#8970)
* chore: convert private methods to constants (#8976)
* chore: deprecate `Annotation::getTagsWithTypes` in favour of `TAGS_WITH_TYPES` constant (#8977)
* chore: exclude files in .gitattributes (#8960)
* chore: extract token types for PHPStan (#8925)
* chore: handle fixer options without allowed types defined (#8973)
* chore: ignore deprecations in `token_get_all` (#8963)
* chore: minor CS fixes (#8979)
* chore: PhpdocTagNoNamedArgumentsFixer - better example (#8971)
* chore: PHPUnit - enforce no memory_limit (#8940)
* chore: remove not-needed reset-call (#9014)
* chore: revert wrong `_PhpTokenArray` usages (#8947)
* chore: rework `ci-integration.sh` (#8989)
* chore: sort .gitattributes (#8966)
* chore: unify entries in `.gitignore` (#8982)
* chore: unify env var `FAST_LINT_TEST_CASES` into `PHP_CS_FIXER_FAST_LINT_TEST_CASES` (#8992)
* chore: use `get_debug_type` and `::class` in exception messages (#9006)
* chore: use `non-empty-list` where appropriate (#8972)
* chore: `AbstractPhpdocTypesFixer` - remove `$tags` property (#8980)
* chore: `CheckCommand` - use regex instead of `explode` & `substr` (#8936)
* chore: `ClassAttributesSeparationFixer` - improve code (#8950)
* chore: `EregToPregFixer` - use constant instead of static property (#8978)
* chore: `FullyQualifiedStrictTypesFixer` - ensure matching number of opening/closing braces (#9009)
* chore: `OrderedClassElementsFixer` - use private method instead of anonymous function (#8931)
* chore: `PhpdocParamOrderFixer` - improve/simplify code (#9012)
* chore: `PhpUnitDedicateAssertFixer` - improve code for phpstan (#9011)
* chore: `Tokens::offsetSet` - explicit validation of input (#9004)
* chore: `Tokens` - override nullability of `SplFixedArray` (#9001)
* refactor: add `DocBlockAnnotation` trait (#8953)
* refactor: `PhpdocOrderFixer` - calculate order only once (#8944)
* CI: add `vendor/bin/phpunit --check-php-configuration` (#8934)
* CI: more self-fixing checks on lowest/highest PHP (#8943)
* CI: Re-enable Paraunit in CI under PHP 8.5 (#8964)
* CI: update checkbashisms (#8951)
* test: extend ProjectCodeTest to check classy names in tests too (#8959)
* test: split test so more of them can run under Windows (#8986)
* test: TypeDeclarationSpacesFixer - demonstrate PHP 8.3 related config doesn't harm older envs (#8999)
* test: update pipe operator tests after pipe & arrow function combination was prohibited (#8990)
* test: `NoUnneededControlParenthesesFixer` - add tests for "clone with" (#8937)
* test: `PhpdocNoAliasTagFixerTest` - add test for `@const` to `@var` (#8998)
* CS: re-apply rules (#8988)
* deps: bump actions/checkout from 4 to 5 (#8938)
* deps: bump shipmonk/dead-code-detector from 0.13.1 to 0.13.2 in /dev-tools (#8939)
* deps: drop support for justinrainbow/json-schema:^5 (#8984)
* deps: minor upgrades (#8983)
* deps: remove `php-cs-fixer/accessible-object` (#8948)
* deps: update dev-tools (#9007)

Changelog for v3.86.0
---------------------

* feat: console application - add completion support (#8887)
* feat: introduce `PER-CS3.0` rulsets (#8841)
* feat: update `@Symfony` and `@PhpCsFixer` sets (#8930)
* feat: `NoSuperfluousPhpdocTagsFixer` - support multiline array shapes (#8903)
* fix: PhpdocToParamTypeFixerTest - handle types_map for union-types (#8926)
* chore: AbstractTransformerTestCase - better virtual type naming (#8927)
* chore: add TODO for v4 (#8912)
* chore: do not call `Reflection*::setAccessible()` in PHP >= 8.1 (#8907)
* chore: document .env.example (#8901)
* chore: fix `@todo` annotation casing (#8921)
* chore: switch to official checkstyle.xsd (#8910)
* chore: unify future default of after_heredoc (#8924)
* chore: `@phpstan-ignore` for php version check (#8920)
* chore: `PhpUnitTestAnnotationFixer` - use `foreach` instead of `for` (#8911)
* CI: allow PHP 8.5 for failure, till it's officially released and we get the stable support (#8928)
* ci: run tests under 8.5 via PHPUnit, as ParaUnit failing (#8933)
* CI: temporarily skip problematic tests under Sf v8 (#8919)
* deps: bump icanhazstring/composer-unused from 0.9.3 to 0.9.4 in /dev-tools (#8905)
* deps: update and bump dev-tools/composer (#8915)
* docs: extend FullOpeningTagFixer samples (#8913)
* docs: extend OperatorLinebreakFixer samples (#8914)
* docs: more explicit msg for stop using deprecated code (#8922)
* DX: .gitignore - ignore php-cs-fixer.cache, as it's existence is possible when using env var (#8929)

Changelog for v3.85.1
---------------------

* chore: update legacy deps (#8902)

Changelog for v3.85.0
---------------------

* feat: `ArrayIndentationFixer` - handle closures inside attributes (#8888)
* feat: `NullableTypeDeclarationFixer` - support final promoted properties (#8885)
* feat: `OperatorLinebreakFixer` - support pipe operator (#8891)
* feat: `PhpdocTypesFixer` - support multiline array shapes (#8893)
* fix: always reach 100% of checked files (#8861)
* fix: `BracesPositionFixer` - handle property hooks correctly (#8886)
* fix: `NullableTypeDeclarationFixer` - handle abstract and final properties (#8876)
* fix: `PhpdocLineSpanFixer` - handle promoted properties (#8897)
* fix: `PhpUnitDataProviderNameFixer` - fix for multiple providers defined by attributes for one test method (#8849)
* fix: `TernaryOperatorSpacesFixer` - handle `instanceof static` (#8879)
* fix: `TypeDeclarationSpacesFixer` - handle asymmetric visibility and final properties (#8900)
* chore: add initial PHP 8.5 integration test (#8871)
* chore: add tests for public API methods (#8895)
* chore: apply changes from `PhpdocTypesFixer` for multiline array shapes (#8894)
* chore: baseline files without error count (#8870)
* chore: cleanup `PhpCsFixer\DocBlock\Annotation` (#8892)
* chore: Command name/descriptions - use attribute and static properties (#8862)
* chore: Commands - unify way to define help content (#8881)
* chore: ConfigurationResolver - add few missing v4 TODOs (#8882)
* chore: fix `booleanNot.exprNotBoolean` errors (#8869)
* chore: fix/optimize some phpdocs (#8889)
* chore: more unification of PHP CS Fixer naming (#8854)
* chore: PHPStan baseline - use `shipmonk/phpstan-baseline-per-identifier` (#8844)
* chore: remove dead code (#8896)
* chore: remove unused constants (#8864)
* chore: remove useless `@var` annotations (#8867)
* chore: simplify `ClassDefinitionFixer` (#8868)
* chore: unify usage of image versions to not mention minor (#8860)
* chore: update PHPStan (#8898)
* chore: update PHPStan extension for `Preg::match` (#8872)
* chore: wrong return values in `ErrorsManager` (#8863)
* chore: `OrderedInterfacesFixer` - make code more PHPStan friendly (#8866)
* chore: `Token` - add `@phpstan-assert-if-true` (#8865)
* deps: bump phpstan/phpstan from 2.1.17 to 2.1.18 in /dev-tools in the phpstan group (#8859)
* deps: bump the phpstan group in /dev-tools with 4 updates (#8890)
* docs: better document deprecated rule sets (#8878)
* docs: cleanup docs for PHP 7 (#8873)
* docs: cleanup docs for PHP < 7.4 (#8855)
* docs: ListSetsCommand,FixCommand - document possible formats in Command's definitions (#8880)
* DX: Explicitly prevent touching non-monolithic files (#6517)

Changelog for v3.84.0
---------------------

* feat: Introduce `NoUselessPrintfFixer` (#8820)
* feat: `CastSpacesFixer` - support `(void)` cast (#8851)
* feat: `NewExpressionParenthesesFixer` - add to `PHP84MigrationSet` (#8788)
* feat: `VisibilityRequiredFixer` - support final promoted properties (#8835)
* fix: `PhpdocToParamTypeFixer` - do not convert types from `phpstan-type`, `phpstan-import-type`, `psalm-type` and `psalm-import-type` (#8842)
* fix: `PhpdocToPropertyTypeFixer` - do not convert types from `phpstan-type`, `phpstan-import-type`, `psalm-type` and `psalm-import-type` (#8847)
* fix: `PhpdocToReturnTypeFixer` - do not convert types from `phpstan-type`, `phpstan-import-type`, `psalm-type` and `psalm-import-type` (#8846)
* chore: introduce FCT for few missing tokens (#8845)
* chore: remove useless static variables for const content (#8836)
* chore: simplify `isGivenKind`/`equals`/`equalsAll` calls (#8843)
* docs: Support for new PHP version (#8780)

Changelog for v3.83.0
---------------------

* feat: Suppress enable parallel runner message when only 1 core is available (#8833)
* fix: BracesPositionFixer - fix performance issue for massive files with CT::T_CURLY_CLOSE (#8830)
* fix: `NoUnreachableDefaultArgumentValueFixer` - do not crash on property hook (#8838)
* chore: Add CS commit to .git-blame-ignore-revs
* chore: apply native_constant_invocation for build-in consts (#8837)
* chore: configure native_constant_invocation (#8840)
* chore: early compat check with Symfony 8 (#8827)
* chore: `FullyQualifiedStrictTypesFixer` - reduce function calls (#8834)
* CI: mark jobs allow-to-fail declarative, instead of imperative check (#8829)

Changelog for v3.82.2
---------------------

* fix: `ClassAttributesSeparationFixer` - handle method `get` aliased in trait import (#8822)
* fix: `LowercaseStaticReferenceFixer` - do not touch enum's cases (#8824)
* fix: `StatementIndentationFixer` - multi constant statement containing array (#8825)
* fix: `VisibilityRequiredFixer` - handle promoted property with visibility and reference, but without type (#8823)

Changelog for v3.82.1
---------------------

* fix: `OrderedClassElementsFixer` - handle property hooks (#8817)
* fix: `SingleClassElementPerStatementFixer` - fix for property hooks (#8816)

Changelog for v3.82.0
---------------------

* chore: cleanup `FCTTest` (#8808)
* chore: PhpdocOrderFixer.php -  CPU optimization (#8812)
* deps: update box (#8795)
* docs: fix whitespace around code examples and reference sections in rules' docs (#8810)
* feat: `MagicConstantCasingFixer` - support `__PROPERTY__` (#8809)
* feat: `PhpUnitDataProviderNameFixer` - support data providers defined by both annotation and attribute for the same test (#8811)
* fix: `PhpdocToParamTypeFixer` - always handle reference in PHPDoc (#8813)

Changelog for v3.81.0
---------------------

* feat: `VisibilityRequiredFixer` - handle more than only the first promoted property (#8801)
* fix: `BracesPositionFixer` - do not crash when variable is terminated by PHP close tag (#8804)
* fix: `PhpUnitDataProviderMethodOrderFixer` - handle data provider defined by both annotation and attribute (#8805)
* fix: `PhpUnitInternalClassFixer` - skip adding `@internal` on instantiation of anonymous classes (#8807)
* fix: `VisibilityRequiredFixer` - handle promoted parameter passed by reference (#8799)
* chore: add automatically to milestone "PHP 8.5 initial compatibility" if label is "topic/PHP8.5" (#8806)
* chore: fail "Deployment checks" if any job from `tests` failed (#8792)
* docs: update docs about --allow-unsupported-php-version (#8796)

Changelog for v3.80.0
---------------------

* feat: PHP 8.4 compatibility support (#8300)

Changelog for v3.79.0
---------------------

* chore: `VisibilityRequiredFixerTest` - run tests in correct PHP version (#8790)
* feat: `BracesPositionFixer` - do not touch one-line properties with hooks (#8785)
* feat: `PhpUnitDataProvider(.+)Fixer` - support attributes (#8197)

Changelog for v3.78.1
---------------------

* fix: `VisibilityRequiredFixer` - do not add `public` incorrectly (#8787)

Changelog for v3.78.0
---------------------

* chore: `VisibilityRequiredFixer` - document behaviour for asymmetric visibility with only set-visibility (#8783)
* feat: `BracesPositionFixer` - support property hooks (#8782)
* feat: `VisibilityRequiredFixer` - support promoted property without visibility (#8773)
* fix: `NativeFunctionInvocationFixer` - fix global function `set` called in array key (#8568)
* fix: `NoBreakCommentFixer` - handle function having return type (#8767)
* fix: `StatementIndentationFixer` - handle functions `set` and `get` (like property hooks, but not) (#8576)
* fix: `StaticPrivateMethodFixer` - handle nested calls (#8768)

Changelog for v3.77.0
---------------------

* chore: add automatically to milestone "PHP 8.4 initial compatibility" if label is "topic/PHP8.4" (#8770)
* chore: Add CS commit to .git-blame-ignore-revs
* chore: fix adding automatically to milestone "PHP 8.4 initial compatibility" if label is "topic/PHP8.4" (#8775)
* chore: fix adding automatically to milestone "PHP 8.4 initial compatibility" if label is "topic/PHP8.4" (#8776)
* chore: move all indicators to analyzers (#8772)
* chore: move PHP-compat integration tests (#8781)
* chore: partially apply NoExtraBlankLinesFixer:tokens.comma (#8762)
* chore: reconfigure phpdoc_order in local config (#8220)
* feat: PhpdocOrderFixer - extend support for phpstan and psalm annotations (#8777)
* feat: support anonymous classes extending `TestCase` in PHPUnit fixers (#8707)
* feat: `CommentToPhpdocFixer` and `PhpdocToCommentFixer` - support asymmetric visibility (#8774)
* feat: `NoEmptyStatementFixer` - support abstract property hooks (#8766)
* feat: `NullableTypeDeclarationForDefaultNullValueFixer` - support asymmetric visibility in the constructor (#8604)
* feat: `ProtectedToPrivateFixer` - add support for promoted properties (#8608)
* fix: `PhpUnitAttributesFixer` - correctly remove annotations when configured `['keep_annotations' => false]` (#8577)
* fix: `ProtectedToPrivateFixer` - fix asymmetric visibility with only set visibility (#8763)

Changelog for v3.76.0
---------------------

* chore(release): bump php ci alpine version (#8581)
* chore: add missing priority test for `BracesPositionFixer` and `MultilinePromotedPropertiesFixer` (#8596)
* chore: add more assertions in tests (#8740)
* chore: bump dependencies version to maximum for non-newest MAJOR version (#8753)
* chore: cast types in tests (#8742)
* chore: cleanup booleans use in tests (#8738)
* chore: cleanup code in tests (#8745)
* chore: cleanup PsrAutoloadingFixerTest.php keywords handling, as always defined currently (#8730)
* chore: cleanup `AlignMultilineCommentFixerTest` (#8688)
* chore: cleanup `BinaryOperatorSpacesFixerTest` (#8687)
* chore: cleanup `BlankLineBeforeStatementFixerTest` (#8685)
* chore: cleanup `ClassDefinitionFixerTest` (#8684)
* chore: cleanup `ClassDefinitionFixer` (#8580)
* chore: cleanup `ConcatSpaceFixerTest` (#8683)
* chore: cleanup `DoctrineAnnotationArrayAssignmentFixerTest` (#8621)
* chore: cleanup `DoctrineAnnotationBracesFixerTest` (#8623)
* chore: cleanup `DoctrineAnnotationIndentationFixerTest` (#8620)
* chore: cleanup `DoctrineAnnotationSpacesFixerTest` (#8624)
* chore: cleanup `EchoTagSyntaxFixerTest` (#8681)
* chore: cleanup `file_get_contents` return types (#8735)
* chore: cleanup `FunctionDeclarationFixerTest` (#8680)
* chore: cleanup `FunctionToConstantFixerTest` (#8618)
* chore: cleanup `GeneralPhpdocTagRenameFixerTest` (#8627)
* chore: cleanup `GlobalNamespaceImportFixerTest` (#8679)
* chore: cleanup `HeaderCommentFixerTest` (#8677)
* chore: cleanup `HeaderCommentFixerTest` (restore `@requires` for PHP 8.1) (#8678)
* chore: cleanup `IncrementStyleFixerTest` (#8676)
* chore: cleanup `IndentationTypeFixerTest` (#8675)
* chore: cleanup `ListSyntaxFixerTest` (#8674)
* chore: cleanup `MethodArgumentSpaceFixerTest` (#8673)
* chore: cleanup `MultilineWhitespaceBeforeSemicolonsFixerTest` (#8614)
* chore: cleanup `NativeConstantInvocationFixerTest` (#8672)
* chore: cleanup `NewWithParenthesesFixerTest` (#8592)
* chore: cleanup `NoBlankLinesAfterPhpdocFixerTest` (#8671)
* chore: cleanup `NoBreakCommentFixerTest` (#8670)
* chore: cleanup `NoClosingTagFixerTest` (#8669)
* chore: cleanup `NoEmptyStatementFixerTest` (#8667)
* chore: cleanup `NoSpacesAroundOffsetFixerTest` (#8666)
* chore: cleanup `NoUnneededControlParenthesesFixerTest` (#8665)
* chore: cleanup `NoUselessElseFixerTest` (#8664)
* chore: cleanup `PhpdocAddMissingParamAnnotationFixerTest` (#8663)
* chore: cleanup `PhpdocNoEmptyReturnFixerTest` (#8662)
* chore: cleanup `PhpdocNoPackageFixerTest` (#8626)
* chore: cleanup `PhpdocOrderByValueFixerTest` (#8661)
* chore: cleanup `PhpdocOrderFixerTest` (#8660)
* chore: cleanup `PhpdocParamOrderFixerTest` (#8659)
* chore: cleanup `PhpdocReturnSelfReferenceFixerTest` (#8658)
* chore: cleanup `PhpdocSeparationFixerTest` (#8657)
* chore: cleanup `PhpdocSummaryFixerTest` (#8654)
* chore: cleanup `PhpdocTrimFixerTest` (#8653)
* chore: cleanup `PhpdocTypesOrderFixerTest` (#8652)
* chore: cleanup `PhpdocVarWithoutNameFixerTest` (#8617)
* chore: cleanup `PhpUnitConstructFixerTest` (#8651)
* chore: cleanup `PhpUnitDedicateAssertFixerTest` (#8650)
* chore: cleanup `PhpUnitTestCaseStaticMethodCallsFixerTest` (#8649)
* chore: cleanup `Preg` issues (#8720)
* chore: cleanup `ReturnAssignmentFixerTest` (#8648)
* chore: cleanup `ReturnTypeDeclarationFixerTest` (#8647)
* chore: cleanup `SingleImportPerStatementFixerTest` (#8645)
* chore: cleanup `SingleLineCommentStyleFixerTest` (#8644)
* chore: cleanup `SingleSpaceAroundConstructFixerTest` (#8642)
* chore: cleanup `SpaceAfterSemicolonFixerTest` (#8625)
* chore: cleanup `SpacesInsideParenthesesFixerTest` (#8641)
* chore: cleanup `StatementIndentationFixerTest` (#8640)
* chore: cleanup `YodaStyleFixerTest` (#8638)
* chore: do not check if `JSON_INVALID_UTF8_IGNORE` is defined because it always is (since PHP 7.2) (#8709)
* chore: handle saveXML failures explicitly (#8755)
* chore: ignore remaining PHPStan (false positive) issues (#8746)
* chore: improve PHPDoc's types in (#8741)
* chore: improve PHPDocs in tests (#8736)
* chore: improve PHPDocs in tests (#8744)
* chore: improve type of `Token::equalsAny` (#8743)
* chore: lint yaml files (#8622)
* chore: MultilinePromotedPropertiesFixer - mark new fixer introduced in #8595 as experimental (#8758)
* chore: PHPMD - cleanup `UnusedLocalVariable` for `foreach` (#8637)
* chore: refactor FCT (#8714)
* chore: remove unused local variables in src (#8600)
* chore: remove unused local variables in tests (#8599)
* chore: remove useless test from `AliasedFixerOptionTest` (#8739)
* chore: remove `defined` calls from tests (#8708)
* chore: restore original type of Token::equalsAny, partially reverts #8743 (#8759)
* chore: run mutation tests on PHP 8.4 (#8594)
* chore: solve one of phpstan warnings (#8754)
* chore: update dev tools (#8737)
* chore: update PHPUnit config (#8721)
* chore: update `checkbashisms` to 2.25.12 (#8694)
* chore: update `checkbashisms` to 2.25.14 (#8731)
* chore: use PHPStan type in data providers (#8605)
* chore: use `foreach` values (#8636)
* chore: use `Preg` class everywhere (#8689)
* CI: add self-approved label (#8757)
* CI: run on PHP 8.5 (#8713)
* deps: bump alpine from 3.21.3 to 3.22.0 (#8724)
* deps: bump alpine from 3.21.3 to 3.22.0 (#8726)
* deps: bump ergebnis/composer-normalize from 2.45.0 to 2.46.0 in /dev-tools (#8578)
* deps: bump ergebnis/composer-normalize from 2.46.0 to 2.47.0 in /dev-tools (#8584)
* deps: bump phpstan/phpstan from 2.1.11 to 2.1.12 in /dev-tools in the phpstan group (#8583)
* deps: bump phpstan/phpstan-symfony from 2.0.3 to 2.0.4 in /dev-tools in the phpstan group (#8557)
* deps: bump the phpstan group across 1 directory with 2 updates (#8682)
* deps: upgrade docker deps (#8566)
* docs: extend and fix links to code (#8639)
* docs: Update SECURITY.md (#8716)
* docs: VisibilityRequiredFixer - extend docs (#8561)
* docs: `VisibilityRequiredFixer` - update docs (#8563)
* DX: add `symfony/polyfill-php84` (#8555)
* DX: always use the latest stable `checkbashisms` package (#8732)
* DX: check for `preg_` functions in tests (#8571)
* DX: cleanup `BlankLinesBeforeNamespaceFixerTest` (#8573)
* DX: cleanup `NativeFunctionInvocationFixerTest` (#8567)
* DX: cleanup `NoUselessConcatOperatorFixerTest` (#8572)
* DX: cleanup `SemicolonAfterInstructionFixerTest` (#8570)
* DX: fix data providers (#8693)
* DX: introduce `FCT` class for tokens not present in the lowest supported PHP version (#8706)
* DX: move `symfony/polyfill-php84` to dev deps (#8559)
* DX: support PHP 8.4 in local Docker development environment (#8564)
* DX: trim array/yield keys (#8460)
* DX: use `WhitespacesAwareFixerInterface` only when needed (#8541)
* feat: add `NewExpressionParenthesesFixer` (#8246)
* feat: allowUnsupportedPhpVersion (#8733)
* feat: introduce `MultilinePromotedPropertiesFixer` (#8595)
* feat: Introduce `StaticPrivateMethodFixer` (#4557)
* feat: `BracesPositionFixer` - support property hooks in promoted properties (#8613)
* feat: `ClassAttributesSeparationFixer` - add support for property hooks (#8610)
* feat: `GlobalNamespaceImportFixer` - analyse and fix more annotations with types (#8593)
* feat: `LowercaseKeywordsFixer` - support asymmetric visibility (#8607)
* feat: `NoExtraBlankLinesFixer` - add comma to supported tokens (#8655)
* feat: `NoSuperfluousPhpdocTagsFixer` - support asymmetric visibility (#8700)
* feat: `NullableTypeDeclarationFixer` - support asymmetric visibility (#8697)
* feat: `OrderedClassElementsFixer` - add support for property hooks for abstract properties (#8574)
* feat: `OrderedTypesFixer` - add support for asymmetric visibility (#8552)
* feat: `OrderedTypesFixer` - support asymmetric visibility in promoted property (#8602)
* feat: `PhpdocAddMissingParamAnnotationFixer` - support asymmetric visibility (#8701)
* feat: `PhpdocLineSpanFixer` - support asymmetric visibility (#8702)
* feat: `PhpdocVarWithoutNameFixer` - support asymmetric visibility (#8704)
* feat: `ProtectedToPrivateFixer` - add support for asymmetric visibility (#8569)
* feat: `SingleClassElementPerStatementFixer` - support asymmetric visibility (#8696)
* feat: `SingleSpaceAroundConstructFixer` - add support for asymmetric visibility (#8699)
* feat: `StaticLambdaFixer` - support functions having classy elements with `$this` (#8728)
* feat: `VisibilityRequiredFixer` - support ordering set-visibility modifier (#8606)
* fix(dependabot): convert time values to string types (#8634)
* fix: "array" type must have no prefix to be fixable to "list" (#8692)
* fix: "min"/"max" in int generics must never be prefixed by backslash (#8691)
* fix: Allow non-doc comment on opening inline brace line (#8690)
* fix: `ConstantCaseFixer` - do not touch namespaces starting with `Null\` (#8752)
* fix: `LowercaseStaticReferenceFixer` - do not change global constants (#8727)
* fix: `MultilineWhitespaceBeforeSemicolonsFixer` - do not touch multiline constants definitions (#8615)
* fix: `NewWithParenthesesFixer` - fix `new` without parentheses on PHP 8.4 syntax (#8588)
* fix: `NoMultipleStatementsPerLineFixer` - handle `set` and `get` in different casing in property hooks (#8558)
* fix: `NoUnusedImportsFixer` - handle imported class name with underscore before or after it in PHPDoc (#8598)
* fix: `PhpUnitDedicateAssertFixer` - fix for `assertFalse` with `instanceof` (#8597)
* fix: `PhpUnitNamespacedFixer` must rune before `NoUnneededImportAliasFixer` (#8579)
* fix: `PhpUnitTestClassRequiresCoversFixer` - do not add `@coversNothing` annotation when `CoversTrait` attribute is used (#8734)
* fix: `VisibilityRequiredFixer` - add support for asymmetric visibility (#8586)
* refactor: avoid unused local variables in tests (#8609)
* test: add more cases to `PhpUnitMethodCasingFixerTest` (#8551)
* test: fix "unused local variables `$token`" (#8603)
* test: More verbose error output for integration test (#8565)
* Update SECURITY.md

Changelog for v3.75.0
---------------------

* feat: `ClassAttributesSeparationFixer` - add support for asymmetric visibility (#8518)
* fix: `NativeFunctionInvocationFixer` - fix for property hooks (#8540)
* chore: add return types for data providers for fixers (#8542)
* chore: add return types for data providers for non-fixers (#8543)
* chore: add return types for remaining data providers (#8544)
* chore: make data providers key type `int` if all the keys are strings (#8550)
* chore: make data providers key type `string` if all the keys are strings (#8545)
* chore: SwitchContinueToBreakFixerTest - improve test case descriptions/typehint (#8546)
* chore: `FunctionsAnalyzerTest` cleanup (#8539)
* deps: bump the phpstan group in /dev-tools with 2 updates (#8537)
* test: ProjectCodeTest::testDataProvidersDeclaredReturnType - allow for int as iterable keys (#8548)

Changelog for v3.74.0
---------------------

* feat: add `--format=@auto` (#8513)
* fix: `BracesPositionFixer` - do not create two consecutive whitespace tokens (#8496)
* fix: `MbStrFunctionsFixer` - fix imports with leading backslash (#8507)
* fix: `NoUnreachableDefaultArgumentValueFixer` - do not crash on property hook (#8512)
* fix: `OrderedImportsFixer` - do not take the braces part in grouped imports into account (#8459)
* fix: `OrderedImportsFixer` - fix syntax error with grouped use statement and multiple use with comma (#8483)
* fix: `PhpUnitAttributesFixer` - handle parentheses after data provider method name (#8510)
* fix: `PhpUnitMethodCasingFixer` - do not touch anonymous class (#8463)
* chore: make options that have default and allowed sets the same size the same array (#8529)
* chore: update return type of `FixerOptionInterface::getAllowedValues` (#8530)
* chore: `Preg` - improve types (#8527)
* CI: fix code coverage job (#8520)
* CI: try MacOS job without ParaUnit (#8528)
* deps: update PHPStan (#8531)
* deps: upgrade `PHPStan/*` (#8524)

Changelog for v3.73.1
---------------------

* fix: `OrderedClassElementsFixer` - do not crash on property hook (#8517)

Changelog for v3.73.0
---------------------

* feat: add support for asymmetric visibility to Doctrine's fixers (#8415)
* fix: `GeneralPhpdocTagRenameFixer` - do not rename keys in array shape definition (#8477)
* fix: `MethodArgumentSpaceFixer` - handle when nested in HTML (#8503)
* chore: update `checkbashisms` to 2.25.5 (#8519)
* DX: cleanup `NoExtraBlankLinesFixerTest` (#8505)
* DX: for duplicated test methods check methods without parameters (#8508)
* DX: remove more duplicated test methods (#8506)
* refactor: `Tokenizer` hash metode using `xxHash` (#8491)
* refactor: `TokensAnalyzerTest` - better test `isArray` and `isArrayMultiLine` (#8504)
* test: run code coverage on PHP 8.4 (#8448)

Changelog for v3.72.0
---------------------

* feat: `StatementIndentationFixer` - handle property hooks (#8492)
* fix: `MbStrFunctionsFixer` - fix imports (#8474)
* fix: `TrailingCommaInMultilineFixer` - handle empty match body (#8480)
* fix: `VisibilityRequiredFixer` - handle property hooks (#8495)
* deps: upgrade few dev-deps (#8490)
* deps: Upgrade PHPStan to 2.1.8 (#8489)
* DX: add trailing comma to multiline auto-generated types (#8499)
* refactor: generalize CodeHasher into Hasher (#8500)
* refactor: Runner - unify paths used when using parallel runner (#8488)
* refactor: use Hasher instead of md5 directly (#8501)

Changelog for v3.71.0
---------------------

* feat: OrderedImportsFixer - deprecate length sorting algorithm (#8473)
* fix: `BinaryOperatorSpacesFixer` - do not break alignment of UTF-8 array keys (#8484)
* fix: `PhpdocAlignFixer` - align correctly type with UTF8 characters (#8486)
* fix: `SingleSpaceAroundConstructFixer` - handle alternative syntax (#8317)
* fix: `StatementIndentationFixer` - return in braceless if (#8479)
* chore: update type in `SingleSpaceAroundConstructFixerTest::provideFixWithElseIfCases` (#8481)
* chore: update type in `SingleSpaceAroundConstructFixerTest::provideFixWithIfCases` (#8482)
* deps: bump phpstan/phpstan from 2.1.6 to 2.1.7 in /dev-tools in the phpstan group (#8485)
* DX: cleanup `PhpdocNoAliasTagFixerTest` (#8476)
* refactor: add `FullyQualifiedNameAnalyzer` (#8048)
* refactor: codeHash - update when it's (re-)generated (#8470)
* refactor: `SwitchAnalyzer` - improve performance (#8407)
* test: NameQualifiedTransformerTest - correct test case (#8471)

Changelog for v3.70.2
---------------------

* deps: upgrade deep dev-tools deps (#8472)
* fix: `MbStrFunctionsFixer` must run before `NativeFunctionInvocationFixer` (#8466)
* fix: `MethodArgumentSpaceFixer` - fix nested calls for `ensure_fully_multiline` option (#8469)

Changelog for v3.70.1
---------------------

* fix: `PhpUnitSizeClassFixer` must run before `PhpUnitAttributesFixer` (#8457)
* DX: cleanup `OrderedImportsFixerTest` (#8458)

Changelog for v3.70.0
---------------------

* feat: Add `PhpUnitDataProviderMethodOrderFixer` fixer (#8225)
* feat: `HeaderCommentFixer` - allow validators (#8452)
* feat: `PhpCsFixer` ruleset: use `operator_linebreak` rule for all operators (#8417)
* feat: `PhpUnitMethodCasingFixer` to support PHPUnit's `Test` attribute (#8451)
* feat: `TypeDeclarationSpacesFixer` - Fix whitespace between const type and const name (#8442)
* chore: extend bug report template (#8447)
* chore: extend bug report template - more installation options (#8450)
* CI: phpstan-symfony - add entry point for console (#8292)
* deps: bump alpine from 3.21.2 to 3.21.3 (#8454)
* deps: bump phpstan/phpstan from 2.1.5 to 2.1.6 in /dev-tools in the phpstan group (#8453)
* fix: `CommentsAnalyzer` - allow other forms of assignment as valid structural elements for PHPDocs (#8371)
* fix: `PhpUnitTestClassRequiresCoversFixer` must run before `PhpUnitAttributesFixer` (#8444)
* test: fix `CheckCommandTest::testDryRunModeIsUnavailable` to correctly check that option `--dry-run` is unavailable (#8438)

Changelog for v3.69.1
---------------------

* fix: `PhpUnitAttributesFixer` - convert correctly version constraint (#8439)
* test: `PhpUnitAttributesFixer` must run before `NoEmptyPhpdocFixer` (#8443)

Changelog for v3.69.0
---------------------

* feat: Add unsealed array shape phpdoc support (#8299)
* fix: `OrderedClassElementsFixer` - sort correctly typed constants (#8408)
* chore: do not use test class as test data (#8430)
* chore: update `checkbashisms` to 2.25.2 (#8427)
* chore: use constants instead of literal strings (#8422)
* CI: no need to unlock deps on master (#8426)
* CI: run tests using PHPUnit 12 (#8431)
* deps: bump kubawerlos/composer-smaller-lock from 1.0.1 to 1.1.0 in /dev-tools (#8414)
* deps: bump maglnet/composer-require-checker from 4.14.0 to 4.15.0 in /dev-tools (#8406)
* deps: bump phpstan/phpstan from 2.1.3 to 2.1.5 in /dev-tools in the phpstan group (#8437)
* deps: bump the phpstan (#8423)
* deps: upgrade dev requirements (#8424)

Changelog for v3.68.5
---------------------

* fix: `NativeTypeDeclarationCasingFixer` - do not touch constants named as native types (#8404)

Changelog for v3.68.4
---------------------

* chore: run SCA on PHP 8.4 (#8396)
* fix: NativeTypeDeclarationCasingFixer should not touch property names (#8400)

Changelog for v3.68.3
---------------------

* fix: `NativeTypeDeclarationCasingFixer` - fix for enum with "Mixed" case (#8395)

Changelog for v3.68.2
---------------------

* fix: `NativeTypeDeclarationCasingFixer` - fix for promoted properties, enums, `false` and `mixed` (#8386)
* chore: ensure that `dev-tools` dependencies are bumped (#8389)
* chore: experiment to see if we can auto-shrink the lock after dependabot (#8383)
* chore: experiment to see if we can auto-shrink the lock after dependabot /part (#8384)
* chore: Get rid of Docker warnings during build (#8379)
* chore: remove redundant check (#8391)
* chore: update types (#8390)
* CI: stale - update close msg to emphasize contribution over demand (#8385)
* deps: bump phpcompatibility/phpcompatibility-symfony from 1.2.1 to 1.2.2 in /dev-tools (#8378)
* deps: bump the phpstan group in /dev-tools with 4 updates (#8387)
* refactor: Use native `Yaml::parseFile()` instead of custom method (#8380)

Changelog for v3.68.1
---------------------

* chore: `AutoReview/CiConfigurationTest` - handle failure of reading files (#8375)
* CI: `Docker` - check all `compose` services (#8370)
* deps: bump alpine from 3.18 to 3.21 (#8377)
* deps: bump alpine from 3.18.4 to 3.21.2 (#8362)
* refactor: Tokens::clearEmptyTokens - optimize cache handling (#8335)

Changelog for v3.68.0
---------------------

* feat: `ModernizeStrposFixer` - support `stripos` (#8019)
* chore: `FullyQualifiedStrictTypesFixer` - reduce conditions count (#8368)
* test: `PhpUnitSetUpTearDownVisibilityFixer` - extend test for anonymous classes (#8369)

Changelog for v3.67.1
---------------------

* fix: `FullyQualifiedStrictTypesFixer` - fix return types (#8367)
* fix: `PhpUnitSetUpTearDownVisibilityFixer` - do not touch anonymous classes (#8366)
* chore: allow for class-string in doc types of rule options (#8358)
* chore: CS: Move data provider methods after their test method (#8302)
* chore: dependabot integration (#8357)
* chore: `PHPStan` - upgrade to 2.1 (#8355)
* CI: Introduce PHP compatibility check (#7844)
* deps: bump docker/build-push-action from 5 to 6 (#8361)
* docs: update installation instructions (#8356)

Changelog for v3.67.0
---------------------

* chore: simplify loops using `end`/`prev` functions (#8352)
* feat: Introduce `general_attribute_remove` fixer (#8339)

Changelog for v3.66.2
---------------------

* chore: do not use bitwise "or" assignment operator (#8346)
* chore: remove extra check, never happening (#8348)
* chore: remove impossible `@throws` annotation (#8353)
* chore: Tokens - cleanup (#8350)
* chore: Tokens - minor performance and types optimizations (#8349)

Changelog for v3.66.1
---------------------

* chore: fix CI for Windows (#8326)
* chore: `NoMultipleStatementsPerLineFixer` - be aware of PHP 8.4 property hooks (#8344)
* chore: `TernaryToElvisOperatorFixer` - improvements based on PHPStan detections (#8345)
* chore: `PhpUnitTestCaseStaticMethodCallsFixer` - fix type of `methods` option in documentation and add example with it (#8338)
* chore: update legacy deps (#8342)
* deps: update box (#8336)

Changelog for v3.66.0
---------------------

* feat: `Tokenizer` - initial support for PHP 8.4 property hooks (#8312)
* feat: `PhpUnitTestCaseStaticMethodCallsFixer` - cover PHPUnit v11.5 methods (#8314)
* feat: `PhpUnitTestCaseStaticMethodCallsFixer` - make sure all static protected methods are handled (#8327)
* feat: `PhpUnitTestCaseStaticMethodCallsFixer` - support createStub (#8319)
* feat: `UseArrowFunctionsFixer` - support multiline statements (#8311)
* fix: `NullableTypeDeclarationFixer` - do not break multi-line declaration (#8331)
* test: `CiConfigurationTest` - drop not needed condition, logic is checked in upcoming assertion (#8303)
* chore: add more typehints (#8325)
* chore: `DotsOutput` - more const, better typing (#8318)
* chore: mark classes as readonly (#8275)
* chore: more const, better typing (#8320)
* chore: temporarily prevent symfony/process 7.2+ (#8322)
* chore: `Tokens` - simplify (un)registerFoundToken types (#8328)
* chore: upgrade PHPStan (#8321)
* chore: `BraceTransformer` - don't touch curly index braces since 8.4, as it's not a valid syntax anymore (#8313)
* CI: enable phpdoc_to_property_type on php-lowest (#8324)
* Create SECURITY.md
* docs: `Tokens` - fix docs (#8332)

Changelog for v3.65.0
---------------------

* feat: Ability to set upper limit when using CPU auto-detection (#8280)
* feat: create `@PHP82Migration:risky` ruleset (#8277)
* feat: Impl. TypeExpression::mapTypes() (#8077)
* feat: Parse array/generic/nullable type into inner expression (#8106)
* feat: phpdoc_to_property_type - handle virtual types and null initialization, enable in php-highest CI job (#8283)
* feat: Store PHPDoc offset in `DataProviderAnalysis` (#8226)
* feat: Support for complex PHPDoc types in `fully_qualified_strict_types` (#8085)
* fix: check for priority tests correctly (#8221)
* fix: Do not mark with `@coversNothing` if `CoversMethod`/`CoversFunction` attribute is used (#8268)
* fix: enum-case mistaken for const invocation (#8190)
* fix: fix typing of few properties wrongly typed as non-nullable (#8285)
* fix: fix typing property wrongly typed as non-nullable (#8290)
* fix: MethodChainingIndentationFixer does not fix indentation of last chained property (#8080)
* fix: NoSuperfluousPhpdocTagsFixer - Remove superfluous phpdoc of parameter with attribute (#8237)
* fix: parsing mixed `&` and `|` in `TypeExpression` (#8210)
* fix: proper base class used for AbstractDoctrineAnnotationFixer templates generation (#8291)
* fix: Properly recognise constants in foreach loops (#8203)
* fix: Tokens::overrideRange() block cache pruning (#8240)
* fix: `BlankLineAfterOpeningTagFixer` - add blank line in file starting with multi-line comment (#8256)
* fix: `MultilineWhitespaceBeforeSemicolonsFixer` - do not produce syntax error when there is a meaningful token after semicolon (#8230)
* fix: `NullableTypeDeclarationFixer` - do not break syntax when there is no space before `?` (#8224)
* fix: `PhpUnitDataProvider(.+)Fixer` - do not omit when there is an attribute between PHPDoc and test method (#8185)
* fix: `PhpUnitDataProviderNameFixer` - for an attribute between PHPDoc and test method (#8217)
* chore: add todo for PHP v8 (#8274)
* chore: auto-fallback to sequential runner if single CPU would handle it (#8154)
* chore: block changing tokens collection size using `PhpCsFixer\Tokenizer\Tokens::setSize` (#8257)
* chore: bump dev-tools (#8286)
* chore: bump PHPStan (#8245)
* chore: Cheaper file check first (#8252)
* chore: ConfigInterface - better types (#8244)
* chore: do not call `Tokens::setSize` in `GroupImportFixer` (#8253)
* chore: do not use `Reflection*::setAccessible` (#8264)
* chore: fix priority tests (#8223)
* chore: Fix typos in AbstractFixerTestCase (#8247)
* chore: GithubClient - make URL injectable (#8272)
* chore: Implement PHPStan `Preg::match()` extensions (#8103)
* chore: mark remaining Analysis as `@internal` (#8284)
* chore: PHPStan - upgrade to v2 (#8288)
* chore: reduce amount of class mutable properties (#8281)
* chore: remove from priority tests exceptions tests that are not actually exceptions (#8222)
* chore: remove incorrect priority tests (#8231)
* chore: remove not needed PHP version requirements in descriptions (#8265)
* chore: remove unnecessary methods (#8200)
* chore: tests/Tokenizer/Transformer - better typehinting (#8243)
* chore: Token - remove 'changed' property (#8273)
* chore: Token::getContent() phpdoc return type (#8236)
* chore: update dev dependencies in root (#8289)
* chore: update PHPStan to 1.12.9 (#8271)
* chore: update `checkbashisms` to 2.24.1 (#8258)
* chore: use null coalescing assignment operator where possible (#8219)
* CI: allow macos to fail (#8194)
* CI: build phar on PHP 8.3 (#8195)
* CI: drop matrix for single-matrix-entry jobs of SCA and Deployment checks (#8193)
* CI: Ensure php-cs-fixer PHP compatibility /part (#8241)
* CI: Ensure `php-cs-fixer` PHP compatibility (#8235)
* CI: generate and execute code in `assert` (#8207)
* CI: update PHPStan to 1.12.2 (#8198)
* CI: update PHPStan to 1.12.3 (#8204)
* CI: use phpstan-symfony (#8287)
* depr: ConfigInterface::getPhpExecutable() and ConfigInterface::setPhpExecutable() (#8192)
* deps: add `composer-smaller-lock` (#8263)
* deps: Update PHPStan to 1.12.4 (#8215)
* deps: Update PHPStan to 1.12.5 (#8218)
* deps: update PHPStan to 1.12.7 (#8255)
* docs: fix inconsistency in config doc (#8269)
* docs: mention github action example instead of travis-ci (#8250)
* DX: Cover `php-cs-fixer` file with static analysis (#8229)
* DX: Make `TypeExpression` API more explicit about composite types (#8214)
* refactor: change `_AttributeItems` to `non-empty-list<_AttributeItem>` to allow using single attribute item (#8199)
* refactor: Rename newly introduced option (#8293)
* refactor: Runner - Enhance eventing system (#8276)
* refactor: Runner - make 4.0 TODOs easier to understand (#8196)
* refactor: use arrow functions in more places (#8294)
* test: `@PHP82Migration:risky` - add integration tests (#8278)

Changelog for v3.64.0
---------------------

* feat: Symfony - adjust configuration for sets (#8188)
* feat: Symfony.trailing_comma_in_multiline - adjust configuration (#8161)
* feat: Update PSR2, PSR12 and PER-CS2 with `single_space_around_construct` config (#8171)
* CI: Update PHPStan to 1.12.0 and fix the error that appeared (#8184)

Changelog for v3.63.2
---------------------

* fix: `FullyQualifiedStrictTypesFixer` - reset cache even if there is no `use` (#8183)

Changelog for v3.63.1
---------------------

* dummy release

Changelog for v3.63.0
---------------------

* feat: Add `array_destructuring` as option for `trailing_comma_in_multiline` (#8172)
* feat: remove braces even for single import (#8156)
* feat: TrailingCommaInMultilineFixer - dynamically evaluate config against PHP version (#8167)
* fix: Do not shorten FQN for class resolution if imported symbol is not a class (#7705)
* fix: Ensure PHP binary path is used as a single CLI argument in parallel worker process (#8180)
* fix: `PhpUnitAttributesFixer` - fix priorities with `PhpUnitDataProvider(.+)Fixer` (#8169)
* chore: add  tags for data providers that will change PHPStan's baseline (#8178)
* chore: add `@return` tags for data providers already having PHPDoc (#8176)
* chore: add `@return` tags for data providers that do not have array in data (#8179)
* chore: remove duplicates from data providers (#8164)
* chore: remove duplicates from data providers that are copies in code (#8145)
* chore: remove `beStrictAboutTodoAnnotatedTests` from PHPUnit's config (#8160)
* CI: Update PHPStan to 1.11.10 (#8163)
* CI: Update PHPStan to 1.11.11 and fix error that changed (#8174)
* docs: fix indent on rule `date_time_create_from_format_call` (#8173)

Changelog for v3.62.0
---------------------

* feat: set new_with_parentheses for anonymous_class to false in PER-CS2.0 (#8140)
* chore: NewWithParenthesesFixer - create TODO to change the default configuration to match PER-CS2 (#8148)

Changelog for v3.61.1
---------------------

* fix: `NoSuperfluousPhpdocTagsFixer` - fix "Undefined array key 0" error (#8150)

Changelog for v3.61.0
---------------------

* feat: no_superfluous_phpdoc_tags - also cover ?type (#8125)
* feat: support PHPUnit v9.1 naming for some asserts (#7997)
* fix: Do not mangle non-whitespace token in `PhpdocIndentFixer` (#8147)
* DX: add more typehints for `class-string` (#8139)
* DX: refactor `ProjectCodeTest::provideDataProviderMethodCases` (#8138)

Changelog for v3.60.0
---------------------

* feat: Add sprintf in the list of compiler optimized functions (#8092)
* feat: `PhpUnitAttributesFixer` - add option to keep annotations (#8090)
* chore: cleanup tests that had `@requires PHP 7.4` ages ago (#8122)
* chore: cleanup `TokensAnalyzerTest` (#8123)
* chore: fix example issue reported by reportPossiblyNonexistentGeneralArrayOffset from PHPStan (#8089)
* chore: NoSuperfluousPhpdocTagsFixer - no need to call heavy toComparableNames method to add null type (#8132)
* chore: PHPStan 11 array rules (#8011)
* chore: PhpUnitSizeClassFixerTest - solve PHP 8.4 issues (#8105)
* chore: reduce PHPStan errors in PhpUnitAttributesFixer (#8091)
* chore: reuse test methods (#8119)
* CI: check autoload (#8121)
* CI: Update PHPStan to 1.11.8 (#8133)
* deps: upgrade dev-tools (#8102)
* DX: check for duplicated test data (#8131)
* DX: check for duplicated test methods (#8124)
* DX: check for duplicated test methods (as AutoReview test) (#8134)
* DX: do not exclude duplicates that are clearly mistakes (#8135)
* DX: Dump `offsetAccess.notFound` errors to baseline (#8107)
* fix: Better way of walking types in `TypeExpression` (#8076)
* fix: CI for PHP 8.4 (#8114)
* fix: update `TokensTest` to shrink PHPStan's baseline (#8112)
* fix: `no_useless_concat_operator` - do not break variable (2) (#7927)
* fix: `NullableTypeDeclarationFixer` - don't convert standalone `null` into nullable union type (#8098)
* fix: `NullableTypeDeclarationFixer` - don't convert standalone `NULL` into nullable union type (#8111)
* fix: `NullableTypeDeclarationFixer` - insert correct token (#8118)
* fix: `PhpUnitAttributesFixer` - handle multiple annotations of the same name (#8075)

Changelog for v3.59.3
---------------------

* refactor: refactor to templated trait+interface (#7988)

Changelog for v3.59.2
---------------------

* fix: "list" is reserved type (#8087)
* chore: add missing type in method prototype (#8088)
* CI: bump Ubuntu version (#8086)
* deps: bump infection to unblock PHPUnit 11, and few more as chore (#8083)

Changelog for v3.59.1
---------------------

* fix: Bump React's JSON decoder buffer size (#8068)
* docs: options - handle enums in dicts (#8082)

Changelog for v3.59.0
---------------------

* feat(Docker): Multi-arch build (support for `arm64`) (#8079)
* feat: `@PhpCsFixer` ruleset - normalise implicit backslashes in single quoted strings (#7965)
* feat: `SimpleToComplexStringVariableFixer` - support variable being an array (#8064)
* fix: Look up for PHPDoc's variable name by only chars allowed in the variables (#8062)
* fix: Update `PhpUnitTestCaseStaticMethodCallsFixer::STATIC_METHODS` (#8073)
* fix: `native_constant_invocation` - array constants with native constant names (#8008)
* chore: update PHPStan (#8060)
* CI: Update PHPStan to 1.11.4 (#8074)
* docs: don't expose list as config type for dicts (#8081)
* docs: Make wording in `final_class` docs less dismissive (#8065)
* docs: Update 1-bug_report.yml (#8067)
* DX: Remove version from Docker Compose files (#8061)

Changelog for v3.58.1
---------------------

* fix: `ConstantCaseFixer` - do not change class constant usages (#8055)
* fix: `PhpUnitTestClassRequiresCoversFixer` - do not add annotation when attribute with leading slash present (#8054)

Changelog for v3.58.0
---------------------

* chore(doc): Use FQCN for parallel config in documentation (#8029)
* chore: fix typo in `PhpUnitTestClassRequiresCoversFixerTest` (#8047)
* chore: RandomApiMigrationFixer - do not modify configuration property (#8033)
* chore: Tokens::setCode - further improvements to cache (#8053)
* chore: update PHPStan (#8045)
* docs: Add missing imports in a cookbook about creating custom rules (#8031)
* docs: fix deprecated string interpolation style (#8036)
* docs: global_namespace_import - simplify allowed config types (#8023)
* feat(GroupImportFixer): Ability to configure which type of imports should be grouped (#8046)
* fix: clear `Tokens::$blockStartCache` and `Tokens::$blockEndCache` when calling `Tokens::setCode` (#8051)
* fix: correctly handle PHP closing tag with `simplified_null_return` (#8049)
* fix: `ConstantCaseFixer` - do not change namespace (#8004)
* fix: `PhpUnitAttributesFixer` - do not add attribute if already present (#8043)
* fix: `PhpUnitSizeClassFixer` - do not add annotation when there are attributes (#8044)
* fix: `PhpUnitTestClassRequiresCoversFixer` - attribute detection when class is `readonly` (#8042)

Changelog for v3.57.2
---------------------

* docs: better ConfigurableFixer allowed types (#8024)
* docs: Improve Docker usage example (#8021)
* feat: Report used memory to 2 decimal digits only (#8017)
* fix: Support named args in `ParallelConfigFactory::detect()` (#8026)
* fix: `php_unit_test_class_requires_covers` Attribute detection when class is final (#8016)

Changelog for v3.57.1
---------------------

* chore: update PHPDoc in `Preg::matchAll` (#8012)
* fix: Runner - handle no files while in parallel runner (#8015)

Changelog for v3.57.0
---------------------

* feat: Ability to run Fixer with parallel runner 🎉 (#7777)

Changelog for v3.56.2
---------------------

* chore: update PHPStan (#8010)
* DX: Fix Mess Detector violations (#8007)
* DX: Install PCov extension for local Docker (#8006)

Changelog for v3.56.1
---------------------

* chore: improve PHPDoc typehints (#7994)
* CI: Allow any integer in PHPStan error for Token's constructor (#8000)
* fix: Better array shape in `PhpUnitDedicateAssertFixer` (#7999)
* fix: `ConstantCaseFixer` - do not touch typed constants (#7998)

Changelog for v3.56.0
---------------------

* feat: `TrailingCommaInMultilineFixer` - handle trailing comma in language constructs (#7989)
* fix: `TrailingCommaInMultilineFixer` - language constructs should be covered by arguments, not parameters (#7990)
* chore: remove invalid comment (#7987)
* DX: Cache optimisation (#7985)

Changelog for v3.55.0
---------------------

* feat: Introduce `OrderedAttributesFixer` (#7395)
* chore: few SCA fixes and dev-tools update (#7969)
* chore: fix phpdoc types (#7977)
* chore: narrow PHPDoc types (#7979)
* chore: Normalize implicit backslashes in single quoted strings internally (#7786)
* chore: phpdoc - rely on strict list/tuple/assoc instead of array (#7978)
* chore: PhpUnitDataProviderNameFixer - follow config creation pattern (#7980)
* chore: Preg - drop half-support for array-pattern (#7976)
* chore: re-use CodeHasher (#7984)
* chore: RuleSetsTest - assert that Fixer is configurable (#7961)
* chore: sugar syntax (#7986)
* chore: Tokens should be always a list (#7698)
* CI: Ad-hoc fix for MacOS jobs (#7970)
* CI: Fix calculating diff between branches in PRs (#7973)
* DX: allow to enforce cache mechanism by env var (#7983)
* DX: do not typehint fixed-length arrays as lists (#7974)
* DX: Prevent having deprecated fixers listed as successors of other deprecated fixers (#7967)
* DX: Resolve/Ignore PHPStan issues on level 6 + bump to level 7 with new baseline (#7971)
* DX: use `list` type in PHPDoc (#7975)
* fix: `PhpUnitAttributesFixer` - fix for `#[RequiresPhp]` exceeding its constructor parameters (#7966)
* test: don't count comment after class as another classy element (#7982)

Changelog for v3.54.0
---------------------

* feat: introduce `PhpUnitAttributesFixer` (#7831)
* chore: Properly determine self-approval trigger commit (#7936)
* chore: Revert ref for self-approval Git checkout (#7944)
* CI: check if proper array key is declared (#7912)
* DX: cleanup `FullyQualifiedStrictTypesFixerTest` (#7954)
* DX: cleanup `PhpdocNoAccessFixerTest` (#7933)
* DX: cleanup `PhpUnitMethodCasingFixerTest` (#7948)
* DX: cleanup `PhpUnitStrictFixerTest` (#7938)
* DX: Improve internal dist config for Fixer (#7952)
* DX: Improve issue templates (#7942)
* DX: there is no namespace if there is no PHP code (#7953)
* DX: update .gitattributes (#7931)
* fix: Remove Infection during Docker release (#7937)
* fix: `FullyQualifiedStrictTypesFixer` - do not add imports before PHP opening tag (#7955)
* fix: `PhpUnitMethodCasingFixer` - do not double underscore (#7949)
* fix: `PhpUnitTestClassRequiresCoversFixer` - do not add annotation when there are attributes (#7880)
* test: Ignore PHP version related mutations (#7935)

Changelog for v3.53.0
---------------------

* chore: Use `list` over `array` in more places (#7905)
* CI: allow for self-approvals for maintainers (#7921)
* CI: Improve Infection setup (#7913)
* CI: no need to trigger enable auto-merge when self-approve (#7929)
* DX: reduce `array_filter` function usages (#7923)
* DX: remove duplicated character from `trim` call (#7930)
* DX: update actions producing warnings (#7925)
* DX: update actions producing warnings (#7928)
* DX: update `phpstan/phpstan-strict-rules` (#7924)
* feat: Add trailing comma in multiline to PER-CS 2.0 (#7916)
* feat: Introduce `AttributeAnalysis` (#7909)
* feat: `@PHP84Migration` introduction (#7774)
* fix: Constant invocation detected in typed constants (#7892)
* fix: `PhpdocArrayTypeFixer` - JIT stack limit exhausted (#7895)
* test: Introduce Infection for mutation tests (#7874)

Changelog for v3.52.1
---------------------

* fix: StatementIndentationFixer - do not crash on ternary operator in class property (#7899)
* fix: `PhpCsFixer\Tokenizer\Tokens::setSize` return type (#7900)

Changelog for v3.52.0
---------------------

* chore: fix PHP 8.4 deprecations (#7894)
* chore: fix PHPStan 1.10.60 issues (#7873)
* chore: list over array in more places (#7876)
* chore: replace template with variable in Preg class (#7882)
* chore: update PHPStan (#7871)
* depr: `nullable_type_declaration_for_default_null_value` - deprecate option that is against `@PHP84Migration` (#7872)
* docs: Fix typo (#7889)
* feat: Add support for callable template in PHPDoc parser (#7084)
* feat: Add `array_indentation` to `PER-CS2.0` ruleset (#7881)
* feat: `@Symfony:risky` - add `no_unreachable_default_argument_value` (#7863)
* feat: `PhpCsFixer` ruleset - enable `nullable_type_declaration_for_default_null_value` (#7870)
* fix: Constant invocation detected in DNF types (#7869)
* fix: Correctly indent multiline constants and properties (#7875)
* fix: `no_useless_concat_operator` - do not break variable (#7827)
* fix: `TokensAnalyzer` - handle unary operator in arrow functions (#7862)
* fix: `TypeExpression` - fix "JIT stack limit exhausted" error (#7843)

Changelog for v3.51.0
---------------------

* chore: add missing tests for non-documentation classes (#7848)
* chore: do not perform type analysis in tests (#7852)
* chore: list over array in more places (#7857)
* chore: tests documentation classes (#7855)
* feat: `@Symfony` - add nullable_type_declaration (#7856)
* test: fix wrong type in param annotation (#7858)

Changelog for v3.50.0
---------------------

* chore: add missing types (#7842)
* chore: BlocksAnalyzer - raise exception on invalid index (#7819)
* chore: DataProviderAnalysis - expect list over array (#7800)
* chore: do not use `@large` on method level (#7832)
* chore: do not use `@medium` on method level (#7833)
* chore: Fix typos (#7835)
* chore: rename variables (#7847)
* chore: some improvements around array typehints (#7799)
* CI: fix PHP 8.4 job (#7829)
* DX: Include `symfony/var-dumper` in dev tools (#7795)
* feat: Ability to remove unused imports from multi-use statements (#7815)
* feat: allow PHPUnit 11 (#7824)
* feat: Allow shortening symbols from multi-use statements (only classes for now) (#7816)
* feat: introduce `PhpdocArrayTypeFixer` (#7812)
* feat: PhpUnitTestCaseStaticMethodCallsFixer - cover PHPUnit v11 methods (#7822)
* feat: Support for multi-use statements in `NamespaceUsesAnalyzer` (#7814)
* feat: `MbStrFunctionsFixer` - add support for `mb_trim`, `mb_ltrim` and `mb_rtrim` functions (#7840)
* feat: `NoEmptyPhpdocFixer` - do not leave empty line after removing PHPDoc (#7820)
* feat: `no_superfluous_phpdoc_tags` - introduce `allow_future_params` option (#7743)
* fix: do not use wrongly named arguments in data providers (#7823)
* fix: Ensure PCNTL extension is always installed in Docker (#7782)
* fix: PhpdocListTypeFixer - support key types containing `<…>` (#7817)
* fix: Proper build target for local Docker Compose (#7834)
* fix: union PHPDoc support in `fully_qualified_strict_types` fixer (#7719)
* fix: `ExecutorWithoutErrorHandler` - remove invalid PHP 7.4 type (#7845)
* fix: `fully_qualified_strict_types` must honour template/local type identifiers (#7724)
* fix: `MethodArgumentSpaceFixer` - do not break heredoc/nowdoc (#7828)
* fix: `NumericLiteralSeparatorFixer` - do not change `float` to `int` when there is nothing after the dot (#7805)
* fix: `PhpUnitStrictFixer` - do not crash on property having the name of method to fix (#7804)
* fix: `SingleSpaceAroundConstructFixer` - correctly recognise multiple constants (#7700)
* fix: `TypeExpression` - handle array shape key with dash (#7841)

Changelog for v3.49.0
---------------------

* chore(checkbashisms): update to 2.23.7 (#7780)
* chore: add missing key types in PHPDoc types (#7779)
* chore: Exclude `topic/core` issues/PRs from Stale Bot (#7788)
* chore: `DescribeCommand` - better handling of deprecations (#7778)
* docs: docker - use gitlab reporter in GitLab integration example (#7764)
* docs: docker in CI - don't suggest command that overrides path from config file (#7763)
* DX: check deprecations exactly (#7742)
* feat: Add `ordered_types` to `@Symfony` (#7356)
* feat: introduce `PhpdocListTypeFixer` (#7796)
* feat: introduce `string_implicit_backslashes` as `escape_implicit_backslashes` replacement (#7669)
* feat: update `Symfony.nullable_type_declaration_for_default_null_value` config (#7773)
* feat: `@PhpCsFixer` ruleset - enable `php_unit_data_provider_static` (#7685)
* fix: Allow using cache when running in Docker distribution (#7769)
* fix: ClassDefinitionFixer for anonymous class with phpdoc/attribute on separate line (#7546)
* fix: `ClassKeywordFixer` must run before `FullyQualifiedStrictTypesFixer` (#7767)
* fix: `function_to_constant` `get_class()` replacement (#7770)
* fix: `LowercaseStaticReferenceFixer` - do not change typed constants (#7775)
* fix: `PhpdocTypesFixer` - handle more complex types (#7791)
* fix: `TypeExpression` - do not break type using `walkTypes` method (#7785)

Changelog for v3.48.0
---------------------

* chore: `FullyQualifiedStrictTypesFixer` must run before `OrderedInterfacesFixer` (#7762)
* docs: Add PHP-CS-Fixer integration in a GitHub Action step (#7757)
* feat: `PhpdocTypesOrderFixer` Support DNF types (#7732)
* fix: Support shebang in fixers operating on PHP opening tag (#7687)
* fix: work correctly for a switch/case with ternary operator (#7756)
* fix: `NoUselessConcatOperatorFixer` - do not remove new line (#7759)

Changelog for v3.47.1
---------------------

* fix: Do not override short name with relative reference (#7752)
* fix: make `BinaryOperatorSpacesFixer` work as pre-v3.47 (#7751)
* fix: Proper Docker image name suffix (#7739)
* fix: `FullyQualifiedStrictTypesFixer` - do not change case of the symbol when there's name collision between imported class and imported function (#7750)
* fix: `FullyQualifiedStrictTypesFixer` - do not modify statements with property fetch and `::` (#7749)

Changelog for v3.47.0
---------------------

* chore: better identify EXPERIMENTAL rules (#7729)
* chore: fix issue detected by unlocked PHPStan + upgrade dev-tools (#7678)
* chore: handle extract() (#7684)
* chore: Mention contributors in app info (#7668)
* chore: no need to mark private methods as internal (#7715)
* chore: ProjectCodeTests - dry for function usage extractions (#7690)
* chore: reduce PHPStan baseline (#7644)
* chore: use numeric literal separator for PHP version IDs (#7712)
* chore: use numeric_literal_separator for project (#7713)
* chore: Utils::sortElements - better typing (#7646)
* CI: Allow running Stale Bot on demand (#7711)
* CI: Fix PHP 8.4 (#7702)
* CI: Give write permissions to Stale Bot (#7716)
* CI: Use `actions/stale` v9 (#7710)
* docs: Add information about allowing maintainers to update PRs (#7683)
* docs: CONTRIBUTING.md - update Opening a PR (#7691)
* docs: Display/include tool info/version by default in commands and reports (#7733)
* DX: fix deprecation tests warnings for PHP 7.4 (#7725)
* DX: update `host.docker.internal` in Compose override template (#7661)
* DX: `NumericLiteralSeparatorFixer` - change default strategy to `use_separator` (#7730)
* feat: Add support for official Docker images of Fixer (#7555)
* feat: Add `spacing` option to `PhpdocAlignFixer` (#6505)
* feat: Add `union_types` option to `phpdoc_to_param_type`, `phpdoc_to_property_type`, and `phpdoc_to_return_type` fixers (#7672)
* feat: Introduce `heredoc_closing_marker` fixer (#7660)
* feat: Introduce `multiline_string_to_heredoc` fixer (#7665)
* feat: Introduce `NumericLiteralSeparatorFixer` (#6761)
* feat: no_superfluous_phpdoc_tags - support for arrow function (#7666)
* feat: Simplify closing marker when possible in `heredoc_closing_marker` fixer (#7676)
* feat: Support typed properties and attributes in `fully_qualified_strict_types` (#7659)
* feat: `@PhpCsFixer` ruleset - enable no_whitespace_before_comma_in_array.after_heredoc (#7670)
* fix: Improve progress bar visual layer (#7708)
* fix: indentation of control structure body without braces (#7663)
* fix: make sure all PHP extensions required by PHPUnit are installed (#7727)
* fix: PhpdocToReturnTypeFixerTest - support for arrow functions (#7645)
* fix: Several improvements for `fully_qualified_strict_types` (respect declared symbols, relative imports, leading backslash in global namespace) (#7679)
* fix: SimplifiedNullReturnFixer - support array return typehint (#7728)
* fix: Support numeric values without leading zero in `numeric_literal_separator` (#7735)
* fix: `BinaryOperatorSpacesFixer` - align correctly when multiple shifts occurs in single line (#7593)
* fix: `ClassReferenceNameCasingFixer` capitalizes the property name after the nullsafe operator (#7696)
* fix: `fully_qualified_strict_types` with `leading_backslash_in_global_namespace` enabled - handle reserved types in phpDoc (#7648)
* fix: `NoSpaceAroundDoubleColonFixer` must run before `MethodChainingIndentationFixer` (#7723)
* fix: `no_superfluous_phpdoc_tags` must honour multiline docs (#7697)
* fix: `numeric_literal_separator` - Handle zero-leading floats properly (#7737)
* refactor: increase performance by ~7% thanks to `Tokens::block*Cache` hit increased by ~12% (#6176)
* refactor: Tokens - fast check for non-block in 'detectBlockType', evaluate definitions only once in 'getBlockEdgeDefinitions' (#7655)
* refactor: `Tokens::clearEmptyTokens` - play defensive with cache clearing (#7658)
* test: ensure we do not forget to test any short_open_tag test (#7638)

Changelog for v3.46.0
---------------------

* chore: fix internal typehints in Tokens (#7656)
* chore: reduce PHPStan baseline (#7643)
* docs: Show class with unit tests and BC promise info (#7667)
* feat: change default ruleset to `@PER-CS` (only behind PHP_CS_FIXER_FUTURE_MODE=1) (#7650)
* feat: Support new/instanceof/use trait in `fully_qualified_strict_types` (#7653)
* fix: FQCN parse phpdoc using full grammar regex (#7649)
* fix: Handle FQCN properly with `leading_backslash_in_global_namespace` option enabled (#7654)
* fix: PhpdocToParamTypeFixerTest - support for arrow functions (#7647)
* fix: PHP_CS_FIXER_FUTURE_MODE - proper boolean validation (#7651)

Changelog for v3.45.0
---------------------

* feat: Enable symbol importing in `@PhpCsFixer` ruleset (#7629)
* fix: NoUnneededBracesFixer - improve handling of global namespace (#7639)
* test: run tests with "short_open_tag" enabled (#7637)

Changelog for v3.44.0
---------------------

* feat: Introduce percentage bar as new default progress output (#7603)

Changelog for v3.43.1
---------------------

* fix: Import only unique symbols' short names (#7635)

Changelog for v3.43.0
---------------------

* chore: change base of `@Symfony` set to `@PER-CS2.0` (#7627)
* chore: PHPUnit - allow for v10 (#7606)
* chore: Preg - rework catching the error (#7616)
* chore: Revert unneeded peer-dep-pin and re-gen lock file (#7618)
* docs: drop extra note about 8.0.0 bug in README.md (#7614)
* feat: add cast_spaces into `@PER-CS2.0` (#7625)
* feat: Configurable phpDoc tags for FQCN processing (#7628)
* feat: StatementIndentationFixer - introduce stick_comment_to_next_continuous_control_statement config (#7624)
* feat: UnaryOperatorSpacesFixer - introduce only_dec_inc config (#7626)
* fix: FullyQualifiedStrictTypesFixer - better support annotations in inline {} (#7633)
* fix: Improve how FQCN is handled in phpDoc (#7622)
* fix: phpdoc_align - fix multiline tag alignment issue (#7630)

Changelog for v3.42.0
---------------------

* chore: aim to not rely on internal array pointer but use array_key_first (#7613)
* chore: deprecate Token::isKeyCaseSensitive (#7599)
* chore: deprecate Token::isKeyCaseSensitive, 2nd part (#7601)
* chore: do not check PHP_VERSION_ID (#7602)
* chore: FileFilterIteratorTest - more accurate type in docs (#7542)
* chore: minor code cleanup (#7607)
* chore: more types (#7598)
* chore: PHPDoc key-value spacing (#7592)
* chore: PHPUnit - run defects first (#7570)
* chore: ProjectCodeTest - DRY on Tokens creation (#7574)
* chore: ProjectCodeTest - prepare for symfony/console v7 (#7605)
* chore: ProjectCodeTest::provide*ClassCases to return iterable with key for better tests execution log (#7572)
* chore: ProjectCodeTest::testDataProvidersDeclaredReturnType - use better DataProvider to simplify test logic (#7573)
* chore: TokensAnalyzer - string-enum for better typehinting (#7571)
* chore: unify tests not agnostic of PHP version (#7581)
* chore: use ::class more (#7545)
* CI: Introduce `composer-unused` (#7536)
* DX: add types to anonymous functions (#7561)
* DX: Allow running smoke tests within Docker runtime (#7608)
* DX: check fixer's options for wording (#7543)
* DX: cleanup deprecation message (#7576)
* DX: do not allow overriding constructor of `PHPUnit\Framework\TestCase` (#7563)
* DX: do not import ExpectDeprecationTrait in UtilsTest (#7562)
* DX: Enforce consistent naming in tests (#7556)
* DX: fix checking test class extends `PhpCsFixer\Tests\TestCase` (#7567)
* DX: make sure that exceptions in `AbstractFixerTestCase::testProperMethodNaming` are not already fixed (#7588)
* DX: remove recursion from AbstractIntegrationTestCase::testIntegration (#7577)
* DX: remove `PhpUnitNamespacedFixerTest::testClassIsFixed` (#7564)
* DX: remove `symfony/phpunit-bridge` (#7578)
* DX: replace fixture classes with anonymous ones (#7533)
* DX: Unify Docker mount points and paths (#7549)
* DX: unify fixer's test method names - quick wins (#7584)
* DX: unify tests for casing fixers (#7558)
* DX: use anonymous function over concrete classes (#7553)
* feat(EXPERIMENTAL): ClassKeywordFixer (#2918)
* feat(EXPERIMENTAL): ClassKeywordFixer, part 2 (#7550)
* feat(PhpdocToCommentFixer): Add option to handle return as valid docblock usage (#7401) (#7402)
* feat: Ability to import FQCNs found during analysis (#7597)
* feat: add phpDoc support for `fully_qualified_strict_types` fixer (#5620)
* feat: Handle deprecated rule sets similarly to deprecated fixers (#7288)
* feat: PhpUnitTestCaseStaticMethodCallsFixer - cover PHPUnit v10 methods (#7604)
* feat: Support more FQCNs cases in `fully_qualified_strict_types` (#7459)
* fix: AbstractFixerTestCase - fix checking for correct casing (#7540)
* fix: Better OS detection in integration tests (#7547)
* fix: NativeTypeDeclarationCasingFixer - handle static property without type (#7589)
* test: AutoReview - unify data provider returns (#7544)
* test: check to have DataProviders code agnostic of PHP version (#7575)

Changelog for v3.41.1
---------------------

* DX: Change `@testWith` to `@dataProvider` (#7535)
* DX: Introduce Markdownlint (#7534)
* fix: NativeTypeDeclarationCasingFixer - do not crash on `var` keyword (#7538)

Changelog for v3.41.0
---------------------

* chore: Move `mb_str_functions` PHP 8.3 cases to separate test (#7505)
* chore: Symfony v7 is now stable (#7469)
* CI: drop PHP 8.3 hacks (#7519)
* docs: Improve docs for `no_spaces_after_function_name` (#7520)
* DX: Ability to run Sphinx linter locally (#7481)
* DX: AbstractFixerTest - use anonymous classes (#7527)
* DX: Add progress output for `cs:check` script (#7514)
* DX: align doubles naming (#7525)
* DX: remove AbstractFixerTestCase::getTestFile() (#7495)
* DX: remove jangregor/phpstan-prophecy (#7524)
* DX: remove Prophecy (#7509)
* DX: replace Prophecy with anonymous classes in CacheTest (#7503)
* DX: replace Prophecy with anonymous classes in ProcessLintingResultTest (#7501)
* DX: Utilise auto-discovery for PHPStan formatter (#7490)
* feat: Support `mb_str_pad` function in `mb_str_functions` rule (#7499)
* fix: BinaryOperatorSpacesFixer - do not add whitespace inside short function (#7523)
* fix: Downgrade PDepend to version not supporting Symfony 7 (#7513)
* fix: GlobalNamespaceImportFixer - key in PHPDoc's array shape matching class name (#7522)
* fix: SpacesInsideParenthesesFixer - handle class instantiation parentheses (#7531)
* Update PHPstan to 1.10.48 (#7532)

Changelog for v3.40.2
---------------------

* docs: fix link to source classes (#7493)

Changelog for v3.40.1
---------------------

* chore: Delete stray file x (#7473)
* chore: Fix editorconfig (#7478)
* chore: Fix typos (#7474)
* chore: Fix YAML line length (#7476)
* chore: Indent JSON files with 4 spaces (#7480)
* chore: Make YAML workflow git-based (#7477)
* chore: Use stable XDebug (#7489)
* CI: Lint docs (#7479)
* CI: Use PHPStan's native Github error formatter (#7487)
* DX: fix PHPStan error (#7488)
* DX: PsrAutoloadingFixerTest - do not build mock in data provider (#7491)
* DX: PsrAutoloadingFixerTest - merge all data providers into one (#7492)
* DX: Update PHPStan to 1.10.46 (#7486)
* fix: `NoSpacesAfterFunctionNameFixer` - do not remove space if the opening parenthesis part of an expression (#7430)

Changelog for v3.40.0
---------------------

* chore: officially support PHP 8.3 (#7466)
* chore: update deps (#7471)
* CI: add --no-update while dropping non-compat `facile-it/paraunit` (#7470)
* CI: automate --ignore-platform-req=PHP (#7467)
* CI: bump actions/github-script to v7 (#7468)
* CI: move humbug/box out of dev-tools/composer.json (#7472)

Changelog for v3.39.1
---------------------

* DX: introduce SwitchAnalyzer (#7456)
* fix: NoExtraBlankLinesFixer - do not remove blank line after `? : throw` (#7457)
* fix: OrderedInterfacesFixer - do not comment out interface (#7464)
* test: Improve `ExplicitIndirectVariableFixerTest` (#7451)

Changelog for v3.39.0
---------------------

* chore: Add support for Symfony 7 (#7453)
* chore: IntegrationTest - move support of php< requirement to main Integration classes (#7448)
* CI: drop Symfony ^7 incompat exceptions of php-coveralls and cli-executor (#7455)
* CI: early compatibility checks with Symfony 7 (#7431)
* docs: drop list.rst and code behind it (#7436)
* docs: remove Gitter mentions (#7441)
* DX: Ability to run Fixer on PHP8.3 for development (#7449)
* DX: describe command - for rules, list also sets that are including them (#7419)
* DX: Docker clean up (#7450)
* DX: more usage of spaceship operator (#7438)
* DX: Put `Preg`'s last error message in exception message (#7443)
* feat: Introduce `@PHP83Migration` ruleset and PHP 8.3 integration test (#7439)
* test: Improve `AbstractIntegrationTestCase` description (#7452)

Changelog for v3.38.2
---------------------

* docs: fix 'Could not lex literal_block as "php". Highlighting skipped.' (#7433)
* docs: small unification between FixerDocumentGenerator and ListDocumentGenerator (#7435)
* docs: unify ../ <> ./../ (#7434)

Changelog for v3.38.1
---------------------

* chore: ListSetsCommand::execute - add missing return type (#7432)
* chore: PHPStan - add counter to dataProvider exception, so we do not increase the tech debt on it (#7425)
* CI: Use `actions/checkout` v4 (#7423)
* fix: ClassAttributesSeparationFixer - handle Disjunctive Normal Form types parentheses (#7428)
* fix: Remove all variable names in `@var` callable signature (#7429)
* fix: Satisfy `composer normalize` (#7424)

Changelog for v3.38.0
---------------------

* chore: upgrade phpstan (#7421)
* CI: add curl and mbstring to build php (#7409)
* CI: cache dev-tools/bin (#7416)
* CI: Composer - move prefer-stable to file config (#7406)
* CI: conditionally install flex (#7412)
* CI: dev-tools/build.sh - no need to repeat 'prefer-stable', but let's use '--no-scripts' (#7408)
* CI: Do not run post-autoload-dump on Composer install (#7403)
* CI: general restructure (#7407)
* CI: GitHub Actions - use actions/cache for Composer in composite action (#7415)
* CI: Improve QA process - supplement (#7411)
* CI: prevent Infection plugins during build time, as we do not use it (#7422)
* CI: simplify setup-php config (#7404)
* DX: Do not mark as stale issues/PRs with milestone assigned (#7398)
* DX: Improve QA process (#7366)
* feat: phpDoc to property/return/param Fixer - allow fixing mixed on PHP >= 8 (#6356)
* feat: phpDoc to property/return/param Fixer - allow fixing union types on PHP >= 8 (#6359)
* feat: Support for array destructuring in `array_indentation` (#7405)
* feat: `@Symfony` - keep Annotation,NamedArgumentConstructor,Target annotations as single group (#7399)
* fix(SelfAccessorFixer): do not touch references inside lambda and/or arrow function (#7349)
* fix: long_to_shorthand_operator - mark as risky fixer (#7418)
* fix: OrderedImportsFixer - handle non-grouped list of const/function imports (#7397)

Changelog for v3.37.1
---------------------

* docs: config file - provide better examples (#7396)
* docs: config file - provide better link to Finder docs (#6992)

Changelog for v3.37.0
---------------------

* feat: add parallel cache support (#7131)

Changelog for v3.36.0
---------------------

* chore: disable `infection-installer` plugin, as we do not use `infection/*` yet (#7391)
* chore: Run dev-tools on PHP 8.2 (#7389)
* CI: Run Symfony 6 compat check on PHP 8.1 (#7383)
* CI: use fast-linter when calculating code coverage (#7390)
* docs: extend example for nullable_type_declaration (#7381)
* DX: FixerFactoryTest - make assertion failing msg more descriptive (#7387)
* feat: PhpdocSummaryFixer - support lists in description (#7385)
* feat: PSR12 - configure unary_operator_spaces (#7388)
* feat: StatementIndentationFixer - support comment for continuous control statement (#7384)

Changelog for v3.35.1
---------------------

* fix: Mark `PhpdocReadonlyClassCommentToKeywordFixer` as risky (#7372)

Changelog for v3.35.0
---------------------

* chore: Autoreview: test all formats are listed in `usage.rst` (#7357)
* chore: no need for `phpunitgoodpractices/traits` anymore (#7362)
* chore: Rename `indexes` to `indices` (#7368)
* chore: stop using `phpunitgoodpractices/traits` (#7363)
* chore: typo (#7367)
* docs: Sort options in documentation (#7345)
* feat(PhpdocReadonlyClassCommentToKeywordFixer): Introduction (#7353)
* feat: Ability to keep/enforce leading `\` when in global namespace (#7186)
* feat: Update `@PER-CS2.0` to match short closure space (#6970)
* feat: use `ordered_types` in `@PhpCsFixer` (#7361)
* fix(SingleLineThrowFixer): fixer goes out of range on close tag (#7369)

Changelog for v3.34.1
---------------------

* deps: revert "prevent using PHPCSFixer along with unfinalize package (#7343)" (#7348)

Changelog for v3.34.0
---------------------

* feat: Introduce `check` command (alias for `fix --dry-run`) (#7322)

Changelog for v3.33.0
---------------------

* feat: Introduce `native_type_declaration_casing` fixer (#7330)

Changelog for v3.32.0
---------------------

* deps: Prevent using PHPCSFixer along with `unfinalize` package (#7343)
* feat: Deprecate `CompactNullableTypehintFixer` and proxy to `CompactNullableTypeDeclarationFixer` (#7339)
* feat: Deprecate `CurlyBracesPositionFixer` and proxy to `BracesPositionFixer` (#7334)
* feat: Deprecate `NewWithBracesFixer` and proxy to `NewWithParenthesesFixer` (#7331)
* feat: Deprecate `NoUnneededCurlyBracesFixer` and proxy to `NoUnneededBracesFixer` (#7335)
* feat: Rename `CurlyBraceTransformer` to `BraceTransformer` (#7333)

Changelog for v3.31.0
---------------------

* chore: Use type declaration instead of type hint (#7338)
* feat: Introduce `attribute_placement` option for `MethodArgumentSpaceFixer` (#7320)
* fix: Adjust wording related to deprecations (#7332)
* fix: Correct deprecation header in rules' docs (#7337)
* fix: Replace mention of bracket with parenthesis (#7336)
* fix: `FunctionToConstantFixer` should run before `NativeConstantInvocationFixer` (#7344)

Changelog for v3.30.0
---------------------

* feat: Introduce `AttributeEmptyParenthesesFixer` (#7284)
* fix(method_argument_space): inject new line after trailing space on current line (#7327)
* fix(`YodaStyleFixer`): do not touch `require(_once)`, `include(_once)` and `yield from` statements (#7325)
* fix: illegal offset type on file-wide return in `ReturnToYieldFromFixer` (#7318)

Changelog for v3.29.0
---------------------

* chore: fix TODO tasks about T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG support (#7316)
* feat(`@PhpCsFixer:risky`): use newest `@PER-CS:risky` instead of locked `@PER-CS2.0:risky` (#7323)
* feat: Introduce `@PER-CS` ruleset (#7321)
* fix: priority issue between array_syntax and space after rules (#7324)

Changelog for v3.28.0
---------------------

* chore(prlint): allow for 'deps' type (#7304)
* CI(prlint): allow for special chars in parentheses (#7308)
* deps(dev-tools): update dev-tools (#7309)
* DX: Bump XDebug version in Docker services (#7300)
* feat(`@PER-CS2.0`): Add `concat_space` to the ruleset (#7302)

Changelog for v3.27.0
---------------------

* docs: cleanup old mention of `--show-progress=estimating` in docs (#7287)
* DX: add Composer script for applying CS fixes in parallel (#7274)
* feat: Clone PER-CS1.0 to PER-CS2.0 to prepare for adding new rules (#7249)
* feat: Introduce `LongToShorthandOperatorFixer` (#7295)
* feat: Mark PER-CS v1 as deprecated (#7283)
* feat: Move `single_line_empty_body` to `@PER-CS2.0` (#7282)
* fix: Priorities for fixers related to curly braces, empty lines and trailing whitespace (#7296)
* fix: `OrderedTraitsFixer` - better support for multiple traits in one `use` statement (#7289)

Changelog for v3.26.1
---------------------

* fix: Handle superfluous asterisk in `no_superfluous_phpdoc_tags` (#7279)

Changelog for v3.26.0
---------------------

* chore(checkbashisms): update to 2.23.6 (#7276)
* chore(phpstan): reduce baseline (#7275)
* feat: Add `single_line_empty_body` to `@PhpCsFixer` (#7266)
* fix(YieldFromArrayToYieldsFixer): mark as Risky (#7272)
* fix(YieldFromArrayToYieldsFixer): skip touching empty array (#7273)
* test: Introduce common way of creating fake Composer project in `InstallViaComposerTest` (#7265)

Changelog for v3.25.1
---------------------

* fix: PhpdocTypesFixer - do not crash for type followed by braces/brackets/chevrons/parentheses (#7268)

Changelog for v3.25.0
---------------------

* feat: Remove Doctrine dependencies (#7263)

Changelog for v3.24.0
---------------------

* chore: apply CS (#7240)
* chore: apply static_lambda rule (#7239)
* chore: Improve template for creating new issue (#7255)
* CI: Conventional Commits support in PRLint config (#7037)
* CI: Remove Travis leftovers (#7259)
* docs: Add information about installing Fixer as dev dependency (#7129)
* docs: document composer script aliases (#7230)
* DX: Add script for running Composer Require Checker (#7252)
* DX: composer script aliases - ensure templated description (#7235)
* DX: composer-script - count PHPMD as static-analysis (#7231)
* DX: do not allow version specific code sample with minimum PHP version lower than the lowest supported one (#7207)
* DX: ensure version specific code samples are suitable for at least 1 supported PHP version (#7212)
* DX: Improve contributing guide (#7241)
* DX: More descriptive stale messages (#7236)
* feat(@PhpCsFixer:risky): add static_lambda rule (#7237)
* feat: Add literal separator support for `integer_literal_case` (#7081)
* feat: Configurable case sensitivity for more ordering fixers (#7021)
* feat: Support for attributes in `method_argument_space` (#7242)
* fix: import detection for attributes at `NoUnusedImportsFixer` (#7246)
* fix: `no_superfluous_phpdoc_tags` with `null` phpdoc (#7234)
* fix: `phpdoc_types` must not lowercase literal types (#7108)
* test: Add static methods from PHPUnit 9.6.11 (#7243)

Changelog for v3.23.0
---------------------

* bug: BlankLineBeforeStatementFixer - do not enforce/add a blank line when there is a blank line between the comment and the statement already (#7190)
* bug: Fix detecting classy invocation in catch (#7191)
* bug: Fix names resolving in `no_superfluous_phpdoc_tags` fixer (#7189)
* bug: Fix various bugs in `FullyQualifiedStrictTypesFixer` fixer (#7188)
* bug: Fixed line between general script documentation and require (#7177)
* bug: Support annotations with arguments in `FinalInternalClassFixer` (#7160)
* bug: YieldFromArrayToYieldsFixer - fix for `yield from` after `}` (#7169)
* bug: YieldFromArrayToYieldsFixer - fix handling the comment before the first array element (#7193)
* bug: `HeaderCommentFixer` must run before `BlankLinesBeforeNamespaceFixer` (#7205)
* bug: `NoUselessReturnFixer` must run before `SingleLineEmptyBodyFixer` (#7226)
* bug: `PhpdocInlineTagNormalizerFixer` - do not break tags (#7227)
* docs: Add allowed values of tags in the `phpdoc_align` (#7120)
* docs: Add extra information for GitLab reporter's integration with GitLab Code Quality (#7172)
* docs: Change the single backticks to double in description of the rules option (#7173)
* docs: Condensed output for rule sets' list that fixer is included in (#7182)
* docs: Improve contributing guide (#7204)
* docs: `MethodArgumentSpaceFixer` - mention PSR in Fixer definition (#7157)
* DX: add first auto-review tests for composer.json file (#7210)
* DX: add `YieldFromArrayToYieldsFixer` to `PhpCsFixer` set (#7115)
* DX: Allow OS conditions for integration tests (#7161)
* DX: Apply current CS rules (#7178)
* DX: Apply suggestions from PR 7210 (#7213)
* DX: apply `ReturnToYieldFromFixer` (#7181)
* DX: Do not mark "long term ideas" as stale (#7206)
* DX: enable `HeredocIndentationFixer` for the codebase (#7195)
* DX: enable `UseArrowFunctionsFixer` for the codebase (#7194)
* DX: few phpstan fixes (#7208)
* DX: fix contravariant types in PHPDocs (#7167)
* DX: Fix detecting trailing spaces (#7216)
* DX: Fix some PHPStan issues (#7180)
* DX: Get rid of deprecation warnings in Mess Detector (#7215)
* DX: Improve Composer scripts (#7214)
* DX: Improve Mess Detector Integration (#7224)
* DX: Introduce Composer scripts as common DX (#6839)
* DX: refactor `ErrorOutputTest` (#7183)
* DX: remove unnecessary arrays from data providers (#7170)
* DX: update `CurlyBracesPositionFixer` code samples (#7198)
* DX: update `HeredocIndentationFixer` code samples (#7197)
* DX: update `PhpdocToReturnTypeFixer` code samples (#7199)
* feature: add at least one space around binary operators (#7175)
* feature: BlankLineBeforeStatementFixer - take into account comment before statement (#7166)
* feature: Introduce `ReturnToYieldFromFixer` (#7168)
* feature: Introduce `SpacesInsideParenthesesFixer` (#5709)
* feature: Support array destructuring in `trim_array_spaces` (#7218)
* feature: `BlankLineBeforeStatementFixer` - skip enum cases (#7203)
* minor: more arrow function usage (#7223)
* minor: PhpdocAlignFixerTest - convert CUSTOM tags test to not rely on non-custom tag from TAGS_WITH_NAME (#7209)
* minor: use JSON_THROW_ON_ERROR for trivial cases (#7221)
* minor: use more spread operator (#7222)

Changelog for v3.22.0
---------------------

* DX: add proper test for `SelfAccessorFixer` must run before `SelfAccessorFixer` (#7153)
* DX: FixerFactoryTest - apply CS (#7154)
* feature: Introduce `PhpUnitDataProviderReturnTypeFixer` (#7156)
* feature: Introduce `YieldFromArrayToYieldsFixer` (#7114)

Changelog for v3.21.3
---------------------

* Revert "DX: encourage to provide wider description" (#7155)

Changelog for v3.21.2
---------------------

* docs: check format of FixerDefinition::getDescription() (#7127)
* DX: add phpstan/phpstan-strict-rules (#7143)
* DX: allow for progressive cache (#7132)
* DX: Copy-pasteable `class::getPriority` for phpDoc diffs (#7148)
* DX: do not allow linebreak at the beginning of code sample (#7126)
* DX: encourage to provide wider description (#7128)
* DX: fix function calls (#7136)
* DX: fix PHPDoc types issues (#7135)
* DX: improve `Tokens` checking for found tokens (#7139)
* DX: Make `AbstractFixerTestCase::getTestFile()` final (#7116)
* DX: make `array_search` call strict (#7145)
* DX: remove `empty` calls (#7138)
* DX: store cache to file only if content will get modified (#7151)
* DX: unify Preg:match in logical conditions (#7146)
* DX: use booleans in conditions (#7149)
* DX: Use ParaUnit to speed up tests (#6883)
* DX: Use relative fixture path as integration test case's name (#7147)
* DX: use strict assertions (#7144)
* DX: `AbstractIntegrationTestCase::provideIntegrationCases` - yield over array, better typehinting (#7150)

Changelog for v3.21.1
---------------------

experimental release

* Require PHP ^8.0.1

Changelog for v3.21.0
---------------------

* bug: Fix and enhance Gitlab reporter (#7089)
* bug: Import with different case must not be removed by non-risky fixer (#7095)
* bug: ordered imports fixer top group only (#7023)
* bug: `FinalPublicMethodForAbstractClassFixer` - fix for readonly classes (#7123)
* DX: do not nest ".editorconfig" files (#7112)
* DX: exclude Dockerfile from dist (#7113)
* DX: fix checkbashisms installation (#7102)
* DX: fix Smoke tests for various git default branch name (#7119)
* DX: Fix `FileRemovalTest` (do not fail when running it standalone) (#7104)
* DX: Progress output refactor (#6848)
* DX: Rename abstract test classes to `*TestCase` convention (#7100)
* DX: test all PHPUnit migration sets (#7107)
* DX: [Docker] Distinguish Alpine version between PHP versions (#7105)
* feature: Create cache path if it does not exist (#7109)
* feature: Introduce `NullableTypeDeclarationFixer` (#7002)
* feature: Introduce `TypeDeclarationSpacesFixer` (#7001)
* feature: `BlankLineBetweenImportGroupsFixer` - keep indent (#7122)
* minor: Parse callable using full phpdoc grammar (#7094)
* minor: PHP8.3 const type tokenizing (#7055)

Changelog for v3.20.0
---------------------

* DX: fix priority of `FinalClassFixer` (#7091)
* DX: use FAST_LINT_TEST_CASES=1 for CI run on macOS (#7092)
* feature: SingleLineEmptyBodyFixer - support interfaces, traits and enums (#7096)
* feature: `NullableTypeDeclarationForDefaultNullValue` - support for nullability in union types (#5819)

Changelog for v3.19.2
---------------------

* bug: NoMultipleStatementsPerLineFixer must run before CurlyBracesPositionFixer (#7087)
* bug: PhpdocAddMissingParamAnnotationFixer - fix for promoted properties (#7090)
* DX: fix priority of SingleBlankLineBeforeNamespaceFixer (#7088)
* minor: Parse all phpdoc types using full grammar (#7010)

Changelog for v3.19.1
---------------------

* bug: CurlyBracesPositionFixer must run before StatementIndentationFixer (#7085)

Changelog for v3.19.0
---------------------

* bug: SelfAccessorFixer - fix for union types (#7080)
* DX: add `php_unit_data_provider_name` to `@PhpCsFixer:risky` set (#7069)
* DX: make data providers return type "iterable" (#7072)
* DX: rename tests and data providers (#7070)
* feature: Introduce `PhpUnitDataProviderNameFixer` (#7057)

Changelog for v3.18.0
---------------------

* bug:  Fix tokenizing of type hints (#7054)
* bug: CompactNullableTypehintFixer - fix for whitespace between `?` and `static` (#6993)
* bug: consider function modifiers for `statement_indentation` (#6978)
* bug: Exclude `$this` from `TernaryToNullCoalescingFixer` (#7052)
* bug: False positive on used imports when docblock includes it with mismatching case (#6909)
* bug: Fix chained calls semicolon indent in switch case (#7045)
* bug: Fix multiline_whitespace_before_semicolons for echo tags (#7019)
* bug: Fix phpDoc align when there is inconsistent spacing after comment star (#7012)
* bug: Fix phpDoc parsing without PCRE JIT (#7031)
* bug: Fix PhpdocVarWithoutNameFixer with Closure with $this (#6979)
* bug: Fix `return_assignment` not formatting when variables are used in `catch` and `finally` (#6960)
* bug: Fix `TypeExpression::allowsNull()` with nullable (#7000)
* bug: Improve definition of conflicting fixers (#7066)
* bug: LambdaNotUsedImportFixer - fix for anonymous class with a string argument (#6972)
* bug: ListFilesCommand - fix computing of relative path (#7028)
* bug: make `php_unit_namespaced` less greedy (#6952)
* bug: PhpdocToCommentFixer - fix for PHPDoc before fn (#6973)
* bug: Restructure PER-CS rule sets (#6707)
* bug: SelfStaticAccessor - fix static access inside enums (#7024)
* bug: SingleSpaceAroundConstructFixer - fix more cases involving `static` (#6995)
* bug: `FullyQualifiedStrictTypesFixer` - fix shortening when namespace is not empty and import exists (#7027)
* bug: `NoUnneededControlParenthesesFixer` PHP8.0 null-safe operator (#7056)
* bug: `PhpdocToCommentFixer` support for enum cases (#7040)
* DX: add more tests to CommentsAnalyzer (#7041)
* DX: Cleanup duplicate files in finder (#7042)
* DX: ControlCaseStructuresAnalyzerTest - cleanup (#6874)
* DX: Fix warning when running test on PHP<8 (#7008)
* DX: handle `@` in PR title (#6982)
* DX: officially deprecate internal Utils anti-pattern class (#7039)
* DX: Remove Fabbot.io conditional configuration (#7038)
* DX: rename data providers (#7058)
* DX: Use `actions/stale` to handle stale issues and pull requests (#5085)
* DX: Use `Utils::naturalLanguageJoin()` in implode calls (#7032)
* feature: Add support for custom method placement in `ordered_class_elements` (#6360)
* feature: Allow case sensitive order for OrderedClassElementsFixer (#7020)
* feature: PHP8.3 - Add CT and block type for `Dynamic class constant fetch` (#7004)
* feature: Support attributes in `FinalClassFixer` (#6893)
* minor: "Callback" must not be fixed to "callback" by default (#7011)
* minor: Add `Util::naturalLanguageJoin()` (#7022)
* minor: ClassDefinitionFixer - handle attributes and `readonly` in anonymous class definitions (#7014)
* minor: FixerFactory::getFixersConflicts - better type hinting (#7044)
* minor: PHP8.3 - Fix TokensAnalyzer::isAnonymousClass support for `readonly` (#7013)
* minor: PHP8.3 - Typed class constants - handle nullable by transformer (#7009)
* minor: Reduce phpDoc type parser complexity from O(n^2) to O(nlog(n)) (#6988)
* minor: ReturnAssignmentFixer - Better handling of anonymous classes (#7015)
* minor: Transfer `HelpCommand::toString()` to `Utils` (#7034)
* minor: Unify "blank lines before namespace" fixers (#7053)
* minor: `SelfStaticAccessorFixer` improvements for enums (#7026)
* minor: `SingleSpaceAroundConstructFixer` - support space before `as` (#7029)
* minor: `UseArrowFunctionsFixer` - run before `FunctionDeclarationFixer` (#7065)

Changelog for v3.17.0
---------------------

* bug: Allow string quote to be escaped within phpdoc constant (#6798)
* bug: ConfigurationResolver - fix running without cache (#6915)
* bug: Fix array/object shape phpdoc type parse (#6962)
* bug: Fix FullyQualifiedStrictTypesFixer common prefix bug (#6898)
* bug: Fix non-parenthesized callable return type parse (#6961)
* bug: Fix parsing of edge cases phpdoc types (#6977)
* bug: FullyQualifiedStrictTypesFixer - fix for FQCN type with class with the same name being imported (#6923)
* bug: GroupImportFixer - support for aliased imports (#6951)
* bug: MultilineWhitespaceBeforeSemicolonsFixer - fix chained calls (#6926)
* bug: MultilineWhitespaceBeforeSemicolonsFixer - fix for discovering multi line calls (#6938)
* bug: NoBreakCommentFixer - fix for nested match (#6899)
* bug: NoExtraBlankLinesFixer - fix for attribute in abstract function (#6920)
* bug: PhpdocTypesFixer - handle types with no space between type and variable (#6922)
* bug: PhpUnitMockShortWillReturnFixer - fix for trailing commas (#6900)
* bug: StatementIndentationFixer - fix comments at the end of if/elseif/else blocks (#6918)
* bug: StatementIndentationFixer - fix for multiline arguments starting with "new" keyword (#6913)
* bug: StatementIndentationFixer - fix for multiline arguments starting with "new" keyword preceded by class instantiation (#6914)
* bug: VoidReturnFixer - fix for intervening attributes (#6863)
* docs: improve code samples for MultilineWhitespaceBeforeSemicolonsFixer (#6919)
* docs: improve cookbook (#6880)
* DX: add cache related tests (#6916)
* DX: Apply `self_static_accessor` fixer to the project (again) (#6927)
* DX: cancel running builds on subsequent pushes in CI (#6940)
* DX: convert more `static` to `self` assert calls (#6931)
* DX: fix GitHub Actions errors and warnings (#6917)
* DX: fix Unsafe call to private method errors reported by PHPStan (#6879)
* DX: Improve performance of FunctionsAnalyzer (#6939)
* DX: improve test method names to avoid confusion (#6974)
* DX: Include self_static_accessor fixer in PhpCsFixer set (#6882)
* DX: make data providers static with straight-forward changes (#6907)
* DX: Mark Tokens::getNamespaceDeclarations as @internal (#6949)
* DX: PHPStan improvements (#6868)
* DX: refactor PhpdocAlignFixerTest (#6925)
* DX: Remove @inheritdoc PHPDoc (#6955)
* DX: Run AutoReview tests only once (#6889)
* DX: simplify EncodingFixer (#6956)
* DX: update Symfony rule set (#6958)
* DX: Use $tokens->getNamespaceDeclarations() to improve performance (#6942)
* DX: use force option for php_unit_data_provider_static in PHPUnit 10.0 migration set (#6908)
* DX: use only PHP modules that are required (#6954)
* DX: use PHPUnit's "requires" instead of "if" condition (#6975)
* feature: Add align_multiline_comment rule to @Symfony (#6875)
* feature: Add no_null_property_initialization rule to @Symfony (#6876)
* feature: Add operator_linebreak rule to @Symfony (#6877)
* feature: add SingleLineEmptyBodyFixer (#6933)
* feature: DescribeCommand - allow describing custom fixers (#6957)
* feature: Introduce `OrderedTypesFixer` (#6571)
* feature: Order of PHPDoc @param annotations (#3909)
* feature: Parse parenthesized & conditional phpdoc type (#6796)
* feature: PhpUnitInternalClassFixer - add empty line before PHPDoc (#6924)
* feature: [PhpdocAlignFixer] Add support for every tag (#6564)
* minor: align NoSuperfluousPhpdocTagsFixer with actual Symfony configuration (#6953)
* minor: do not add empty line in PHPDoc when adding annotation in PHPUnit class (#6928)
* minor: PhpdocAlignFixer - support cases with type and variable separated with no space (#6921)
* minor: PhpdocSeparationFixer - add integration tests (#6929)
* minor: update PHPStan (to fix CI on master branch) (#6901)
* minor: Use single Dockerfile with multiple build targets (#6840)

Changelog for v3.16.0
---------------------

* bug: ControlStructureBracesFixer - handle closing tag (#6873)
* bug: CurlyBracesPositionFixer - fix for callable return type (#6855)
* bug: CurlyBracesPositionFixer - fix for DNF types (#6859)
* bug: Fix MultilineWhitespaceBeforeSemicolonsFixer (#5126)
* docs: Fix rule description (#6844)
* DX: fix checkbashisms installation (#6843)
* DX: make data providers static for fixer's tests (#6860)
* DX: refactor PHPUnit fixers adding class-level annotation to use shared code (#6756)
* DX: unify option's descriptions (#6856)
* feature: AbstractPhpUnitFixer - support attribute detection in docblock insertion (#6858)
* feature: add "force" option to PhpUnitDataProviderStaticFixer (#6757)
* feature: introduce single_space_around_construct, deprecate single_space_after_construct (#6857)
* feature: PhpUnitTestClassRequiresCoversFixer - support single-line PHPDocs (#6847)
* minor: Deprecate BracesFixer (#4885)
* minor: Fix autocompletion for `Tokens::offsetGet()` (#6838)
* minor: PHP8.2 Docker runtime (#6833)
* minor: Use Composer binary-only images instead of installer script (#6834)

Changelog for v3.15.1
---------------------

* bug: BinaryOperatorSpacesFixer - fix for static in type (#6835)
* bug: BinaryOperatorSpacesFixer - fix parameters with union types passed by reference (#6826)
* bug: NoUnusedImportsFixer - fix for splat operator (#6836)
* DX: fix CI (#6837)
* feature: Support for type casing in arrow functions (#6831)
* minor: fix CI on PHP 8.3 (#6827)

Changelog for v3.15.0
---------------------

* bug: VisibilityRequiredFixer - handle DNF types (#6806)
* DX: officially enable 8.2 support (#6825)

Changelog for v3.14.5
---------------------

* bug: EmptyLoopBodyFixer must keep comments inside (#6800)
* bug: FunctionsAnalyzer - fix detecting global function (#6792)
* bug: NativeFunctionTypeDeclarationCasingFixer - do not require T_STRING present in code (#6812)
* bug: PhpdocTypesFixer - do not change case of array keys (#6810)
* bug: PhpUnitTestAnnotationFixer - do not break single line @depends (#6824)
* docs: Add supported PHP versions section to the README (#6768)
* docs: drop Atom from readme, due to it's sunsetting (#6778)
* DX: Add composer keywords (#6781)
* DX: update PHPStan to 1.10.3 (#6805)
* feature: [PHP8.2] Support for readonly classes (#6745)
* minor: add custom tokens for Disjunctive Normal Form types parentheses (#6823)
* minor: PHP8.2 - handle union and intersection types for DNF types (#6804)
* minor: PHP8.2 - support property in const expressions (#6803)

Changelog for v3.14.4
---------------------

* bug: CurlyBracesPositionFixer - fix for open brace not preceded by space and followed by a comment (#6776)
* docs: drop license end year (#6767)
* DX: use numeric_literal_separator (#6766)
* feature: Allow installation of `sebastian/diff:^5.0.0` (#6771)

Changelog for v3.14.3
---------------------

* DX: Drop doctrine/annotations 1, allow doctrine/lexer 3 (#6730)

Changelog for v3.14.2
---------------------

* DX: Drop support for doctrine/lexer 1 (#6729)

Changelog for v3.14.1
---------------------

* DX: Allow doctrine/annotations 2 (#6721)

Changelog for v3.14.0
---------------------

* bug: Fix indentation for comment at end of function followed by a comma (#6542)
* bug: Fix PHPDoc alignment fixer containing callbacks using `\Closure` (#6746)
* bug: Fix type error when using paths intersection mode (#6734)
* bug: PhpdocSeparationFixer - Make groups handling more flexible (#6668)
* docs: make bug_report.md template more explicit (#6736)
* docs: PhpUnitTestCaseIndicator - fix docs (#6727)
* DX: apply CS (#6759)
* DX: bump doctrine/annotations to prevent installing version with unintentional BC break (#6739)
* DX: update deps (#6760)
* DX: upgrade dev-tools/composer.json (#6737)
* DX: upgrade PHPStan to 1.9.7 (#6741)
* feature: Add php 7.4 types to Cookbook docs (#6763)
* feature: add PhpUnitDataProviderStaticFixer (#6702)
* feature: binary_operator_spaces - Revert change about => alignment and use option instead (#6724)
* feature: make OrderedInterfacesFixer non-risky (#6722)
* feature: OctalNotationFixer - support _ notation (#6762)
* fix: enum case "PARENT" must not be renamed (#6732)
* minor: Follow PSR12 ordered imports in Symfony ruleset (#6712)
* minor: improve rule sets order (#6738)

Changelog for v3.13.2
---------------------

* bug: Fix type error when using paths intersection mode (#6734)

Changelog for v3.13.1
---------------------

* bug: Align all the arrows inside the same array (#6590)
* bug: Fix priority between `modernize_types_casting` and `no_unneeded_control_parentheses` (#6687)
* bug: TrailingCommaInMultilineFixer - do not add trailing comma when there is no break line after last element (#6677)
* docs: Fix docs for disabled rules in rulesets (#6679)
* docs: fix the cookbook_fixers.rst (#6672)
* docs: Update installation recommended commands for `mkdir` argument (`-p` insteadof `--parents`). (#6689)
* Make static data providers that are not using dynamic calls (#6696)
* minor: displaying number of checked files (#6674)

Changelog for v3.13.0
---------------------

* bug: BracesFixer - Fix unexpected extra blank line (#6667)
* bug: fix CI on master branch (#6663)
* bug: IsNullFixer - handle casting (#6661)
* docs: feature or bug (#6652)
* docs: Use case insensitive sorting for options (#6666)
* docs: [DateTimeCreateFromFormatCallFixer] Fix typos in the code sample (#6671)
* DX: update cli-executor (#6664)
* DX: update dev-tools (#6665)
* feature: Add global_namespace_import to @Symfony ruleset (#6662)
* feature: Add separate option for closure_fn_spacing (#6658)
* feature: general_phpdoc_annotation_remove - allow add case_sensitive option (#6660)
* minor: AllowedValueSubset - possible values are sorted (#6651)
* minor: Use md5 for file hashing to reduce possible collisions (#6597)

Changelog for v3.12.0
---------------------

* bug: SingleLineThrowFixer - Handle throw expression inside block (#6653)
* DX: create TODO to change default ruleset for v4 (#6601)
* DX: Fix SCA findings (#6626)
* DX: HelpCommand - fix docblock (#6584)
* DX: Narrow some docblock types (#6581)
* DX: Remove redundant check for PHP <5.2.7 (#6620)
* DX: Restore PHPDoc to type rules workflow step (#6615)
* DX: SCA - scope down types (#6630)
* DX: Specify value type in iterables in tests (#6594)
* DX: Test on PHP 8.2 (#6558)
* DX: Update GitHub Actions (#6606)
* DX: Update PHPStan (#6616)
* feature: Add `@PHP82Migration` ruleset (#6621)
* feature: ArrayPushFixer now fix short arrays (#6639)
* feature: NoSuperfluousPhpdocTagsFixer - support untyped and empty annotations in phpdoc (#5792)
* feature: NoUselessConcatOperatorFixer - Introduction (#6447)
* feature: Support for constants in traits (#6607)
* feature: [PHP8.2] Support for new standalone types (`null`, `true`, `false`) (#6623)
* minor: GitHub Workflows security hardening (#6644)
* minor: prevent BC break in ErrorOutput (#6633)
* minor: prevent BC break in Runner (#6634)
* minor: Revert "minor: prevent BC break in Runner" (#6637)
* minor: Update dev tools (#6554)

Changelog for v3.11.0
---------------------

* bug: DateTimeCreateFromFormatCallFixer - Mark as risky (#6575)
* bug: Do not treat implements list comma as array comma (#6595)
* bug: Fix MethodChainingIndentationFixer with arrow functions and class instantiation (#5587)
* bug: MethodChainingIndentationFixer - Fix bug with attribute access (#6573)
* bug: NoMultilineWhitespaceAroundDoubleArrowFixer - fix for single line comment (#6589)
* bug: TypeAlternationTransformer - TypeIntersectionTransformer - Bug: handle attributes (#6579)
* bug: [BinaryOperatorFixer] Fix more issues with scoped operators (#6559)
* docs: Remove `$` from console command snippets (#6600)
* docs: Remove `$` from console command snippets in documentation (#6599)
* DX: AllowedValueSubset::getAllowedValues - fix method prototype (#6585)
* DX: Narrow docblock types in FixerConfiguration (#6580)
* DX: updagte @PhpCsFixer set config for phpdoc_order rule (#6555)
* DX: Update PHPUnit config (#6566)
* feature: Introduce configurability to PhpdocSeparationFixer (#6501)
* feature: Introduce PER set (#6545)
* feature: NoTrailingCommaInSinglelineFixer - Introduction (#6529)
* feature: Support removing superfluous PHPDocs involving `self` (#6583)
* minor: NoUnneededControlParenthesesFixer - Support instanceof static cases (#6587)
* minor: PhpdocToCommentFixer - allow phpdoc comments before trait use statement. Fixes #6092 (#6565)

Changelog for v3.10.0
---------------------

* bug: Fix error in `regular_callable_call` with static property (#6539)
* bug: Fix indentation for multiline class definition (#6540)
* bug: Fix indentation for switch ending with empty case (#6538)
* bug: Fix indentation of comment at end of switch case (#6493)
* bug: PhpdocAlignFixer - fix static `@method` (#6366)
* bug: SingleSpaceAfterConstructFixer - fix handling open tag (#6549)
* bug: VisibilityRequiredFixer must run before ClassAttributesSeparationFixer (#6548)
* DX: Assert dataproviders of tests of project itself return "array" or "iterable". (#6524)
* feature: Introduce configurability to PhpdocOrderFixer (#6466)
* feature: WhitespaceAfterCommaInArrayFixer - add option "ensure_single_space" (#6527)
* minor: Add test for indentation of trait conflict resolution (#6541)
* minor: Split BracesFixer (#4884)
* minor: TrailingCommaInMultilineFixer - Add comma to multiline `new static` (#6380)

Changelog for v3.9.6
--------------------

* bug: BinaryOperatorSpacesFixer: Solve issues with scoped arrow and equal alignments (#6515)
* bug: Fix 3 weird behaviour about BinaryOperatorSpacesFixer (#6450)
* docs: Add intersection type to types_spaces rule description (#6479)
* DX: no need to use forked diff anymore (#6526)
* DX: remove unused FixerFileProcessedEvent::STATUS_UNKNOWN (#6516)
* Improve `statement_indentation` compatibility with `braces` (#6401)
* minor: add test: multi-line comments before else indented correctly. (#3573)
* minor: ReturnAssignmentFixer - Support for anonymous classes, lambda and match (#6391)

Changelog for v3.9.5
--------------------

* bug: AlternativeSyntaxAnalyzer - fix for nested else (#6495)
* bug: Fix cases related to binary strings (#6432)
* bug: Fix trailing whitespace after moving brace (#6489)
* bug: NoUnneededControlParenthesesFixer - Fix some curly close cases (#6502)
* bug: TypeColonTransformer - fix for backed enum types (#6494)
* DX: Add tests for type colon in backed enums (#6497)
* DX: Fix CI static analysis workflow (#6506)
* DX: Fix PHPStan errors (#6504)
* DX: Increase PHPStan level to 6 (#6468)
* DX: Narrow docblock types in Runner and Report (#6465)
* DX: Narrow docblock types in Tokenizer (#6293)
* minor: extract NoMultipleStatementsPerLineFixer from BracesFixer (#6458)
* minor: Let PhpdocLineSpan fixer detect docblocks when separator from token with attribute (#6343)

Changelog for v3.9.4
--------------------

* bug: Fix various indentation issues (#6480)
* bug: Fix wrong brace position after static return type (#6485)
* bug: Prevent breaking functions returning by reference (#6487)

Changelog for v3.9.3
--------------------

* bug: Fix BinaryOperatorSpacesFixer adding whitespace outside PHP blocks (#6476)
* bug: Fix brace location after multiline function signature (#6475)

Changelog for v3.9.2
--------------------

* bug: Fix indentation after control structure in switch (#6473)

Changelog for v3.9.1
--------------------

* bug: Add attributes support to `statement_indentation` (#6429)
* bug: BinaryOperatorSpacesFixer - Allow to align `=` inside array definitions (#6444)
* bug: BinaryOperatorSpacesFixer - Fix align of operator with function declaration (#6445)
* bug: ConstantCaseFixer - Do not touch enum case (#6367)
* bug: CurlyBracesPositionFixer - multiple elseifs (#6459)
* bug: Fix #6439 issue in `StaticLambda` fixer (#6440)
* bug: FullOpeningTagFixer - fix substr check for pre PHP8 (#6388)
* bug: IncrementStyleFixer - NoSpacesInsideParenthesisFixer - prio (#6416)
* bug: LambdaNotUsedImportFixer must run before MethodArgumentSpaceFixer (#6453)
* bug: MethodArgumentSpaceFixer - first element in same line, space before comma and inconsistent indent (#6438)
* bug: NoSuperfluousPhpdocTagsFixer - fix for promoted properties (#6403)
* bug: StatementIndentationFixer - Fix indentation for multiline traits use (#6402)
* bug: StrictComparisonFixer must rune before ModernizeStrposFixer (#6455)
* bug: TokensAnalyzer - fix intersection types considered as binary operator (#6414)
* DX: `ISSUE_TEMPLATE` hints to check applied rules (#6398)
* DX: Add more type hints (#6383)
* DX: Fix CI/CD issues (#6411)
* DX: cleanup test (#6410)
* DX: integrate PRLint (#6406)
* feature: BlankLineBetweenImportGroupsFixer - Introduction (#6365)
* feature: DateTimeCreateFromFormatCallFixer - Add DateTimeImmutable support (#6350)
* feature: Extract StatementIndentationFixer from BracesFixer (#5960)
* feature: ModernizeStrposFixer - fix leading backslash with yoda (#6377)
* feature: NoExtraBlankLinesFixer - Add `attributes` option - Fix support for `enum` `case` (#6426)
* feature: NoUnneededControlParenthesesFixer - Fix more cases (#6409)
* feature: NoUselessNullsafeOperatorFixer - Introduction (#6425)
* feature: OrderedTrait - Move Phpdoc with trait import (#6361)
* feature: PhpdocOrderByValueFixer - Allow sorting of mixin annotations by value (#6446)
* feature: TrailingCommaInMultiline - Add `match` support (#6381)
* minor: Allow Composer Normalize plugin (#6454)
* minor: ExplicitStringVariableFixer - Fix to PHP8.2 compat code (#6424)
* minor: Extract ControlStructureBracesFixer from BracesFixer (#6399)
* minor: NoBinaryStringFixer - Fix more cases (#6442)
* minor: NoSuperfluousPhpdocTagsFixer - Attribute handling (#6382)
* minor: PhpCsFixerSet - Update blank_line_before_statement config (#6389)
* minor: Remove unnecessary PHP version constraints (#6461)
* minor: SingleImportPerStatementFixer - fix PSR12 set (#6415)
* minor: SingleSpaceAfterConstructFixer - add option `type_colon` (#6434)
* minor: SymfonySet - Add SimpleToComplexStringVariableFixer (#6423)
* minor: Update PHPStan (#6467)
* minor: extract CurlyBracesPositionFixer from BracesFixer (#6452)

Changelog for v3.8.0
--------------------

* bug #6322 PhpdocTypesFixer - fix recognizing callable (kubawerlos)
* bug #6331 ClassReferenceNameCasingFixer - Fix false hits (SpacePossum)
* bug #6333 BinaryOperatorSpacesFixer - Fix for alignment in `elseif` (paulbalandan, SpacePossum)
* bug #6337 PhpdocTypesFixer - fix recognising callable without return type (kubawerlos)
* feature #6286 DateTimeCreateFromFormatCallFixer - Introduction (liquid207)
* feature #6312 TypesSpacesFixer - add option for CS of catching multiple types of exceptions (SpacePossum)
* minor #6326 Bump migration sets used to PHP7.4 (SpacePossum)
* minor #6328 DX: .gitignore ASC file (keradus)

Changelog for v3.7.0
--------------------

* bug #6112 [BinaryOperatorSpacesFixer] Fix align of `=` inside calls of methods (VincentLanglet)
* bug #6279 ClassReferenceNameCasingFixer - Fix for double arrow (SpacePossum)
* bug #6280 Fix bunch of enum issues (SpacePossum)
* bug #6283 ClassReferenceNameCasingFixer - detect imports (SpacePossum)
* feature #5892 NewWithBracesFixer - option to remove braces (jrmajor)
* feature #6081 Allow multiline constructor arguments in an anonymous classes (jrmajor, SpacePossum)
* feature #6274 SingleLineCommentSpacingFixer - Introduction (SpacePossum)
* feature #6300 OrderedClassElementsFixer - handle enums (gharlan)
* feature #6304 NoTrailingCommaInSinglelineFunctionCallFixer - Introduction (SpacePossum)
* minor #6277 Add `is_scalar`, `sizeof`, `ini_get` in list of compiled functions (jderusse)
* minor #6284 ClassReferenceNameCasingFixer - Update doc (SpacePossum)
* minor #6289 PHP7.4 - clean up tests (SpacePossum)
* minor #6290 PHP7.4 - properties types (SpacePossum)
* minor #6291 PHP7.4 - remove run time checks (SpacePossum)
* minor #6292 PhpUnitDedicateAssertFixer - Fix more count cases (SpacePossum)
* minor #6294 PhpUnitDedicateAssertFixer - add assertInstanceOf support (SpacePossum)
* minor #6295 PhpUnitTestCaseIndicator - Check if PHPUnit-test class extends another class (SpacePossum)
* minor #6298 Fix checkbashisms download ans SCA violations (SpacePossum)
* minor #6301 BracesFixer - handle enums (gharlan)
* minor #6302 Bump checkbashisms version (kubawerlos)
* minor #6303 PHP8 - Utilize "get_debug_type" (SpacePossum)
* minor #6316 bump xdebug-handler (SpacePossum)
* minor #6327 bump polyfills (SpacePossum)

Changelog for v3.6.0
--------------------

* bug #6063 PhpdocTypesOrderFixer - Improve nested types support (ruudk, julienfalque)
* bug #6197 FullyQualifiedStrictTypesFixer - fix same classname is imported from … (SpacePossum)
* bug #6241 NoSuperfluousPhpdocTagsFixer - fix for reference and splat operator (kubawerlos)
* bug #6243 PhpdocTypesOrderFixer - fix for intersection types (kubawerlos)
* bug #6254 PhpUnitDedicateAssertFixer - remove `is_resource`. (drupol)
* bug #6264 TokensAnalyzer - fix isConstantInvocation detection for multiple exce… (SpacePossum)
* bug #6265 NullableTypeDeclarationForDefaultNullValueFixer - handle "readonly" a… (SpacePossum)
* bug #6266 SimplifiedIfReturnFixer - handle statement in loop without braces (SpacePossum)
* feature #6262 ClassReferenceNameCasingFixer - introduction (SpacePossum)
* feature #6267 NoUnneededImportAliasFixer - Introduction (SpacePossum)
* minor #6199 HeaderCommentFixer - support monolithic files with shebang (kubawerlos, keradus)
* minor #6231 Fix priority descriptions and tests. (SpacePossum)
* minor #6237 DX: Application - better display version when displaying gitSha (keradus)
* minor #6242 Annotation - improve on recognising types with reference and splat operator (kubawerlos)
* minor #6250 Tokens - optimize cache clear (SpacePossum)
* minor #6269 Docs: redo warnings in RST docs to fix issue on website docs (keradus)
* minor #6270 ClassReferenceNameCasingFixer - Add missing test cases for catch (SpacePossum)
* minor #6273 Add priority test (SpacePossum)

Changelog for v3.5.0
--------------------

* bug #6058 Fix `Tokens::insertSlices` not moving around all affected tokens (paulbalandan, SpacePossum)
* bug #6160 NonPrintableCharacterFixer - fix for when removing non-printable character break PHP syntax (kubawerlos)
* bug #6165 DeclareEqualNormalizeFixer - fix for declare having multiple directives (kubawerlos)
* bug #6170 NonPrintableCharacterFixer - fix for string in single quotes, having non-breaking space, linebreak, and single quote inside (kubawerlos)
* bug #6181 UseTransformer - Trait import in enum fix (PHP8.1) (SpacePossum)
* bug #6188 `PhpdocTo(Param|Property|Return)TypeFixer` - fix for type intersections (kubawerlos)
* bug #6202 SquareBraceTransformer - fix for destructing square brace after double arrow (kubawerlos)
* bug #6209 OrderedClassElementsFixer - PHP8.0 support abstract private methods in traits (SpacePossum)
* bug #6224 ArgumentsAnalyzer - support PHP8.1 readonly (SpacePossum)
* feature #4571 BlankLineBeforeStatementFixer - can now add blank lines before doc-comments (addiks, SpacePossum)
* feature #5953 GetClassToClassKeywordFixer - introduction (paulbalandan)
* minor #6108 Drop support for Symfony v4 (keradus)
* minor #6163 CI: update used PHP version (keradus)
* minor #6167 SingleSpaceAfterConstructFixer - allow multiline const (y_ahiru, SpacePossum)
* minor #6168 indexes -> indices (SpacePossum)
* minor #6171 Fix tests and CS (SpacePossum)
* minor #6172 DX: Tokens::insertSlices - groom code and fix tests (keradus)
* minor #6174 PhpdocAlignFixer: fix property-read/property-write descriptions not getting aligned (antichris)
* minor #6177 DX: chmod +x for benchmark.sh file (keradus)
* minor #6180 gitlab reporter - add fixed severity to match format (cbourreau)
* minor #6183 Simplify DiffConsoleFormatter (kubawerlos)
* minor #6184 Do not support array of patterns in Preg methods (kubawerlos)
* minor #6185 Upgrade PHPStan (kubawerlos)
* minor #6189 Finder - fix usage of ignoreDotFiles (kubawerlos)
* minor #6190 DX: DiffConsoleFormatter - escape - (keradus)
* minor #6194 Update Docker setup (julienfalque)
* minor #6196 clean ups (SpacePossum)
* minor #6198 DX: format dot files (kubawerlos)
* minor #6200 DX: Composer's branch-alias leftovers cleanup (kubawerlos)
* minor #6203 Bump required PHP to 7.4 (keradus)
* minor #6205 DX: bump PHPUnit to v9, PHPUnit bridge to v6 and Prophecy-PHPUnit to v2 (keradus)
* minor #6210 NullableTypeDeclarationForDefaultNullValueFixer - fix tests (HypeMC)
* minor #6212 bump year 2021 -> 2022 (SpacePossum)
* minor #6215 DX: Doctrine\Annotation\Tokens - fix phpstan violations (keradus)
* minor #6216 DX: Doctrine\Annotation\Tokens - drop unused methods (keradus)
* minor #6217 DX: lock SCA tools for PR builds (keradus)
* minor #6218 Use composer/xdebug-handler v3 (gharlan)
* minor #6222 Show runtime on version command (SpacePossum)
* minor #6229 Simplify Tokens::isMonolithicPhp tests (kubawerlos)
* minor #6232 Use expectNotToPerformAssertions where applicable (SpacePossum)
* minor #6233 Update Tokens::isMonolithicPhp (kubawerlos)
* minor #6236 Annotation - improve getting variable name (kubawerlos)

Changelog for v3.4.0
--------------------

* bug #6117 SingleSpaceAfterConstruct - handle before destructuring close brace (liquid207)
* bug #6122 NoMultilineWhitespaceAroundDoubleArrowFixer - must run before MethodArgumentSpaceFixer (kubawerlos)
* bug #6130 StrictParamFixer - must run before MethodArgumentSpaceFixer (kubawerlos)
* bug #6137 NewWithBracesFixer - must run before ClassDefinitionFixer (kubawerlos)
* bug #6139 PhpdocLineSpanFixer - must run before NoSuperfluousPhpdocTagsFixer (kubawerlos)
* bug #6143 OperatorLinebreakFixer - fix for alternative syntax (kubawerlos)
* bug #6159 ImportTransformer - fix for grouped constant and function imports (kubawerlos)
* bug #6161 NoUnreachableDefaultArgumentValueFixer - fix for attributes (kubawerlos)
* feature #5776 DX: test on PHP 8.1 (kubawerlos)
* feature #6152 PHP8.1 support (SpacePossum)
* minor #6095 Allow Symfony 6 (derrabus, keradus)
* minor #6107 Drop support of PHPUnit v7 dependency (keradus)
* minor #6109 Add return type to `DummyTestSplFileInfo::getRealPath()` (derrabus)
* minor #6115 Remove PHP 7.2 polyfill (derrabus)
* minor #6116 CI: remove installation of mbstring polyfill in build script, it's required dependency now (keradus)
* minor #6119 OrderedClassElementsFixer - PHPUnit `assert(Pre|Post)Conditions` methods support (meyerbaptiste)
* minor #6121 Use Tokens::ensureWhitespaceAtIndex to simplify code (kubawerlos)
* minor #6127 Remove 2nd parameter to XdebugHandler constructor (phil-davis)
* minor #6129 clean ups (SpacePossum)
* minor #6138 PHP8.1 - toString cannot return type hint void (SpacePossum)
* minor #6146 PHP 8.1: add new_in_initializers to PHP 8.1 integration test (keradus)
* minor #6147 DX: update composer-normalize (keradus)
* minor #6156 DX: drop hack for Prophecy incompatibility (keradus)

Changelog for v3.3.1
--------------------

* minor #6067 Bump minimum PHP version to 7.2 (keradus)

Changelog for v3.3.0
--------------------

* bug #6054 Utils - Add multibyte and UTF-8 support (paulbalandan)
* bug #6061 ModernizeStrposFixer - fix for negated with leading slash (kubawerlos)
* bug #6064 SquareBraceTransformer - fix detect array destructing in foreach (SpacePossum)
* bug #6082 PhpUnitDedicateAssertFixer must run before NoUnusedImportsFixer (kubawerlos)
* bug #6089 TokensAnalyzer.php - Fix T_ENCAPSED_AND_WHITESPACE handling in isBina… (SpacePossum)
* feature #5123 PhpdocTypesFixer - support generic types (kubawerlos)
* minor #5775 DX: run static code analysis on PHP 8.0 (kubawerlos)
* minor #6050 DX: TypeIntersectionTransformer - prove to not touch T_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG (keradus)
* minor #6051 NoExtraBlankLinesFixer - Improve deprecation message (paulbalandan)
* minor #6060 DX: Add upgrade guide link when next Major is available (keradus)
* minor #6066 Clean ups (SpacePossum, kubawerlos)
* minor #6069 DX: cleanup stub file (keradus)
* minor #6070 Update UPGRADE-v3.md with php_unit_test_annotation/case deprecation (kubawerlos)
* minor #6072 Update usage doc to reflect change to PSR12 default. (hannob, keradus)
* minor #6084 Change: Remove __constructor() from RuleSetDescriptionInterface (niklam)
* minor #6085 Dx: reuse WhitespacesAnalyzer::detectIndent (kubawerlos)
* minor #6087 AbstractProxyFixer - more tests (SpacePossum)

Changelog for v3.2.1
---------------------

experimental release

* Require PHP 7.2

Changelog for v3.2.0
--------------------

* bug #5809 FunctionsAnalyzer - fix for recognizing global functions in attributes (kubawerlos)
* bug #5909 NativeFunctionCasingFixer - fix for attributes and imported functions (kubawerlos)
* bug #5920 ClassAttributesSeparationFixer - fixes & enhancements (SpacePossum)
* bug #5923 TypeAlternationTransformer - fix for promoted properties (kubawerlos)
* bug #5938 NoAliasFunctionsFixer - remove dir -> getdir mapping (SpacePossum)
* bug #5941 TokensAnalyzer - isAnonymousClass bug on PHP8 (SpacePossum)
* bug #5942 TokensAnalyzer - isConstantInvocation PHP 8 issue (SpacePossum)
* bug #5943 NoUnusedImportsFixer - use in attribute (SpacePossum)
* bug #5955 Fixed `class_attributes_separation` processing class with multiple trait imports (GrahamCampbell)
* bug #5977 LowercaseStaticReference - SingleClassElementPerStatement - union types (SpacePossum)
* bug #5984 RegularCallableCallFixer must run before NativeFunctionInvocationFixer (kubawerlos)
* bug #5986 CurlyBraceTransformer - count T_CURLY_OPEN itself as level as well (SpacePossum)
* bug #5989 NoAliasFunctionsFixer -  Correct mapping (weshooper)
* bug #6004 SwitchContinueToBreakFixer - Fix candidate check (SpacePossum)
* bug #6005 CommentsAnalyzer - before static call (SpacePossum)
* bug #6007 YodaStyleFixer - PHP8 named arguments support (liquid207)
* bug #6015 CommentsAnalyzer - constructor property promotion support (liquid207)
* bug #6020 RegularCallableCallFixer - case insensitive fixing (SpacePossum)
* bug #6037 PhpdocLineSpanFixer - do not crash on trait imports (SpacePossum)
* feature #4834 AssignNullCoalescingToCoalesceEqualFixer - introduction (SpacePossum)
* feature #5754 ModernizeStrposFixer - introduction (derrabus, SpacePossum, keradus)
* feature #5858 EmptyLoopConditionFixer - introduction (SpacePossum)
* feature #5967 PHP8.1 - type "never" support (SpacePossum)
* feature #5968 PHP8.1 - "readonly" property modifier support (SpacePossum)
* feature #5970 IntegerLiteralCaseFixer - introduction (SpacePossum)
* feature #5971 PHP8.1 - Explicit octal integer literal notation (SpacePossum)
* feature #5997 NoSuperfluousPhpdocTagsFixer - Add union types support (julienfalque)
* feature #6026 TypeIntersectionTransformer - introduction (kubawerlos, SpacePossum)
* feature #6031 NoSpaceAroundDoubleColonFixer - introduction (SpacePossum)
* feature #6047 StringLengthToEmptyFixer - introduction (SpacePossum)
* minor #5773 NoAlternativeSyntaxFixer - Add option to not fix non-monolithic PHP code (paulbalandan)
* minor #5887 Detect renamed rules in configuration resolver (shakaran)
* minor #5901 DX: update PHPStan (kubawerlos)
* minor #5906 Remove references to PHP 7.0 in tests (with updates) (kubawerlos)
* minor #5918 Remove PHP version specific code sample constraint when not needed (kubawerlos)
* minor #5924 PSR12 - ClassDefinition - space_before_parenthesis (SpacePossum)
* minor #5925 DX: ProjectCodeTest - fix detection by testExpectedInputOrder (keradus)
* minor #5926 DX: remove not needed requirements from fixtures (kubawerlos)
* minor #5927 Symfonyset - EmptyLoopBody (SpacePossum)
* minor #5928 PhpdocTo*TypeFixer - add more test cases (keradus)
* minor #5929 Remove not needed PHP version checks (kubawerlos)
* minor #5930 simplify code, more tests (SpacePossum)
* minor #5931 logo copyright - bump year (SpacePossum)
* minor #5932 Extract ControlStructureContinuationPositionFixer from BracesFixer (julienfalque)
* minor #5933 Consistency invalid configuration exception for test (shakaran)
* minor #5934 Add return types (SpacePossum)
* minor #5949 Removed PHP 5 exception catch (GrahamCampbell)
* minor #5952 ClassAttributesSeparationFixer - Re-add omitted `only_if_meta` option (paulbalandan)
* minor #5957 Keep PHPStan cache between Docker runs (julienfalque)
* minor #5958 Fix STDIN test when path is one level deep (julienfalque)
* minor #5959 SymfonySet - add EmptyLoopConditionFixer (SpacePossum)
* minor #5961 Remove duplicated method (julienfalque)
* minor #5962 DX: Add return types (kubawerlos)
* minor #5963 DX: extract config for special CI jobs (keradus)
* minor #5964 DX: use modernize_strpos (keradus)
* minor #5965 CI: don't try to execute jobs with Symfony:^3 (keradus)
* minor #5972 PHP8.1 - FirstClassCallable (SpacePossum)
* minor #5973 PHP8.1 - "final const" support (SpacePossum)
* minor #5975 Tree shake PHP8.1 PRs (SpacePossum)
* minor #5978 PHP8.1 - Enum (start) (SpacePossum)
* minor #5982 Fix test warning (SpacePossum)
* minor #5987 PHP8.1 - Enum (start) (SpacePossum)
* minor #5995 Fix link to Code Climate SPEC.md in GitlabReporter (astehlik)
* minor #5996 Fix URL to Doctrine Annotations documentation (astehlik)
* minor #6000 Prevent PHP CS Fixer from fixing PHPStan cache files (julienfalque)
* minor #6006 SCA/utilize PHP8.1 (SpacePossum)
* minor #6008 SCA (SpacePossum)
* minor #6010 SCA (SpacePossum)
* minor #6011 NoSuperfluousPhpdocTagsFixer - Remove superfluous annotation `@abstract` and `@final` (liquid207, SpacePossum)
* minor #6018 PhpdocLineSpan - Allow certain types to be ignored (devfrey)
* minor #6019 Improve test coverage (SpacePossum)
* minor #6021 Linter/*Exception - Tag as final (SpacePossum)
* minor #6023 OrderedClassElementsFixer - PHP8.1 readonly properties support (SpacePossum)
* minor #6027 MbStrFunctionsFixer - more details about risky (SpacePossum)
* minor #6028 BinaryOperatorSpacesFixer - list all operators in doc (SpacePossum)
* minor #6029 PhpUnitDedicateAssertFixer - add "assertStringContainsString" and "as… (SpacePossum)
* minor #6030 SingleSpaceAfterConstructFixer - Add `switch` support (SpacePossum)
* minor #6033 ArgumentsAnalyzerTest - add more tests (SpacePossum)
* minor #6034 Cleanup tests for PHP 7.0 and 7.1 (SpacePossum)
* minor #6035 Documentation generation split up and add list. (SpacePossum)
* minor #6048 Fix "can not" spelling (mvorisek)

Changelog for v3.1.0
--------------------

* feature #5572 PhpdocToCommentFixer - Add `ignored_tags` option (VincentLanglet)
* feature #5588 NoAliasFunctionsFixer - Add more function aliases (danog)
* feature #5704 ClassAttributesSeparationFixer - Introduce `only_if_meta` spacing option (paulbalandan)
* feature #5734 TypesSpacesFixer - Introduction (kubawerlos)
* feature #5745 EmptyLoopBodyFixer - introduction (SpacePossum, keradus)
* feature #5751 Extract DeclareParenthesesFixer from BracesFixer (julienfalque, keradus)
* feature #5877 ClassDefinitionFixer - PSR12 for anonymous class (SpacePossum)
* minor #5875 EmptyLoopBodyFixer - NoTrailingWhitespaceFixer - priority test (SpacePossum)
* minor #5914 Deprecate ClassKeywordRemoveFixer (kubawerlos)

Changelog for v3.0.3
--------------------

* bug #4927 PhpdocAlignFixer - fix for whitespace in type (kubawerlos)
* bug #5720 NoUnusedImportsFixer - Fix undetected unused imports when type mismatch (julienfalque, SpacePossum)
* bug #5806 DoctrineAnnotationFixer - Add template to ignored_tags (akalineskou)
* bug #5849 PhpdocTagTypeFixer - must not remove inlined tags within other tags (boesing)
* bug #5853 BracesFixer - handle alternative short foreach with if (SpacePossum)
* bug #5855 GlobalNamespaceImportFixer - fix for attributes imported as constants (kubawerlos)
* bug #5881 SelfUpdateCommand - fix link to UPGRADE docs (keradus)
* bug #5884 CurlyBraceTransformer - fix handling dynamic property with string with variable (kubawerlos, keradus)
* bug #5912 TypeAlternationTransformer - fix for "callable" type (kubawerlos)
* bug #5913 SingleSpaceAfterConstructFixer - improve comma handling (keradus)
* minor #5829 DX: Fix SCA with PHPMD (paulbalandan)
* minor #5838 PHP7  - use spaceship (SpacePossum, keradus)
* minor #5848 Docs: update PhpStorm integration link (keradus)
* minor #5856 Add AttributeAnalyzer (kubawerlos)
* minor #5857 DX: PHPMD - exclude fixtures (keradus)
* minor #5859 Various fixes (kubawerlos)
* minor #5864 DX: update dev tools (kubawerlos)
* minor #5876 AttributeTransformerTest - add more tests (SpacePossum)
* minor #5879 Update UPGRADE-v3.md adding relative links (shakaran, keradus)
* minor #5882 Docs: don't use v2 for installation example (keradus)
* minor #5883 Docs: typo (brianteeman, keradus)
* minor #5890 DX: use PHP 8.1 polyfill (keradus)
* minor #5902 Remove references to PHP 7.0 in tests (only removing lines) (kubawerlos)
* minor #5905 DX: Use "yield from" in tests (kubawerlos, keradus)
* minor #5917 Use `@PHP71Migration` rules (kubawerlos, keradus)

Changelog for v3.0.2
--------------------

* bug #5816 FullyQualifiedStrictTypesFixer - fix for union types (kubawerlos, keradus)
* bug #5835 PhpdocTypesOrderFixer: fix for array shapes (kubawerlos)
* bug #5837 SingleImportPerStatementFixer - fix const and function imports (SpacePossum)
* bug #5844 PhpdocTypesOrderFixer: handle callable() type (Slamdunk)
* minor #5839 DX: automate checking 7.0 types on project itself (keradus)
* minor #5840 DX: drop v2 compatible config in project itself (keradus)

Changelog for v3.0.1
--------------------

* bug #5395 PhpdocTagTypeFixer: Do not modify array shapes (localheinz, julienfalque)
* bug #5678 UseArrowFunctionsFixer - fix for return without value (kubawerlos)
* bug #5679 PhpUnitNamespacedFixer - do not try to fix constant usage (kubawerlos)
* bug #5681 RegularCallableCallFixer - fix for function name with escaped slash (kubawerlos)
* bug #5687 FinalInternalClassFixer - fix for annotation with space after "@" (kubawerlos)
* bug #5688 ArrayIndentationFixer - fix for really long arrays (kubawerlos)
* bug #5690 PhpUnitNoExpectationAnnotationFixer - fix "expectedException" annotation with message below (kubawerlos)
* bug #5693 YodaStyleFixer - fix for assignment operators (kubawerlos)
* bug #5697 StrictParamFixer - fix for method definition (kubawerlos)
* bug #5702 CommentToPhpdocFixer - fix for single line comments starting with more than 2 slashes (kubawerlos)
* bug #5703 DateTimeImmutableFixer - fix for method definition (kubawerlos)
* bug #5718 VoidReturnFixer - do not break syntax with magic methods (kubawerlos)
* bug #5727 SingleSpaceAfterConstructFixer - Add support for `namespace` (julienfalque)
* bug #5730 Fix transforming deprecations into exceptions (julienfalque)
* bug #5738 TokensAnalyzer - fix for union types (kubawerlos)
* bug #5741 Fix constant invocation detection cases (kubawerlos)
* bug #5769 Fix priority between `phpdoc_to_property_type` and `no_superfluous_phpdoc_tags` (julienfalque)
* bug #5774 FunctionsAnalyzer::isTheSameClassCall - fix for $this with double colon following (kubawerlos)
* bug #5779 SingleLineThrowFixer - fix for throw in match (kubawerlos)
* bug #5781 ClassDefinition - fix for anonymous class with trailing comma (kubawerlos)
* bug #5783 StaticLambdaFixer - consider parent:: as a possible reference to $this (fancyweb)
* bug #5791 NoBlankLinesAfterPhpdoc - Add T_NAMESPACE in array of forbidden successors (paulbalandan)
* bug #5799 TypeAlternationTransformer - fix for multiple function parameters (kubawerlos)
* bug #5804 NoBreakCommentFixer - fix for "default" in "match" (kubawerlos)
* bug #5805 SingleLineCommentStyleFixer - run after HeaderCommentFixer (kubawerlos)
* bug #5817 NativeFunctionTypeDeclarationCasingFixer - fix for union types (kubawerlos)
* bug #5823 YodaStyleFixer - yield support (SpacePossum)
* minor #4914 Improve PHPDoc types support (julienfalque, keradus)
* minor #5592 Fix checking for default config used in rule sets (kubawerlos)
* minor #5675 Docs: extend Upgrade Guide (keradus)
* minor #5680 DX: benchmark.sh - ensure deps are updated to enable script working across less-similar branches (keradus)
* minor #5689 Calculate code coverage on PHP 8 (kubawerlos)
* minor #5694 DX: fail on risky tests (kubawerlos)
* minor #5695 Utils - save only unique deprecations to avoid memory issues (PetrHeinz)
* minor #5710 [typo] add correct backquotes (PhilETaylor)
* minor #5711 Fix doc, "run-in" show-progress option is no longer present (mvorisek)
* minor #5713 Upgrade-Guide: fix typo (staabm)
* minor #5717 Run migration rules on PHP 8 (kubawerlos, keradus)
* minor #5721 Fix reStructuredText markup (julienfalque)
* minor #5725 Update LICENSE (exussum12)
* minor #5731 CI - Fix checkbashisms installation (julienfalque)
* minor #5736 Remove references to PHP 5.6 (kubawerlos, keradus)
* minor #5739 DX: more typehinting (keradus)
* minor #5740 DX: more type-related docblocks (keradus)
* minor #5746 Config - Improve deprecation message with details (SpacePossum)
* minor #5747 RandomApiMigrationFixer - better docs and better "random_int" support (SpacePossum)
* minor #5748 Updated the link to netbeans plugins page (cyberguroo)
* minor #5750 Test all const are in uppercase (SpacePossum)
* minor #5752 NoNullPropertyInitializationFixer - fix static properties as well (HypeMC)
* minor #5756 Fix rule sets descriptions (kubawerlos)
* minor #5761 Fix links in custom rules documentation (julienfalque)
* minor #5771 doc(config): change set's name (Kocal)
* minor #5777 DX: update PHPStan (kubawerlos)
* minor #5789 DX: update PHPStan (kubawerlos)
* minor #5808 Update PHPStan to 0.12.92 (kubawerlos)
* minor #5813 Docs: point to v3 in installation description (Jimbolino)
* minor #5824 Deprecate v2 (keradus)
* minor #5825 DX: update checkbashisms to v2.21.3 (keradus)
* minor #5826 SCA: check both composer files (keradus)
* minor #5827 ClassAttributesSeparationFixer - Add `trait_import` support (SpacePossum)
* minor #5831 DX: fix SCA violations (keradus)

Changelog for v3.0.0
--------------------

* bug #5164 Differ - surround file name with double quotes if it contains spacing. (SpacePossum)
* bug #5560 PSR2: require visibility only for properties and methods (kubawerlos)
* bug #5576 ClassAttributesSeparationFixer: do not allow using v2 config (kubawerlos)
* feature #4979 Pass file to differ (paulhenri-l, SpacePossum)
* minor #3374 show-progress option: drop run-in and estimating, rename estimating-max to dots (keradus)
* minor #3375 Fixers - stop exposing extra properties/consts (keradus)
* minor #3376 Tokenizer - remove deprecations and legacy mode (keradus)
* minor #3377 rules - change default options (keradus)
* minor #3378 SKIP_LINT_TEST_CASES - drop env (keradus)
* minor #3379 MethodArgumentSpaceFixer - fixSpace is now private (keradus)
* minor #3380 rules - drop rootless configurations (keradus)
* minor #3381 rules - drop deprecated configurations (keradus)
* minor #3382 DefinedFixerInterface - incorporate into FixerInterface (keradus)
* minor #3383 FixerDefinitionInterface - drop getConfigurationDescription and getDefaultConfiguration (keradus)
* minor #3384 diff-format option: drop sbd diff, use udiffer by default, drop SebastianBergmannDiffer and SebastianBergmannShortDiffer classes (keradus)
* minor #3385 ConfigurableFixerInterface::configure - param is now not nullable and not optional (keradus)
* minor #3386 ConfigurationDefinitionFixerInterface - incorporate into ConfigurableFixerInterface (keradus)
* minor #3387 FixCommand - forbid passing 'config' and 'rules' options together (keradus)
* minor #3388 Remove Helpers (keradus)
* minor #3389 AccessibleObject - drop class (keradus)
* minor #3390 Drop deprecated rules: blank_line_before_return, hash_to_slash_comment, method_separation, no_extra_consecutive_blank_lines, no_multiline_whitespace_before_semicolons and pre_increment (keradus)
* minor #3456 AutoReview - drop references to removed rule (keradus)
* minor #3659 use php-cs-fixer/diff ^2.0 (SpacePossum)
* minor #3681 CiIntegrationTest - fix incompatibility from 2.x line (keradus)
* minor #3740 NoUnusedImportsFixer - remove SF exception (SpacePossum)
* minor #3771 UX: always set error_reporting in entry file, not Application (keradus)
* minor #3922 Make some more classes final (ntzm, SpacePossum)
* minor #3995 Change default config of native_function_invocation (dunglas, SpacePossum)
* minor #4432 DX: remove empty sets from RuleSet (kubawerlos)
* minor #4489 Fix ruleset @PHPUnit50Migration:risky (kubawerlos)
* minor #4620 DX: cleanup additional, not used parameters (keradus)
* minor #4666 Remove deprecated rules: lowercase_constants, php_unit_ordered_covers, silenced_deprecation_error (keradus)
* minor #4697 Remove deprecated no_short_echo_tag rule (julienfalque)
* minor #4851 fix phpstan on 3.0 (SpacePossum)
* minor #4901 Fix SCA (SpacePossum)
* minor #5069 Fixed failing tests on 3.0 due to unused import after merge (GrahamCampbell)
* minor #5096 NativeFunctionInvocationFixer - BacktickToShellExecFixer - fix integration test (SpacePossum)
* minor #5171 Fix test (SpacePossum)
* minor #5245 Fix CI for 3.0 line (keradus)
* minor #5351 clean ups (SpacePossum)
* minor #5364 DX: Do not display runtime twice on 3.0 line (keradus)
* minor #5412 3.0 - cleanup (SpacePossum, keradus)
* minor #5417 Further BC cleanup for 3.0 (keradus)
* minor #5418 Drop src/Test namespace (keradus)
* minor #5436 Drop mapping of strings to boolean option other than yes/no (keradus)
* minor #5440 Change default ruleset to PSR-12 (keradus)
* minor #5477 Drop diff-format (keradus)
* minor #5478 Docs: Cleanup UPGRADE markdown files (keradus)
* minor #5479 ArraySyntaxFixer, ListSyntaxFixer - change default syntax to short (keradus)
* minor #5480 Tokens::findBlockEnd - drop deprecated argument (keradus)
* minor #5485 ClassAttributesSeparationFixer - drop deprecated flat list configuration (keradus)
* minor #5486 CI: drop unused env variables (keradus)
* minor #5488 Do not distribute documentation (szepeviktor)
* minor #5513 DX: Tokens::warnPhp8SplFixerArrayChange - drop unused method (keradus)
* minor #5520 DX: Drop IsIdenticalConstraint (keradus)
* minor #5521 DX: apply rules configuration cleanups for PHP 7.1+ (keradus)
* minor #5524 DX: drop support of very old deps (keradus)
* minor #5525 Drop phpunit-legacy-adapter (keradus)
* minor #5527 Bump required PHP to 7.1 (keradus)
* minor #5529 DX: bump required PHPUnit to v7+ (keradus)
* minor #5532 Apply PHP 7.1 typing (keradus)
* minor #5541 RuleSet - disallow null usage to disable the rule (keradus)
* minor #5555 DX: further typing improvements (keradus)
* minor #5562 Fix table row rendering for default values of array_syntax and list_syntax (derrabus)
* minor #5608 DX: new cache filename (keradus)
* minor #5609 Forbid old config filename usage (keradus)
* minor #5638 DX: remove Utils::calculateBitmask (keradus)
* minor #5641 DX: use constants for PHPUnit version on 3.0 line (keradus)
* minor #5643 FixCommand - simplify help (keradus)
* minor #5644 Token::toJson() - remove parameter (keradus)
* minor #5645 DX: YodaStyleFixerTest - fix CI (keradus)
* minor #5649 DX: YodaStyleFixerTest - fix 8.0 compat (keradus)
* minor #5650 DX: FixCommand - drop outdated/duplicated docs (keradus)
* minor #5656 DX: mark some constants as internal or private (keradus)
* minor #5657 DX: convert some properties to constants (keradus)
* minor #5669 Remove TrailingCommaInMultilineArrayFixer (kubawerlos, keradus)

Changelog for v2.19.3
---------------------

* minor #6060 DX: Add upgrade guide link when next Major is available (keradus)

Changelog for v2.19.2
---------------------

* bug #5881 SelfUpdateCommand - fix link to UPGRADE docs (keradus)

Changelog for v2.19.1
---------------------

* bug #5395 PhpdocTagTypeFixer: Do not modify array shapes (localheinz, julienfalque)
* bug #5678 UseArrowFunctionsFixer - fix for return without value (kubawerlos)
* bug #5679 PhpUnitNamespacedFixer - do not try to fix constant usage (kubawerlos)
* bug #5681 RegularCallableCallFixer - fix for function name with escaped slash (kubawerlos)
* bug #5687 FinalInternalClassFixer - fix for annotation with space after "@" (kubawerlos)
* bug #5688 ArrayIndentationFixer - fix for really long arrays (kubawerlos)
* bug #5690 PhpUnitNoExpectationAnnotationFixer - fix "expectedException" annotation with message below (kubawerlos)
* bug #5693 YodaStyleFixer - fix for assignment operators (kubawerlos)
* bug #5697 StrictParamFixer - fix for method definition (kubawerlos)
* bug #5702 CommentToPhpdocFixer - fix for single line comments starting with more than 2 slashes (kubawerlos)
* bug #5703 DateTimeImmutableFixer - fix for method definition (kubawerlos)
* bug #5718 VoidReturnFixer - do not break syntax with magic methods (kubawerlos)
* bug #5727 SingleSpaceAfterConstructFixer - Add support for `namespace` (julienfalque)
* bug #5730 Fix transforming deprecations into exceptions (julienfalque)
* bug #5738 TokensAnalyzer - fix for union types (kubawerlos)
* bug #5741 Fix constant invocation detection cases (kubawerlos)
* bug #5769 Fix priority between `phpdoc_to_property_type` and `no_superfluous_phpdoc_tags` (julienfalque)
* bug #5774 FunctionsAnalyzer::isTheSameClassCall - fix for $this with double colon following (kubawerlos)
* bug #5779 SingleLineThrowFixer - fix for throw in match (kubawerlos)
* bug #5781 ClassDefinition - fix for anonymous class with trailing comma (kubawerlos)
* bug #5783 StaticLambdaFixer - consider parent:: as a possible reference to $this (fancyweb)
* bug #5791 NoBlankLinesAfterPhpdoc - Add T_NAMESPACE in array of forbidden successors (paulbalandan)
* bug #5799 TypeAlternationTransformer - fix for multiple function parameters (kubawerlos)
* bug #5804 NoBreakCommentFixer - fix for "default" in "match" (kubawerlos)
* bug #5805 SingleLineCommentStyleFixer - run after HeaderCommentFixer (kubawerlos)
* bug #5817 NativeFunctionTypeDeclarationCasingFixer - fix for union types (kubawerlos)
* bug #5823 YodaStyleFixer - yield support (SpacePossum)
* minor #4914 Improve PHPDoc types support (julienfalque, keradus)
* minor #5680 DX: benchmark.sh - ensure deps are updated to enable script working across less-similar branches (keradus)
* minor #5689 Calculate code coverage on PHP 8 (kubawerlos)
* minor #5694 DX: fail on risky tests (kubawerlos)
* minor #5695 Utils - save only unique deprecations to avoid memory issues (PetrHeinz)
* minor #5710 [typo] add correct backquotes (PhilETaylor)
* minor #5717 Run migration rules on PHP 8 (kubawerlos, keradus)
* minor #5721 Fix reStructuredText markup (julienfalque)
* minor #5725 Update LICENSE (exussum12)
* minor #5731 CI - Fix checkbashisms installation (julienfalque)
* minor #5740 DX: more type-related docblocks (keradus)
* minor #5746 Config - Improve deprecation message with details (SpacePossum)
* minor #5747 RandomApiMigrationFixer - better docs and better "random_int" support (SpacePossum)
* minor #5748 Updated the link to netbeans plugins page (cyberguroo)
* minor #5750 Test all const are in uppercase (SpacePossum)
* minor #5752 NoNullPropertyInitializationFixer - fix static properties as well (HypeMC)
* minor #5756 Fix rule sets descriptions (kubawerlos)
* minor #5761 Fix links in custom rules documentation (julienfalque)
* minor #5777 DX: update PHPStan (kubawerlos)
* minor #5789 DX: update PHPStan (kubawerlos)
* minor #5808 Update PHPStan to 0.12.92 (kubawerlos)
* minor #5824 Deprecate v2 (keradus)
* minor #5825 DX: update checkbashisms to v2.21.3 (keradus)
* minor #5826 SCA: check both composer files (keradus)
* minor #5827 ClassAttributesSeparationFixer - Add `trait_import` support (SpacePossum)

Changelog for v2.19.0
---------------------

* feature #4238 TrailingCommaInMultilineFixer - introduction (kubawerlos)
* feature #4592 PhpdocToPropertyTypeFixer - introduction (julienfalque)
* feature #5390 feature #4024 added a `list-files` command (clxmstaab, keradus)
* feature #5635 Add list-sets command (keradus)
* feature #5674 UX: Display deprecations to end-user (keradus)
* minor #5601 Always stop when "PHP_CS_FIXER_FUTURE_MODE" is used (kubawerlos)
* minor #5607 DX: new config filename (keradus)
* minor #5613 DX: UtilsTest - add missing teardown (keradus)
* minor #5631 DX: config deduplication (keradus)
* minor #5633 fix typos (staabm)
* minor #5642 Deprecate parameter of Token::toJson() (keradus)
* minor #5672 DX: do not test deprecated fixer (kubawerlos)

Changelog for v2.18.7
---------------------

* bug #5593 SingleLineThrowFixer - fix handling anonymous classes (kubawerlos)
* bug #5654 SingleLineThrowFixer - fix for match expression (kubawerlos)
* bug #5660 TypeAlternationTransformer - fix for "array" type in type alternation (kubawerlos)
* bug #5665 NullableTypeDeclarationForDefaultNullValueFixer - fix for nullable with attribute (kubawerlos)
* bug #5670 PhpUnitNamespacedFixer - do not try to fix constant (kubawerlos)
* bug #5671 PhpdocToParamTypeFixer - do not change function call (kubawerlos)
* bug #5673 GroupImportFixer - Fix failing case (julienfalque)
* minor #4591 Refactor conversion of PHPDoc to type declarations (julienfalque, keradus)
* minor #5611 DX: use method expectDeprecation from Symfony Bridge instead of annotation (kubawerlos)
* minor #5658 DX: use constants in tests for Fixer configuration (keradus)
* minor #5661 DX: remove PHPStan exceptions for "tests" from phpstan.neon (kubawerlos)
* minor #5662 Change wording from "merge" to "intersect" (jschaedl)
* minor #5663 DX: do not abuse "inheritdoc" tag (kubawerlos)
* minor #5664 DX: code grooming (keradus)

Changelog for v2.18.6
---------------------

* bug #5586 Add support for nullsafe object operator ("?->") (kubawerlos)
* bug #5597 Tokens - fix for checking block edges  (kubawerlos)
* bug #5604 Custom annotations @type changed into @var (Leprechaunz)
* bug #5606 DoctrineAnnotationBracesFixer false positive (Leprechaunz)
* bug #5610 BracesFixer - fix braces of match expression (Leprechaunz)
* bug #5615 GroupImportFixer severely broken (Leprechaunz)
* bug #5617 ClassAttributesSeparationFixer - fix for using visibility for class elements (kubawerlos)
* bug #5618 GroupImportFixer - fix removal of import type when mixing multiple types (Leprechaunz)
* bug #5622 Exclude Doctrine documents from final fixer (ossinkine)
* bug #5630 PhpdocTypesOrderFixer - handle complex keys (Leprechaunz)
* minor #5554 DX: use tmp file in sys_temp_dir for integration tests (keradus)
* minor #5564 DX: make integration tests matching entries in FixerFactoryTest (kubawerlos)
* minor #5603 DX: DocumentationGenerator - no need to re-configure Differ (keradus)
* minor #5612 DX: use ::class whenever possible (kubawerlos)
* minor #5619 DX: allow XDebugHandler v2 (keradus)
* minor #5623 DX: when displaying app version, don't put extra space if there is no CODENAME available (keradus)
* minor #5626 DX: update PHPStan and way of ignoring flickering PHPStan exception (keradus)
* minor #5629 DX: fix CiIntegrationTest (keradus)
* minor #5636 DX: remove 'create' method in internal classes (keradus)
* minor #5637 DX: do not calculate bitmap via helper anymore (keradus)
* minor #5639 Move fix reports (classes and schemas) (keradus)
* minor #5640 DX: use constants for PHPUnit version (keradus)
* minor #5646 Cleanup YodaStyleFixerTest (kubawerlos)

Changelog for v2.18.5
---------------------

* bug #5561 NoMixedEchoPrintFixer: fix for conditions without curly brackets (kubawerlos)
* bug #5563 Priority fix: SingleSpaceAfterConstructFixer must run before BracesFixer (kubawerlos)
* bug #5567 Fix order of BracesFixer and ClassDefinitionFixer (Daeroni)
* bug #5596 NullableTypeTransformer - fix for attributes (kubawerlos, jrmajor)
* bug #5598 GroupImportFixer - fix breaking code when fixing root classes (Leprechaunz)
* minor #5571 DX: add test to make sure SingleSpaceAfterConstructFixer runs before FunctionDeclarationFixer (kubawerlos)
* minor #5577 Extend priority test for "class_definition" vs "braces" (kubawerlos)
* minor #5585 DX: make doc examples prettier (kubawerlos)
* minor #5590 Docs: HeaderCommentFixer - document example how to remove header comment (keradus)
* minor #5602 DX: regenerate docs (keradus)

Changelog for v2.18.4
---------------------

* bug #4085 Priority: AlignMultilineComment should run before every PhpdocFixer (dmvdbrugge)
* bug #5421 PsrAutoloadingFixer - Fix PSR autoloading outside configured directory (kelunik, keradus)
* bug #5464 NativeFunctionInvocationFixer - PHP 8 attributes (HypeMC, keradus)
* bug #5548 NullableTypeDeclarationForDefaultNullValueFixer - fix handling promoted properties (jrmajor, keradus)
* bug #5550 TypeAlternationTransformer - fix for typed static properties (kubawerlos)
* bug #5551 ClassAttributesSeparationFixer - fix for properties with type alternation (kubawerlos, keradus)
* bug #5552 DX: test relation between function_declaration and method_argument_space (keradus)
* minor #5540 DX: RuleSet - convert null handling to soft-warning (keradus)
* minor #5545 DX: update checkbashisms (keradus)

Changelog for v2.18.3
---------------------

* bug #5484 NullableTypeDeclarationForDefaultNullValueFixer - handle mixed pseudotype (keradus)
* minor #5470 Disable CI fail-fast (mvorisek)
* minor #5491 Support php8 static return type for NoSuperfluousPhpdocTagsFixer (tigitz)
* minor #5494 BinaryOperatorSpacesFixer - extend examples (keradus)
* minor #5499 DX: add TODOs for PHP requirements cleanup (keradus)
* minor #5500 DX: Test that Transformers are adding only CustomTokens that they define and nothing else (keradus)
* minor #5507 Fix quoting in exception message (gquemener)
* minor #5514 DX: PHP 7.0 integration test - solve TODO for random_api_migration usage (keradus)
* minor #5515 DX: do not override getConfigurationDefinition (keradus)
* minor #5516 DX: AbstractDoctrineAnnotationFixer - no need for import aliases (keradus)
* minor #5518 DX: minor typing and validation fixes (keradus)
* minor #5522 Token - add handling json_encode crash (keradus)
* minor #5523 DX: EregToPregFixer - fix sorting (keradus)
* minor #5528 DX: code cleanup (keradus)

Changelog for v2.18.2
---------------------

* bug #5466 Fix runtime check of PHP version (keradus)
* minor #4250 POC Tokens::insertSlices (keradus)

Changelog for v2.18.1
---------------------

* bug #5447 switch_case_semicolon_to_colon should skip match/default statements (derrabus)
* bug #5453 SingleSpaceAfterConstructFixer - better handling of closing parenthesis and brace (keradus)
* bug #5454 NullableTypeDeclarationForDefaultNullValueFixer - support property promotion via constructor (keradus)
* bug #5455 PhpdocToCommentFixer - add support for attributes (keradus)
* bug #5462 NullableTypeDeclarationForDefaultNullValueFixer - support union types (keradus)
* minor #5444 Fix PHP version number in PHP54MigrationSet description (jdreesen, keradus)
* minor #5445 DX: update usage of old TraversableContains in tests (keradus)
* minor #5456 DX: Fix CiIntegrationTest (keradus)
* minor #5457 CI: fix params order (keradus)
* minor #5458 CI: fix migration workflow (keradus)
* minor #5459 DX: cleanup PHP Migration rulesets (keradus)

Changelog for v2.18.0
---------------------

* feature #4943 Add PSR12 ruleset (julienfalque, keradus)
* feature #5426 Update Symfony ruleset (keradus)
* feature #5428 Add/Change PHP.MigrationSet to update array/list syntax to short one (keradus)
* minor #5441 Allow execution under PHP 8 (keradus)

Changelog for v2.17.5
---------------------

* bug #5447 switch_case_semicolon_to_colon should skip match/default statements (derrabus)
* bug #5453 SingleSpaceAfterConstructFixer - better handling of closing parenthesis and brace (keradus)
* bug #5454 NullableTypeDeclarationForDefaultNullValueFixer - support property promotion via constructor (keradus)
* bug #5455 PhpdocToCommentFixer - add support for attributes (keradus)
* bug #5462 NullableTypeDeclarationForDefaultNullValueFixer - support union types (keradus)
* minor #5445 DX: update usage of old TraversableContains in tests (keradus)
* minor #5456 DX: Fix CiIntegrationTest (keradus)
* minor #5457 CI: fix params order (keradus)
* minor #5459 DX: cleanup PHP Migration rulesets (keradus)

Changelog for v2.17.4
---------------------

* bug #5379 PhpUnitMethodCasingFixer - Do not modify class name (localheinz)
* bug #5404 NullableTypeTransformer - constructor property promotion support (Wirone)
* bug #5433 PhpUnitTestCaseStaticMethodCallsFixer - fix for abstract static method (kubawerlos)
* minor #5234 DX: Add Docker dev setup (julienfalque, keradus)
* minor #5391 PhpdocOrderByValueFixer - Add additional annotations to sort (localheinz)
* minor #5392 PhpdocScalarFixer - Fix description (localheinz)
* minor #5397 NoExtraBlankLinesFixer - PHP8 throw support (SpacePossum)
* minor #5399 Add PHP8 integration test (keradus)
* minor #5405 TypeAlternationTransformer - add support for PHP8 (SpacePossum)
* minor #5406 SingleSpaceAfterConstructFixer - Attributes, comments and PHPDoc support (SpacePossum)
* minor #5407 TokensAnalyzer::getClassyElements - return trait imports (SpacePossum)
* minor #5410 minors (SpacePossum)
* minor #5411 bump year in LICENSE file (SpacePossum)
* minor #5414 TypeAlternationTransformer - T_FN support (SpacePossum)
* minor #5415 Forbid execution under PHP 8.0.0 (keradus)
* minor #5416 Drop Travis CI (keradus)
* minor #5419 CI: separate SCA checks to dedicated jobs (keradus)
* minor #5420 DX: unblock PHPUnit 9.5 (keradus)
* minor #5423 DX: PHPUnit - disable verbose by default (keradus)
* minor #5425 Cleanup 3.0 todos (keradus)
* minor #5427 Plan changing defaults for array_syntax and list_syntax in 3.0 release (keradus)
* minor #5429 DX: Drop speedtrap PHPUnit listener (keradus)
* minor #5432 Don't allow unserializing classes with a destructor (jderusse)
* minor #5435 DX: PHPUnit - groom configuration of time limits (keradus)
* minor #5439 VisibilityRequiredFixer - support type alternation for properties (keradus)
* minor #5442 DX: FunctionsAnalyzerTest - add missing 7.0 requirement (keradus)

Changelog for v2.17.3
---------------------

* bug #5384 PsrAutoloadingFixer - do not remove directory structure from the Class name (kubawerlos, keradus)
* bug #5385 SingleLineCommentStyleFixer- run before NoUselessReturnFixer (kubawerlos)
* bug #5387 SingleSpaceAfterConstructFixer - do not touch multi line implements (SpacePossum)
* minor #5329 DX: collect coverage with Github Actions (kubawerlos)
* minor #5380 PhpdocOrderByValueFixer - Allow sorting of throws annotations by value (localheinz, keradus)
* minor #5383 DX: fail PHPUnit tests on warning (kubawerlos)
* minor #5386 DX: remove incorrect priority relations (kubawerlos)

Changelog for v2.17.2
---------------------

* bug #5345 CleanNamespaceFixer - preserve trailing comments (SpacePossum)
* bug #5348 PsrAutoloadingFixer - fix for class without namespace (kubawerlos)
* bug #5362 SingleSpaceAfterConstructFixer: Do not adjust whitespace before multiple multi-line extends (localheinz, SpacePossum)
* minor #5314 Enable testing with PHPUnit 9.x (sanmai)
* minor #5319 Clean ups (SpacePossum)
* minor #5338 clean ups (SpacePossum)
* minor #5339 NoEmptyStatementFixer - fix more cases (SpacePossum)
* minor #5340 NamedArgumentTransformer - Introduction (SpacePossum)
* minor #5344 Update docs: do not use deprecated create method (SpacePossum)
* minor #5353 Fix typo in issue template (stof)
* minor #5355 OrderedTraitsFixer - mark as risky (SpacePossum)
* minor #5356 RuleSet description fixes (SpacePossum)
* minor #5359 Add application version to "fix" out put when verbosity flag is set (SpacePossum)
* minor #5360 DX: clean up detectIndent methods (kubawerlos)
* minor #5363 Added missing self return type to ConfigInterface::registerCustomFixers() (vudaltsov)
* minor #5366 PhpUnitDedicateAssertInternalTypeFixer - recover target option (keradus)
* minor #5368 DX: PHPUnit 9 compatibility for 2.17 (keradus)
* minor #5370 DX: update PHPUnit usage to use external Prophecy trait and solve warning (keradus)
* minor #5371 Update documentation about PHP_CS_FIXER_IGNORE_ENV (SanderSander, keradus)
* minor #5373 DX: MagicMethodCasingFixerTest - fix test case description (keradus)
* minor #5374 DX: PhpUnitDedicateAssertInternalTypeFixer - add code sample for non-default config (keradus)

Changelog for v2.17.1
---------------------

* bug #5325 NoBreakCommentFixer - better throw handling (SpacePossum)
* bug #5327 StaticLambdaFixer - fix for arrow function used in class with $this (kubawerlos, SpacePossum)
* bug #5332 Fix file missing for php8 (jderusse)
* bug #5333 Fix file missing for php8 (jderusse)
* minor #5328 Fixed deprecation message version (GrahamCampbell)
* minor #5330 DX: cleanup Github Actions configs (kubawerlos)

Changelog for v2.17.0
---------------------

* bug #4752 SimpleLambdaCallFixer - bug fixes (SpacePossum)
* bug #4794 TernaryToElvisOperatorFixer - fix open tag with echo (SpacePossum)
* bug #5084 Fix for variables within string interpolation in lambda_not_used_import (GrahamCampbell)
* bug #5094 SwitchContinueToBreakFixer - do not support alternative syntax (SpacePossum)
* feature #2619 PSR-5 @inheritDoc support (julienfalque)
* feature #3253 Add SimplifiedIfReturnFixer (Slamdunk, SpacePossum)
* feature #4005 GroupImportFixer - introduction (greeflas)
* feature #4012 BracesFixer - add "allow_single_line_anonymous_class_with_empty_body" option (kubawerlos)
* feature #4021 OperatorLinebreakFixer - Introduction (kubawerlos, SpacePossum)
* feature #4259 PsrAutoloadingFixer - introduction (kubawerlos)
* feature #4375 extend ruleset "@PHP73Migration" (gharlan)
* feature #4435 SingleSpaceAfterConstructFixer - Introduction (localheinz)
* feature #4493 Add echo_tag_syntax rule (mlocati, kubawerlos)
* feature #4544 SimpleLambdaCallFixer - introduction (keradus)
* feature #4569 PhpdocOrderByValueFixer - Introduction (localheinz)
* feature #4590 SwitchContinueToBreakFixer - Introduction (SpacePossum)
* feature #4679 NativeConstantInvocationFixer - add "strict" flag (kubawerlos)
* feature #4701 OrderedTraitsFixer - introduction (julienfalque)
* feature #4704 LambdaNotUsedImportFixer - introduction (SpacePossum)
* feature #4740 NoAliasLanguageConstructCallFixer - introduction (SpacePossum)
* feature #4741 TernaryToElvisOperatorFixer - introduction (SpacePossum)
* feature #4778 UseArrowFunctionsFixer - introduction (gharlan)
* feature #4790 ArrayPushFixer - introduction (SpacePossum)
* feature #4800 NoUnneededFinalMethodFixer - Add "private_methods" option (SpacePossum)
* feature #4831 BlankLineBeforeStatementFixer - add yield from (SpacePossum)
* feature #4832 NoUnneededControlParenthesesFixer -  add yield from (SpacePossum)
* feature #4863 NoTrailingWhitespaceInStringFixer - introduction (gharlan)
* feature #4875 ClassAttributesSeparationFixer - add option for no new lines between properties (adri, ruudk)
* feature #4880 HeredocIndentationFixer - config option for indentation level (gharlan)
* feature #4908 PhpUnitExpectationFixer - update for Phpunit 8.4 (ktomk)
* feature #4942 OrderedClassElementsFixer - added support for abstract method sorting (carlalexander, SpacePossum)
* feature #4947 NativeConstantInvocation - Add "PHP_INT_SIZE" to SF rule set (kubawerlos)
* feature #4953 Add support for custom differ (paulhenri-l, SpacePossum)
* feature #5264 CleanNamespaceFixer - Introduction (SpacePossum)
* feature #5280 NoUselessSprintfFixer - Introduction (SpacePossum)
* minor #4634 Make all options snake_case (kubawerlos)
* minor #4667 PhpUnitOrderedCoversFixer - stop using deprecated fixer (keradus)
* minor #4673 FinalStaticAccessFixer - deprecate (julienfalque)
* minor #4762 Rename simple_lambda_call to regular_callable_call (julienfalque)
* minor #4782 Update RuleSets (SpacePossum)
* minor #4802 Master cleanup (SpacePossum)
* minor #4828 Deprecate Config::create() (DocFX)
* minor #4872 Update RuleSet SF and PHP-CS-Fixer with new config for `no_extra_blan… (SpacePossum)
* minor #4900 Move "no_trailing_whitespace_in_string" to SF ruleset. (SpacePossum)
* minor #4903 Docs: extend regular_callable_call rule docs (keradus, SpacePossum)
* minor #4910 Add use_arrow_functions rule to PHP74Migration:risky set (keradus)
* minor #5025 PhpUnitDedicateAssertInternalTypeFixer - deprecate "target" option (kubawerlos)
* minor #5037 FinalInternalClassFixer- Rename option (SpacePossum)
* minor #5093 LambdaNotUsedImportFixer - add heredoc test (SpacePossum)
* minor #5163 Fix CS (SpacePossum)
* minor #5169 PHP8 care package master (SpacePossum)
* minor #5186 Fix tests (SpacePossum)
* minor #5192 GotoLabelAnalyzer - introduction (SpacePossum)
* minor #5230 Fix: Reference (localheinz)
* minor #5240 PHP8 - Allow trailing comma in parameter list support (SpacePossum)
* minor #5244 Fix 2.17 build (keradus)
* minor #5251 PHP8 - match support (SpacePossum)
* minor #5252 Update RuleSets (SpacePossum)
* minor #5278 PHP8 constructor property promotion support (SpacePossum)
* minor #5284 PHP8 - Attribute support (SpacePossum)
* minor #5323 NoUselessSprintfFixer - Fix test on PHP5.6 (SpacePossum)
* minor #5326 DX: relax composer requirements to not block installation under PHP v8, support for PHP v8 is not yet ready (keradus)

Changelog for v2.16.10
----------------------

* minor #5314 Enable testing with PHPUnit 9.x (sanmai)
* minor #5338 clean ups (SpacePossum)
* minor #5339 NoEmptyStatementFixer - fix more cases (SpacePossum)
* minor #5340 NamedArgumentTransformer - Introduction (SpacePossum)
* minor #5344 Update docs: do not use deprecated create method (SpacePossum)
* minor #5356 RuleSet description fixes (SpacePossum)
* minor #5360 DX: clean up detectIndent methods (kubawerlos)
* minor #5370 DX: update PHPUnit usage to use external Prophecy trait and solve warning (keradus)
* minor #5373 DX: MagicMethodCasingFixerTest - fix test case description (keradus)
* minor #5374 DX: PhpUnitDedicateAssertInternalTypeFixer - add code sample for non-default config (keradus)

Changelog for v2.16.9
---------------------

* bug #5095 Annotation - fix for Windows line endings (SpacePossum)
* bug #5221 NoSuperfluousPhpdocTagsFixer - fix for single line PHPDoc (kubawerlos)
* bug #5225 TernaryOperatorSpacesFixer - fix for alternative control structures (kubawerlos)
* bug #5235 ArrayIndentationFixer - fix for nested arrays (kubawerlos)
* bug #5248 NoBreakCommentFixer - fix throw detect (SpacePossum)
* bug #5250 SwitchAnalyzer - fix for semicolon after case/default (kubawerlos)
* bug #5253 IO - fix cache info message (SpacePossum)
* bug #5273 Fix PHPDoc line span fixer when property has array typehint (ossinkine)
* bug #5274 TernaryToNullCoalescingFixer - concat precedence fix (SpacePossum)
* feature #5216 Add RuleSets to docs (SpacePossum)
* minor #5226 Applied CS fixes from 2.17-dev (GrahamCampbell)
* minor #5229 Fixed incorrect phpdoc (GrahamCampbell)
* minor #5231 CS: unify styling with younger branches (keradus)
* minor #5232 PHP8 - throw expression support (SpacePossum)
* minor #5233 DX: simplify check_file_permissions.sh (kubawerlos)
* minor #5236 Improve handling of unavailable code samples (julienfalque, keradus)
* minor #5239 PHP8 - Allow trailing comma in parameter list support (SpacePossum)
* minor #5254 PHP8 - mixed type support (SpacePossum)
* minor #5255 Tests: do not skip documentation test (keradus)
* minor #5256 Docs: phpdoc_to_return_type - add new example in docs (keradus)
* minor #5261 Do not update Composer twice (sanmai)
* minor #5263 PHP8 support (SpacePossum)
* minor #5266 PhpUnitTestCaseStaticMethodCallsFixer - PHPUnit 9.x support (sanmai)
* minor #5267 Improve InstallViaComposerTest (sanmai)
* minor #5268 Add GitHub Workflows CI, including testing on PHP 8 and on macOS/Windows/Ubuntu (sanmai)
* minor #5269 Prep work to migrate to PHPUnit 9.x (sanmai, keradus)
* minor #5275 remove not supported verbose options (SpacePossum)
* minor #5276 PHP8 - add NoUnreachableDefaultArgumentValueFixer to risky set (SpacePossum)
* minor #5277 PHP8 - Constructor Property Promotion support (SpacePossum)
* minor #5292 Disable blank issue template and expose community chat (keradus)
* minor #5293 Add documentation to "yoda_style" sniff to convert Yoda style to non-Yoda style (Luc45)
* minor #5295 Run static code analysis off GitHub Actions (sanmai)
* minor #5298 Add yamllint workflow, validates .yaml files (sanmai)
* minor #5302 SingleLineCommentStyleFixer - do not fix possible attributes (PHP8) (SpacePossum)
* minor #5303 Drop CircleCI and AppVeyor (keradus)
* minor #5304 DX: rename TravisTest, as we no longer test only Travis there (keradus)
* minor #5305 Groom GitHub CI and move some checks from TravisCI to GitHub CI (keradus)
* minor #5308 Only run yamllint when a YAML file is changed (julienfalque, keradus)
* minor #5309 CICD: create yamllint config file (keradus)
* minor #5311 OrderedClassElementsFixer - PHPUnit Bridge support (ktomk)
* minor #5316 PHP8 - Attribute support (SpacePossum)
* minor #5321 DX: little code grooming (keradus)

Changelog for v2.16.8
---------------------

* bug #5325 NoBreakCommentFixer - better throw handling (SpacePossum)
* bug #5327 StaticLambdaFixer - fix for arrow function used in class with $this (kubawerlos, SpacePossum)
* bug #5333 Fix file missing for php8 (jderusse)
* minor #5328 Fixed deprecation message version (GrahamCampbell)
* minor #5330 DX: cleanup Github Actions configs (kubawerlos)

Changelog for v2.16.5
---------------------

* bug #4378 PhpUnitNoExpectationAnnotationFixer - annotation in single line doc comment (kubawerlos)
* bug #4936 HeaderCommentFixer - Fix unexpected removal of regular comments (julienfalque)
* bug #5006 PhpdocToParamTypeFixer - fix for breaking PHP syntax for type having reserved name (kubawerlos)
* bug #5016 NoSuperfluousPhpdocTagsFixer - fix for @return with @inheritDoc in description (kubawerlos)
* bug #5017 PhpdocTrimConsecutiveBlankLineSeparationFixer - must run after AlignMultilineCommentFixer (kubawerlos)
* bug #5032 SingleLineAfterImportsFixer - fix for line after import (and before another import) already added using CRLF (kubawerlos)
* bug #5033 VoidReturnFixer - must run after NoSuperfluousPhpdocTagsFixer (kubawerlos)
* bug #5038 HelpCommandTest - toString nested array (SpacePossum)
* bug #5040 LinebreakAfterOpeningTagFixer - do not change code if linebreak already present (kubawerlos)
* bug #5044 StandardizeIncrementFixer - fix handling static properties (kubawerlos)
* bug #5045 BacktickToShellExecFixer - add priority relation to NativeFunctionInvocationFixer and SingleQuoteFixer (kubawerlos)
* bug #5054 PhpdocTypesFixer - fix for multidimensional array (kubawerlos)
* bug #5065 TernaryOperatorSpacesFixer - fix for discovering ":" correctly (kubawerlos)
* bug #5068 Fixed php-cs-fixer crashes on input file syntax error (GrahamCampbell)
* bug #5087 NoAlternativeSyntaxFixer - add support for switch and declare (SpacePossum)
* bug #5092 PhpdocToParamTypeFixer - remove not used option (SpacePossum)
* bug #5105 ClassKeywordRemoveFixer - fix for fully qualified class (kubawerlos)
* bug #5113 TernaryOperatorSpacesFixer - handle goto labels (SpacePossum)
* bug #5124 Fix TernaryToNullCoalescingFixer when dealing with object properties (HypeMC)
* bug #5137 DoctrineAnnotationSpacesFixer - fix for typed properties (kubawerlos)
* bug #5180 Always lint test cases with the stricter process linter (GrahamCampbell)
* bug #5190 PhpUnit*Fixers - Only fix in unit test class scope (SpacePossum)
* bug #5195 YodaStyle - statements in braces should be treated as variables in strict … (SpacePossum)
* bug #5220 NoUnneededFinalMethodFixer - do not fix private constructors (SpacePossum)
* feature #3475 Rework documentation (julienfalque, SpacePossum)
* feature #5166 PHP8 (SpacePossum)
* minor #4878 ArrayIndentationFixer - refactor (julienfalque)
* minor #5031 CI: skip_cleanup: true (keradus)
* minor #5035 PhpdocToParamTypeFixer - Rename attribute (SpacePossum)
* minor #5048 Allow composer/semver ^2.0 and ^3.0 (thomasvargiu)
* minor #5050 DX: moving integration test for braces, indentation_type and no_break_comment into right place (kubawerlos)
* minor #5051 DX: move all tests from AutoReview\FixerTest to Test\AbstractFixerTestCase (kubawerlos)
* minor #5053 DX: cleanup FunctionTypehintSpaceFixer (kubawerlos)
* minor #5056 DX: add missing priority test for indentation_type and phpdoc_indent (kubawerlos)
* minor #5077 DX: add missing priority test between NoUnsetCastFixer and BinaryOperatorSpacesFixer (kubawerlos)
* minor #5083 Update composer.json to prevent issue #5030 (mvorisek)
* minor #5088 NoBreakCommentFixer - NoUselessElseFixer - priority test (SpacePossum)
* minor #5100 Fixed invalid PHP 5.6 syntax (GrahamCampbell)
* minor #5106 Symfony's finder already ignores vcs and dot files by default (GrahamCampbell)
* minor #5112 DX: check file permissions (kubawerlos, SpacePossum)
* minor #5122 Show runtime PHP version (kubawerlos)
* minor #5132 Do not allow assignments in if statements (SpacePossum)
* minor #5133 RuleSetTest - Early return for boolean and detect more defaults (SpacePossum)
* minor #5139 revert some unneeded exclusions (SpacePossum)
* minor #5148 Upgrade Xcode (kubawerlos)
* minor #5149 NoUnsetOnPropertyFixer - risky description tweaks (SpacePossum)
* minor #5161 minors (SpacePossum)
* minor #5170 Fix test on PHP8 (SpacePossum)
* minor #5172 Remove accidentally inserted newlines (GrahamCampbell)
* minor #5173 Fix PHP8 RuleSet inherit (SpacePossum)
* minor #5174 Corrected linting error messages (GrahamCampbell)
* minor #5177 PHP8 (SpacePossum)
* minor #5178 Fix tests (SpacePossum)
* minor #5184 [FinalStaticAccessFixer] Handle new static() in final class (localheinz)
* minor #5188 DX: Update sibling debs to version supporting PHP8/PHPUnit9 (keradus)
* minor #5189 Create temporary linting file in system temp dir (keradus)
* minor #5191 MethodArgumentSpaceFixer - support use/import of anonymous functions. (undefinedor)
* minor #5193 DX: add AbstractPhpUnitFixer (kubawerlos)
* minor #5204 DX: cleanup NullableTypeTransformerTest (kubawerlos)
* minor #5207 Add © for logo (keradus)
* minor #5208 DX: cleanup php-cs-fixer entry file (keradus)
* minor #5210 CICD - temporarily disable problematic test (keradus)
* minor #5211 CICD: fix file permissions (keradus)
* minor #5213 DX: move report schemas to dedicated dir (keradus)
* minor #5214 CICD: fix file permissions (keradus)
* minor #5215 CICD: update checkbashisms (keradus)
* minor #5217 CICD: use Composer v2 and drop hirak/prestissimo plugin (keradus)
* minor #5218 DX: .gitignore - add .phpunit.result.cache (keradus)
* minor #5222 Upgrade Xcode (kubawerlos)
* minor #5223 Docs: regenerate docs on 2.16 line (keradus)

Changelog for v2.16.4
---------------------

* bug #3893 Fix handling /** and */ on the same line as the first and/or last annotation (dmvdbrugge)
* bug #4919 PhpUnitTestAnnotationFixer - fix function starting with "test" and having lowercase letter after (kubawerlos)
* bug #4929 YodaStyleFixer - handling equals empty array (kubawerlos)
* bug #4934 YodaStyleFixer - fix for conditions weird are (kubawerlos)
* bug #4958 OrderedImportsFixer - fix for trailing comma in group (kubawerlos)
* bug #4959 BlankLineBeforeStatementFixer - handle comment case (SpacePossum)
* bug #4962 MethodArgumentSpaceFixer - must run after MethodChainingIndentationFixer (kubawerlos)
* bug #4963 PhpdocToReturnTypeFixer - fix for breaking PHP syntax for type having reserved name (kubawerlos, Slamdunk)
* bug #4978 ArrayIndentationFixer - must run after MethodArgumentSpaceFixer (kubawerlos)
* bug #4994 FinalInternalClassFixer - must run before ProtectedToPrivateFixer (kubawerlos)
* bug #4996 NoEmptyCommentFixer - handle multiline comments (kubawerlos)
* bug #4999 BlankLineBeforeStatementFixer - better comment handling (SpacePossum)
* bug #5009 NoEmptyCommentFixer - better handle comments sequence (kubawerlos)
* bug #5010 SimplifiedNullReturnFixer - must run before VoidReturnFixer (kubawerlos)
* bug #5011 SingleClassElementPerStatementFixer - must run before ClassAttributesSeparationFixer (kubawerlos)
* bug #5012 StrictParamFixer - must run before NativeFunctionInvocationFixer (kubawerlos)
* bug #5014 PhpdocToParamTypeFixer - fix for void as param (kubawerlos)
* bug #5018 PhpdocScalarFixer - fix for comment with Windows line endings (kubawerlos)
* bug #5029 SingleLineAfterImportsFixer - fix for line after import already added using CRLF (kubawerlos)
* minor #4904 Increase PHPStan level to 8 with strict rules (julienfalque)
* minor #4920 Enhancement: Use DocBlock itself to make it multi-line (localheinz)
* minor #4930 DX: ensure PhpUnitNamespacedFixer handles all classes (kubawerlos)
* minor #4931 DX: add test to ensure each target version in PhpUnitTargetVersion has its set in RuleSet (kubawerlos)
* minor #4932 DX: Travis CI config - fix warnings and infos (kubawerlos)
* minor #4940 Reject empty path (julienfalque)
* minor #4944 Fix grammar (julienfalque)
* minor #4946 Allow "const" option on PHP <7.1 (julienfalque)
* minor #4948 Added describe command to readme (david, 8ctopus)
* minor #4949 Fixed build readme on Windows fails if using Git Bash (Mintty) (8ctopus)
* minor #4954 Config - Trim path (julienfalque)
* minor #4957 DX: Check trailing spaces in project files only (ktomk)
* minor #4961 Assert all project source files are monolithic. (SpacePossum)
* minor #4964 Fix PHPStan baseline (julienfalque)
* minor #4965 Fix PHPStan baseline (julienfalque)
* minor #4973 DX: test "isRisky" method in fixer tests, not as auto review (kubawerlos)
* minor #4974 Minor: Fix typo (ktomk)
* minor #4975 Revert PHPStan level to 5 (julienfalque)
* minor #4976 Add instructions for PHPStan (julienfalque)
* minor #4980 Introduce new issue templates (julienfalque)
* minor #4981 Prevent error in CTTest::testConstants (for PHP8) (guilliamxavier)
* minor #4982 Remove PHIVE (kubawerlos)
* minor #4985 Fix tests with Symfony 5.1 (julienfalque)
* minor #4987 PhpdocAnnotationWithoutDotFixer - handle unicode characters using mb_* (SpacePossum)
* minor #5008 Enhancement: Social justification applied (gbyrka-fingo)
* minor #5023 Fix issue templates (kubawerlos)
* minor #5024 DX: add missing non-default code samples (kubawerlos)

Changelog for v2.16.3
---------------------

* bug #4915 Fix handling property PHPDocs with unsupported type (julienfalque)
* minor #4916 Fix AppVeyor build (julienfalque)
* minor #4917 CircleCI - Bump xcode to 11.4 (GrahamCampbell)
* minor #4918 DX: do not fix ".phpt" files by default (kubawerlos)

Changelog for v2.16.2
---------------------

* bug #3820 Braces - (re)indenting comment issues (SpacePossum)
* bug #3911 PhpdocVarWithoutNameFixer - fix for properties only (dmvdbrugge)
* bug #4601 ClassKeywordRemoveFixer - Fix for namespace (yassine-ah, kubawerlos)
* bug #4630 FullyQualifiedStrictTypesFixer - Ignore partial class names which look like FQCNs (localheinz, SpacePossum)
* bug #4661 ExplicitStringVariableFixer - variables pair if one is already explicit (kubawerlos)
* bug #4675 NonPrintableCharacterFixer - fix for backslash and quotes when changing to escape sequences (kubawerlos)
* bug #4678 TokensAnalyzer::isConstantInvocation - fix for importing multiple classes with single "use" (kubawerlos)
* bug #4682 Fix handling array type declaration in properties (julienfalque)
* bug #4685 Improve Symfony 5 compatibility (keradus)
* bug #4688 TokensAnalyzer::isConstantInvocation - Fix detection for fully qualified return type (julienfalque)
* bug #4689 DeclareStrictTypesFixer - fix for "strict_types" set to "0" (kubawerlos)
* bug #4690 PhpdocVarAnnotationCorrectOrderFixer - fix for multiline `@var` without type (kubawerlos)
* bug #4710 SingleTraitInsertPerStatement - fix formatting for multiline "use" (kubawerlos)
* bug #4711 Ensure that files from "tests" directory in release are autoloaded (kubawerlos)
* bug #4749 TokensAnalyze::isUnaryPredecessorOperator fix for CT::T_ARRAY_INDEX_C… (SpacePossum)
* bug #4759 Add more priority cases (SpacePossum)
* bug #4761 NoSuperfluousElseifFixer - handle single line (SpacePossum)
* bug #4783 NoSuperfluousPhpdocTagsFixer - fix for really big PHPDoc (kubawerlos, mvorisek)
* bug #4787 NoUnneededFinalMethodFixer - Mark as risky (SpacePossum)
* bug #4795 OrderedClassElementsFixer - Fix (SpacePossum)
* bug #4801 GlobalNamespaceImportFixer - fix docblock handling (gharlan)
* bug #4804 TokensAnalyzer::isUnarySuccessorOperator fix for array curly braces (SpacePossum)
* bug #4807 IncrementStyleFixer - handle after ")" (SpacePossum)
* bug #4808 Modernize types casting fixer array curly (SpacePossum)
* bug #4809 Fix "braces" and "method_argument_space" priority (julienfalque)
* bug #4813 BracesFixer - fix invalid code generation on alternative syntax (SpacePossum)
* bug #4822 fix 2 bugs in phpdoc_line_span (lmichelin)
* bug #4823 ReturnAssignmentFixer - repeat fix (SpacePossum)
* bug #4824 NoUnusedImportsFixer - SingleLineAfterImportsFixer - fix priority (SpacePossum)
* bug #4825 GlobalNamespaceImportFixer - do not import global into global (SpacePossum)
* bug #4829 YodaStyleFixer - fix precedence for T_MOD_EQUAL and T_COALESCE_EQUAL (SpacePossum)
* bug #4830 TernaryToNullCoalescingFixer - handle yield from (SpacePossum)
* bug #4835 Remove duplicate "function_to_constant" from RuleSet (SpacePossum)
* bug #4840 LineEndingFixer - T_CLOSE_TAG support, StringLineEndingFixer - T_INLI… (SpacePossum)
* bug #4846 FunctionsAnalyzer - better isGlobalFunctionCall detection (SpacePossum)
* bug #4852 Priority issues (SpacePossum)
* bug #4870 HeaderCommentFixer - do not remove class docs (gharlan)
* bug #4871 NoExtraBlankLinesFixer - handle cases on same line (SpacePossum)
* bug #4895 Fix conflict between header_comment and declare_strict_types (BackEndTea, julienfalque)
* bug #4911 PhpdocSeparationFixer - fix regression with lack of next line (keradus)
* feature #4742 FunctionToConstantFixer - get_class($this) support (SpacePossum)
* minor #4377 CommentsAnalyzer - fix for declare before header comment (kubawerlos)
* minor #4636 DX: do not check for PHPDBG when collecting coverage (kubawerlos)
* minor #4644 Docs: add info about "-vv..." (voku)
* minor #4691 Run Travis CI on stable PHP 7.4 (kubawerlos)
* minor #4693 Increase Travis CI Git clone depth (julienfalque)
* minor #4699 LineEndingFixer - handle "\r\r\n" (kubawerlos)
* minor #4703 NoSuperfluousPhpdocTagsFixer,PhpdocAddMissingParamAnnotationFixer - p… (SpacePossum)
* minor #4707 Fix typos (TysonAndre)
* minor #4712 NoBlankLinesAfterPhpdocFixer — Do not strip newline between docblock and use statements (mollierobbert)
* minor #4715 Enhancement: Install ergebnis/composer-normalize via Phive (localheinz)
* minor #4722 Fix Circle CI build (julienfalque)
* minor #4724 DX: Simplify installing PCOV (kubawerlos)
* minor #4736 NoUnusedImportsFixer - do not match variable name as import (SpacePossum)
* minor #4746 NoSuperfluousPhpdocTagsFixer - Remove for typed properties (PHP 7.4) (ruudk)
* minor #4753 Do not apply any text/.git filters to fixtures (mvorisek)
* minor #4757 Test $expected is used before $input (SpacePossum)
* minor #4758 Autoreview the PHPDoc of *Fixer::getPriority based on the priority map (SpacePossum)
* minor #4765 Add test on some return types (SpacePossum)
* minor #4766 Remove false test skip (SpacePossum)
* minor #4767 Remove useless priority comments (kubawerlos)
* minor #4769 DX: add missing priority tests (kubawerlos)
* minor #4772 NoUnneededFinalMethodFixer - update description (kubawerlos)
* minor #4774 DX: simplify Utils::camelCaseToUnderscore (kubawerlos)
* minor #4781 NoUnneededCurlyBracesFixer - handle namespaces (SpacePossum)
* minor #4784 Travis CI - Use multiple keyservers (ktomk)
* minor #4785 Improve static analysis (enumag)
* minor #4788 Configurable fixers code sample (SpacePossum)
* minor #4791 Increase PHPStan level to 3 (julienfalque)
* minor #4797 clean ups (SpacePossum)
* minor #4803 FinalClassFixer - Doctrine\ORM\Mapping as ORM alias should not be required (localheinz)
* minor #4839 2.15 - clean ups (SpacePossum)
* minor #4842 ReturnAssignmentFixer - Support more cases (julienfalque)
* minor #4843 NoSuperfluousPhpdocTagsFixer - fix typo in option description (OndraM)
* minor #4844 Same requirements for descriptions (SpacePossum)
* minor #4849 Increase PHPStan level to 5 (julienfalque)
* minor #4850 Fix phpstan (SpacePossum)
* minor #4857 Fixed the unit tests (GrahamCampbell)
* minor #4865 Use latest xcode image (GrahamCampbell)
* minor #4892 CombineNestedDirnameFixer - Add space after comma (julienfalque)
* minor #4894 DX: PhpdocToParamTypeFixer - improve typing (keradus)
* minor #4898 FixerTest - yield the data in AutoReview (Nyholm)
* minor #4899 Fix exception message format for fabbot.io (SpacePossum)
* minor #4905 Support composer v2 installed.json files (GrahamCampbell)
* minor #4906 CI: use Composer stable release for AppVeyor (kubawerlos)
* minor #4909 DX: HeaderCommentFixer - use non-aliased version of option name in code (keradus)
* minor #4912 CI: Fix AppVeyor integration (keradus)

Changelog for v2.16.1
---------------------

* bug #4476 FunctionsAnalyzer - add "isTheSameClassCall" for correct verifying of function calls (kubawerlos)
* bug #4605 PhpdocToParamTypeFixer - cover more cases (keradus, julienfalque)
* bug #4626 FinalPublicMethodForAbstractClassFixer - Do not attempt to mark abstract public methods as final (localheinz)
* bug #4632 NullableTypeDeclarationForDefaultNullValueFixer - fix for not lowercase "null" (kubawerlos)
* bug #4638 Ensure compatibility with PHP 7.4 (julienfalque)
* bug #4641 Add typed properties test to VisibilityRequiredFixerTest (GawainLynch, julienfalque)
* bug #4654 ArrayIndentationFixer - Fix array indentation for multiline values (julienfalque)
* bug #4660 TokensAnalyzer::isConstantInvocation - fix for extending multiple interfaces (kubawerlos)
* bug #4668 TokensAnalyzer::isConstantInvocation - fix for interface method return type (kubawerlos)
* minor #4608 Allow Symfony 5 components (l-vo)
* minor #4622 Disallow PHP 7.4 failures on Travis CI (julienfalque)
* minor #4623 README - Mark up as code (localheinz)
* minor #4637 PHP 7.4 integration test (GawainLynch, julienfalque)
* minor #4643 DX: Update .gitattributes and move ci-integration.sh to root of the project (kubawerlos, keradus)
* minor #4645 Check PHP extensions on runtime (kubawerlos)
* minor #4655 Improve docs - README (mvorisek)
* minor #4662 DX: generate headers in README.rst (kubawerlos)
* minor #4669 Enable execution under PHP 7.4 (keradus)
* minor #4670 TravisTest - rewrite tests to allow last supported by tool PHP version to be snapshot (keradus)
* minor #4671 TravisTest - rewrite tests to allow last supported by tool PHP version to be snapshot (keradus)

Changelog for v2.16.0
---------------------

* feature #3810 PhpdocLineSpanFixer - Introduction (BackEndTea)
* feature #3928 Add FinalPublicMethodForAbstractClassFixer (Slamdunk)
* feature #4000 FinalStaticAccessFixer - Introduction (ntzm)
* feature #4275 Issue #4274: Let lowercase_constants directive to be configurable. (drupol)
* feature #4355 GlobalNamespaceImportFixer - Introduction (gharlan)
* feature #4358 SelfStaticAccessorFixer - Introduction (SpacePossum)
* feature #4385 CommentToPhpdocFixer - allow to ignore tags (kubawerlos)
* feature #4401 Add NullableTypeDeclarationForDefaultNullValueFixer (HypeMC)
* feature #4452 Add SingleLineThrowFixer (kubawerlos)
* feature #4500 NoSuperfluousPhpdocTags - Add remove_inheritdoc option (julienfalque)
* feature #4505 NoSuperfluousPhpdocTagsFixer - allow params that aren't on the signature (azjezz)
* feature #4531 PhpdocAlignFixer - add "property-read" and "property-write" to allowed tags (kubawerlos)
* feature #4583 Phpdoc to param type fixer rebase (jg-development)
* minor #4033 Raise deprecation warnings on usage of deprecated aliases (ntzm)
* minor #4423 DX: update branch alias (keradus)
* minor #4537 SelfStaticAccessor - extend itests (keradus)
* minor #4607 Configure no_superfluous_phpdoc_tags for Symfony (keradus)
* minor #4618 DX: fix usage of deprecated options (0x450x6c)
* minor #4619 Fix PHP 7.3 strict mode warnings (keradus)
* minor #4621 Add single_line_throw to Symfony ruleset (keradus)

Changelog for v2.15.10
----------------------

* bug #5095 Annotation - fix for Windows line endings (SpacePossum)
* bug #5221 NoSuperfluousPhpdocTagsFixer - fix for single line PHPDoc (kubawerlos)
* bug #5225 TernaryOperatorSpacesFixer - fix for alternative control structures (kubawerlos)
* bug #5235 ArrayIndentationFixer - fix for nested arrays (kubawerlos)
* bug #5248 NoBreakCommentFixer - fix throw detect (SpacePossum)
* bug #5250 SwitchAnalyzer - fix for semicolon after case/default (kubawerlos)
* bug #5253 IO - fix cache info message (SpacePossum)
* bug #5274 TernaryToNullCoalescingFixer - concat precedence fix (SpacePossum)
* feature #5216 Add RuleSets to docs (SpacePossum)
* minor #5226 Applied CS fixes from 2.17-dev (GrahamCampbell)
* minor #5229 Fixed incorrect phpdoc (GrahamCampbell)
* minor #5231 CS: unify styling with younger branches (keradus)
* minor #5232 PHP8 - throw expression support (SpacePossum)
* minor #5233 DX: simplify check_file_permissions.sh (kubawerlos)
* minor #5236 Improve handling of unavailable code samples (julienfalque, keradus)
* minor #5239 PHP8 - Allow trailing comma in parameter list support (SpacePossum)
* minor #5254 PHP8 - mixed type support (SpacePossum)
* minor #5255 Tests: do not skip documentation test (keradus)
* minor #5261 Do not update Composer twice (sanmai)
* minor #5263 PHP8 support (SpacePossum)
* minor #5266 PhpUnitTestCaseStaticMethodCallsFixer - PHPUnit 9.x support (sanmai)
* minor #5267 Improve InstallViaComposerTest (sanmai)
* minor #5276 PHP8 - add NoUnreachableDefaultArgumentValueFixer to risky set (SpacePossum)

Changelog for v2.15.9
---------------------

* bug #4378 PhpUnitNoExpectationAnnotationFixer - annotation in single line doc comment (kubawerlos)
* bug #4936 HeaderCommentFixer - Fix unexpected removal of regular comments (julienfalque)
* bug #5017 PhpdocTrimConsecutiveBlankLineSeparationFixer - must run after AlignMultilineCommentFixer (kubawerlos)
* bug #5033 VoidReturnFixer - must run after NoSuperfluousPhpdocTagsFixer (kubawerlos)
* bug #5038 HelpCommandTest - toString nested array (SpacePossum)
* bug #5040 LinebreakAfterOpeningTagFixer - do not change code if linebreak already present (kubawerlos)
* bug #5044 StandardizeIncrementFixer - fix handling static properties (kubawerlos)
* bug #5045 BacktickToShellExecFixer - add priority relation to NativeFunctionInvocationFixer and SingleQuoteFixer (kubawerlos)
* bug #5054 PhpdocTypesFixer - fix for multidimensional array (kubawerlos)
* bug #5065 TernaryOperatorSpacesFixer - fix for discovering ":" correctly (kubawerlos)
* bug #5068 Fixed php-cs-fixer crashes on input file syntax error (GrahamCampbell)
* bug #5087 NoAlternativeSyntaxFixer - add support for switch and declare (SpacePossum)
* bug #5105 ClassKeywordRemoveFixer - fix for fully qualified class (kubawerlos)
* bug #5113 TernaryOperatorSpacesFixer - handle goto labels (SpacePossum)
* bug #5124 Fix TernaryToNullCoalescingFixer when dealing with object properties (HypeMC)
* bug #5137 DoctrineAnnotationSpacesFixer - fix for typed properties (kubawerlos)
* bug #5180 Always lint test cases with the stricter process linter (GrahamCampbell)
* bug #5190 PhpUnit*Fixers - Only fix in unit test class scope (SpacePossum)
* bug #5195 YodaStyle - statements in braces should be treated as variables in strict … (SpacePossum)
* bug #5220 NoUnneededFinalMethodFixer - do not fix private constructors (SpacePossum)
* feature #3475 Rework documentation (julienfalque, SpacePossum)
* feature #5166 PHP8 (SpacePossum)
* minor #4878 ArrayIndentationFixer - refactor (julienfalque)
* minor #5031 CI: skip_cleanup: true (keradus)
* minor #5048 Allow composer/semver ^2.0 and ^3.0 (thomasvargiu)
* minor #5050 DX: moving integration test for braces, indentation_type and no_break_comment into right place (kubawerlos)
* minor #5051 DX: move all tests from AutoReview\FixerTest to Test\AbstractFixerTestCase (kubawerlos)
* minor #5053 DX: cleanup FunctionTypehintSpaceFixer (kubawerlos)
* minor #5056 DX: add missing priority test for indentation_type and phpdoc_indent (kubawerlos)
* minor #5077 DX: add missing priority test between NoUnsetCastFixer and BinaryOperatorSpacesFixer (kubawerlos)
* minor #5083 Update composer.json to prevent issue #5030 (mvorisek)
* minor #5088 NoBreakCommentFixer - NoUselessElseFixer - priority test (SpacePossum)
* minor #5100 Fixed invalid PHP 5.6 syntax (GrahamCampbell)
* minor #5106 Symfony's finder already ignores vcs and dot files by default (GrahamCampbell)
* minor #5112 DX: check file permissions (kubawerlos, SpacePossum)
* minor #5122 Show runtime PHP version (kubawerlos)
* minor #5132 Do not allow assignments in if statements (SpacePossum)
* minor #5133 RuleSetTest - Early return for boolean and detect more defaults (SpacePossum)
* minor #5139 revert some unneeded exclusions (SpacePossum)
* minor #5148 Upgrade Xcode (kubawerlos)
* minor #5149 NoUnsetOnPropertyFixer - risky description tweaks (SpacePossum)
* minor #5161 minors (SpacePossum)
* minor #5172 Remove accidentally inserted newlines (GrahamCampbell)
* minor #5173 Fix PHP8 RuleSet inherit (SpacePossum)
* minor #5174 Corrected linting error messages (GrahamCampbell)
* minor #5177 PHP8 (SpacePossum)
* minor #5188 DX: Update sibling debs to version supporting PHP8/PHPUnit9 (keradus)
* minor #5189 Create temporary linting file in system temp dir (keradus)
* minor #5191 MethodArgumentSpaceFixer - support use/import of anonymous functions. (undefinedor)
* minor #5193 DX: add AbstractPhpUnitFixer (kubawerlos)
* minor #5204 DX: cleanup NullableTypeTransformerTest (kubawerlos)
* minor #5207 Add © for logo (keradus)
* minor #5208 DX: cleanup php-cs-fixer entry file (keradus)
* minor #5210 CICD - temporarily disable problematic test (keradus)
* minor #5211 CICD: fix file permissions (keradus)
* minor #5213 DX: move report schemas to dedicated dir (keradus)
* minor #5214 CICD: fix file permissions (keradus)
* minor #5215 CICD: update checkbashisms (keradus)
* minor #5217 CICD: use Composer v2 and drop hirak/prestissimo plugin (keradus)
* minor #5218 DX: .gitignore - add .phpunit.result.cache (keradus)
* minor #5222 Upgrade Xcode (kubawerlos)

Changelog for v2.15.8
---------------------

* bug #3893 Fix handling /** and */ on the same line as the first and/or last annotation (dmvdbrugge)
* bug #4919 PhpUnitTestAnnotationFixer - fix function starting with "test" and having lowercase letter after (kubawerlos)
* bug #4929 YodaStyleFixer - handling equals empty array (kubawerlos)
* bug #4934 YodaStyleFixer - fix for conditions weird are (kubawerlos)
* bug #4958 OrderedImportsFixer - fix for trailing comma in group (kubawerlos)
* bug #4959 BlankLineBeforeStatementFixer - handle comment case (SpacePossum)
* bug #4962 MethodArgumentSpaceFixer - must run after MethodChainingIndentationFixer (kubawerlos)
* bug #4963 PhpdocToReturnTypeFixer - fix for breaking PHP syntax for type having reserved name (kubawerlos, Slamdunk)
* bug #4978 ArrayIndentationFixer - must run after MethodArgumentSpaceFixer (kubawerlos)
* bug #4994 FinalInternalClassFixer - must run before ProtectedToPrivateFixer (kubawerlos)
* bug #4996 NoEmptyCommentFixer - handle multiline comments (kubawerlos)
* bug #4999 BlankLineBeforeStatementFixer - better comment handling (SpacePossum)
* bug #5009 NoEmptyCommentFixer - better handle comments sequence (kubawerlos)
* bug #5010 SimplifiedNullReturnFixer - must run before VoidReturnFixer (kubawerlos)
* bug #5011 SingleClassElementPerStatementFixer - must run before ClassAttributesSeparationFixer (kubawerlos)
* bug #5012 StrictParamFixer - must run before NativeFunctionInvocationFixer (kubawerlos)
* bug #5029 SingleLineAfterImportsFixer - fix for line after import already added using CRLF (kubawerlos)
* minor #4904 Increase PHPStan level to 8 with strict rules (julienfalque)
* minor #4930 DX: ensure PhpUnitNamespacedFixer handles all classes (kubawerlos)
* minor #4931 DX: add test to ensure each target version in PhpUnitTargetVersion has its set in RuleSet (kubawerlos)
* minor #4932 DX: Travis CI config - fix warnings and infos (kubawerlos)
* minor #4940 Reject empty path (julienfalque)
* minor #4944 Fix grammar (julienfalque)
* minor #4946 Allow "const" option on PHP <7.1 (julienfalque)
* minor #4948 Added describe command to readme (david, 8ctopus)
* minor #4949 Fixed build readme on Windows fails if using Git Bash (Mintty) (8ctopus)
* minor #4954 Config - Trim path (julienfalque)
* minor #4957 DX: Check trailing spaces in project files only (ktomk)
* minor #4961 Assert all project source files are monolithic. (SpacePossum)
* minor #4964 Fix PHPStan baseline (julienfalque)
* minor #4973 DX: test "isRisky" method in fixer tests, not as auto review (kubawerlos)
* minor #4974 Minor: Fix typo (ktomk)
* minor #4975 Revert PHPStan level to 5 (julienfalque)
* minor #4976 Add instructions for PHPStan (julienfalque)
* minor #4980 Introduce new issue templates (julienfalque)
* minor #4981 Prevent error in CTTest::testConstants (for PHP8) (guilliamxavier)
* minor #4982 Remove PHIVE (kubawerlos)
* minor #4985 Fix tests with Symfony 5.1 (julienfalque)
* minor #4987 PhpdocAnnotationWithoutDotFixer - handle unicode characters using mb_* (SpacePossum)
* minor #5008 Enhancement: Social justification applied (gbyrka-fingo)
* minor #5023 Fix issue templates (kubawerlos)
* minor #5024 DX: add missing non-default code samples (kubawerlos)

Changelog for v2.15.7
---------------------

* bug #4915 Fix handling property PHPDocs with unsupported type (julienfalque)
* minor #4916 Fix AppVeyor build (julienfalque)
* minor #4917 CircleCI - Bump xcode to 11.4 (GrahamCampbell)
* minor #4918 DX: do not fix ".phpt" files by default (kubawerlos)

Changelog for v2.15.6
---------------------

* bug #3820 Braces - (re)indenting comment issues (SpacePossum)
* bug #3911 PhpdocVarWithoutNameFixer - fix for properties only (dmvdbrugge)
* bug #4601 ClassKeywordRemoveFixer - Fix for namespace (yassine-ah, kubawerlos)
* bug #4630 FullyQualifiedStrictTypesFixer - Ignore partial class names which look like FQCNs (localheinz, SpacePossum)
* bug #4661 ExplicitStringVariableFixer - variables pair if one is already explicit (kubawerlos)
* bug #4675 NonPrintableCharacterFixer - fix for backslash and quotes when changing to escape sequences (kubawerlos)
* bug #4678 TokensAnalyzer::isConstantInvocation - fix for importing multiple classes with single "use" (kubawerlos)
* bug #4682 Fix handling array type declaration in properties (julienfalque)
* bug #4685 Improve Symfony 5 compatibility (keradus)
* bug #4688 TokensAnalyzer::isConstantInvocation - Fix detection for fully qualified return type (julienfalque)
* bug #4689 DeclareStrictTypesFixer - fix for "strict_types" set to "0" (kubawerlos)
* bug #4690 PhpdocVarAnnotationCorrectOrderFixer - fix for multiline `@var` without type (kubawerlos)
* bug #4710 SingleTraitInsertPerStatement - fix formatting for multiline "use" (kubawerlos)
* bug #4711 Ensure that files from "tests" directory in release are autoloaded (kubawerlos)
* bug #4749 TokensAnalyze::isUnaryPredecessorOperator fix for CT::T_ARRAY_INDEX_C… (SpacePossum)
* bug #4759 Add more priority cases (SpacePossum)
* bug #4761 NoSuperfluousElseifFixer - handle single line (SpacePossum)
* bug #4783 NoSuperfluousPhpdocTagsFixer - fix for really big PHPDoc (kubawerlos, mvorisek)
* bug #4787 NoUnneededFinalMethodFixer - Mark as risky (SpacePossum)
* bug #4795 OrderedClassElementsFixer - Fix (SpacePossum)
* bug #4804 TokensAnalyzer::isUnarySuccessorOperator fix for array curly braces (SpacePossum)
* bug #4807 IncrementStyleFixer - handle after ")" (SpacePossum)
* bug #4808 Modernize types casting fixer array curly (SpacePossum)
* bug #4809 Fix "braces" and "method_argument_space" priority (julienfalque)
* bug #4813 BracesFixer - fix invalid code generation on alternative syntax (SpacePossum)
* bug #4823 ReturnAssignmentFixer - repeat fix (SpacePossum)
* bug #4824 NoUnusedImportsFixer - SingleLineAfterImportsFixer - fix priority (SpacePossum)
* bug #4829 YodaStyleFixer - fix precedence for T_MOD_EQUAL and T_COALESCE_EQUAL (SpacePossum)
* bug #4830 TernaryToNullCoalescingFixer - handle yield from (SpacePossum)
* bug #4835 Remove duplicate "function_to_constant" from RuleSet (SpacePossum)
* bug #4840 LineEndingFixer - T_CLOSE_TAG support, StringLineEndingFixer - T_INLI… (SpacePossum)
* bug #4846 FunctionsAnalyzer - better isGlobalFunctionCall detection (SpacePossum)
* bug #4852 Priority issues (SpacePossum)
* bug #4870 HeaderCommentFixer - do not remove class docs (gharlan)
* bug #4871 NoExtraBlankLinesFixer - handle cases on same line (SpacePossum)
* bug #4895 Fix conflict between header_comment and declare_strict_types (BackEndTea, julienfalque)
* bug #4911 PhpdocSeparationFixer - fix regression with lack of next line (keradus)
* feature #4742 FunctionToConstantFixer - get_class($this) support (SpacePossum)
* minor #4377 CommentsAnalyzer - fix for declare before header comment (kubawerlos)
* minor #4636 DX: do not check for PHPDBG when collecting coverage (kubawerlos)
* minor #4644 Docs: add info about "-vv..." (voku)
* minor #4691 Run Travis CI on stable PHP 7.4 (kubawerlos)
* minor #4693 Increase Travis CI Git clone depth (julienfalque)
* minor #4699 LineEndingFixer - handle "\r\r\n" (kubawerlos)
* minor #4703 NoSuperfluousPhpdocTagsFixer,PhpdocAddMissingParamAnnotationFixer - p… (SpacePossum)
* minor #4707 Fix typos (TysonAndre)
* minor #4712 NoBlankLinesAfterPhpdocFixer — Do not strip newline between docblock and use statements (mollierobbert)
* minor #4715 Enhancement: Install ergebnis/composer-normalize via Phive (localheinz)
* minor #4722 Fix Circle CI build (julienfalque)
* minor #4724 DX: Simplify installing PCOV (kubawerlos)
* minor #4736 NoUnusedImportsFixer - do not match variable name as import (SpacePossum)
* minor #4746 NoSuperfluousPhpdocTagsFixer - Remove for typed properties (PHP 7.4) (ruudk)
* minor #4753 Do not apply any text/.git filters to fixtures (mvorisek)
* minor #4757 Test $expected is used before $input (SpacePossum)
* minor #4758 Autoreview the PHPDoc of *Fixer::getPriority based on the priority map (SpacePossum)
* minor #4765 Add test on some return types (SpacePossum)
* minor #4766 Remove false test skip (SpacePossum)
* minor #4767 Remove useless priority comments (kubawerlos)
* minor #4769 DX: add missing priority tests (kubawerlos)
* minor #4772 NoUnneededFinalMethodFixer - update description (kubawerlos)
* minor #4774 DX: simplify Utils::camelCaseToUnderscore (kubawerlos)
* minor #4781 NoUnneededCurlyBracesFixer - handle namespaces (SpacePossum)
* minor #4784 Travis CI - Use multiple keyservers (ktomk)
* minor #4785 Improve static analysis (enumag)
* minor #4788 Configurable fixers code sample (SpacePossum)
* minor #4791 Increase PHPStan level to 3 (julienfalque)
* minor #4797 clean ups (SpacePossum)
* minor #4803 FinalClassFixer - Doctrine\ORM\Mapping as ORM alias should not be required (localheinz)
* minor #4839 2.15 - clean ups (SpacePossum)
* minor #4842 ReturnAssignmentFixer - Support more cases (julienfalque)
* minor #4844 Same requirements for descriptions (SpacePossum)
* minor #4849 Increase PHPStan level to 5 (julienfalque)
* minor #4857 Fixed the unit tests (GrahamCampbell)
* minor #4865 Use latest xcode image (GrahamCampbell)
* minor #4892 CombineNestedDirnameFixer - Add space after comma (julienfalque)
* minor #4898 FixerTest - yield the data in AutoReview (Nyholm)
* minor #4899 Fix exception message format for fabbot.io (SpacePossum)
* minor #4905 Support composer v2 installed.json files (GrahamCampbell)
* minor #4906 CI: use Composer stable release for AppVeyor (kubawerlos)
* minor #4909 DX: HeaderCommentFixer - use non-aliased version of option name in code (keradus)
* minor #4912 CI: Fix AppVeyor integration (keradus)

Changelog for v2.15.5
---------------------

* bug #4476 FunctionsAnalyzer - add "isTheSameClassCall" for correct verifying of function calls (kubawerlos)
* bug #4641 Add typed properties test to VisibilityRequiredFixerTest (GawainLynch, julienfalque)
* bug #4654 ArrayIndentationFixer - Fix array indentation for multiline values (julienfalque)
* bug #4660 TokensAnalyzer::isConstantInvocation - fix for extending multiple interfaces (kubawerlos)
* bug #4668 TokensAnalyzer::isConstantInvocation - fix for interface method return type (kubawerlos)
* minor #4608 Allow Symfony 5 components (l-vo)
* minor #4622 Disallow PHP 7.4 failures on Travis CI (julienfalque)
* minor #4637 PHP 7.4 integration test (GawainLynch, julienfalque)
* minor #4643 DX: Update .gitattributes and move ci-integration.sh to root of the project (kubawerlos, keradus)
* minor #4645 Check PHP extensions on runtime (kubawerlos)
* minor #4655 Improve docs - README (mvorisek)
* minor #4662 DX: generate headers in README.rst (kubawerlos)
* minor #4669 Enable execution under PHP 7.4 (keradus)
* minor #4671 TravisTest - rewrite tests to allow last supported by tool PHP version to be snapshot (keradus)

Changelog for v2.15.4
---------------------

* bug #4183 IndentationTypeFixer - fix handling 2 spaces indent (kubawerlos)
* bug #4406 NoSuperfluousElseifFixer - fix invalid escape sequence in character class (remicollet, SpacePossum)
* bug #4416 NoUnusedImports - Fix imports detected as used in namespaces (julienfalque, SpacePossum)
* bug #4518 PhpUnitNoExpectationAnnotationFixer - fix handling expect empty exception message (ktomk)
* bug #4548 HeredocIndentationFixer - remove whitespace in empty lines (gharlan)
* bug #4556 ClassKeywordRemoveFixer - fix for self,static and parent keywords (kubawerlos)
* bug #4572 TokensAnalyzer - handle nested anonymous classes (SpacePossum)
* bug #4573 CombineConsecutiveIssetsFixer - fix stop based on precedence (SpacePossum)
* bug #4577 Fix command exit code on lint error after fixing fix. (SpacePossum)
* bug #4581 FunctionsAnalyzer: fix for comment in type (kubawerlos)
* bug #4586 BracesFixer - handle dynamic static method call (SpacePossum)
* bug #4594 Braces - fix both single line comment styles (SpacePossum)
* bug #4609 PhpdocTypesOrderFixer - Prevent unexpected default value change (laurent35240)
* minor #4458 Add PHPStan (julienfalque)
* minor #4479 IncludeFixer - remove braces when the statement is wrapped in block (kubawerlos)
* minor #4490 Allow running if installed as project specific (ticktackk)
* minor #4517 Verify PCRE pattern before use (ktomk)
* minor #4521 Remove superfluous leading backslash, closes 4520 (ktomk)
* minor #4532 DX: ensure data providers are used (kubawerlos)
* minor #4534 Redo PHP7.4 - Add "str_split" => "mb_str_split" mapping (keradus, Slamdunk)
* minor #4536 DX: use PHIVE for dev tools (keradus)
* minor #4538 Docs: update Cookbook (keradus)
* minor #4541 Enhancement: Use default name property to configure command names (localheinz)
* minor #4546 DX: removing unnecessary variable initialization (kubawerlos)
* minor #4549 DX: use ::class whenever possible (keradus, kubawerlos)
* minor #4550 DX: travis_retry for dev-tools install (ktomk, keradus)
* minor #4559 Allow 7.4snapshot to fail due to a bug on it (kubawerlos)
* minor #4563 GitlabReporter - fix report output (mjanser)
* minor #4564 Move readme-update command to Section 3 (iwasherefirst2)
* minor #4566 Update symfony ruleset (gharlan)
* minor #4570 Command::execute() should always return an integer (derrabus)
* minor #4580 Add support for true/false return type hints. (SpacePossum)
* minor #4584 Increase PHPStan level to 1 (julienfalque)
* minor #4585 Fix deprecation notices (julienfalque)
* minor #4587 Output details - Explain why a file was skipped (SpacePossum)
* minor #4588 Fix STDIN test when path is one level deep (julienfalque)
* minor #4589 PhpdocToReturnType - Add support for Foo[][] (SpacePossum)
* minor #4593 Ensure compatibility with PHP 7.4 typed properties (julienfalque)
* minor #4595 Import cannot be used after `::` so can be removed (SpacePossum)
* minor #4596 Ensure compatibility with PHP 7.4 numeric literal separator (julienfalque)
* minor #4597 Fix PHP 7.4 deprecation notices (julienfalque)
* minor #4600 Ensure compatibility with PHP 7.4 arrow functions (julienfalque)
* minor #4602 Ensure compatibility with PHP 7.4 spread operator in array expression (julienfalque)
* minor #4603 Ensure compatibility with PHP 7.4 null coalescing assignment operator (julienfalque)
* minor #4606 Configure no_superfluous_phpdoc_tags for Symfony (keradus)
* minor #4610 Travis CI - Update known files list (julienfalque)
* minor #4615 Remove workaround for dev-tools install reg. Phive (ktomk)

Changelog for v2.15.3
---------------------

* bug #4533 Revert PHP7.4 - Add "str_split" => "mb_str_split" mapping (keradus)
* minor #4264 DX: AutoReview - ensure Travis handle all needed PHP versions (keradus)
* minor #4524 MethodArgumentSpaceFixerTest - make explicit configuration to prevent fail on configuration change (keradus)

Changelog for v2.15.2
---------------------

* bug #4132 BlankLineAfterNamespaceFixer - do not remove indent, handle comments (kubawerlos)
* bug #4384 MethodArgumentSpaceFixer - fix for on_multiline:ensure_fully_multiline with trailing comma in function call (kubawerlos)
* bug #4404 FileLintingIterator - fix current value on end/invalid (SpacePossum)
* bug #4421 FunctionTypehintSpaceFixer - Ensure single space between type declaration and parameter (localheinz)
* bug #4436 MethodArgumentSpaceFixer - handle misplaced ) (keradus)
* bug #4439 NoLeadingImportSlashFixer - Add space if needed (SpacePossum)
* bug #4440 SimpleToComplexStringVariableFixer - Fix $ bug (dmvdbrugge)
* bug #4453 Fix preg_match error on 7.4snapshot (kubawerlos)
* bug #4461 IsNullFixer - fix null coalescing operator handling (linniksa)
* bug #4467 ToolInfo - fix access to reference without checking existence (black-silence)
* bug #4472 Fix non-static closure unbinding this on PHP 7.4 (kelunik)
* minor #3726 Use Box 3 to build the PHAR (theofidry, keradus)
* minor #4412 PHP 7.4 - Tests for support (SpacePossum)
* minor #4431 DX: test that default config is not passed in RuleSet (kubawerlos)
* minor #4433 DX: test to ensure @PHPUnitMigration rule sets are correctly defined (kubawerlos)
* minor #4445 DX: static call of markTestSkippedOrFail (kubawerlos)
* minor #4463 Add apostrophe to possessive "team's" (ChandlerSwift)
* minor #4471 ReadmeCommandTest - use CommandTester (kubawerlos)
* minor #4477 DX: control names of public methods in test's classes (kubawerlos)
* minor #4483 NewWithBracesFixer - Fix object operator and curly brace open cases (SpacePossum)
* minor #4484 fix typos in README (Sven Ludwig)
* minor #4494 DX: Fix shell script syntax in order to fix Travis builds (drupol)
* minor #4516 DX: Lock binary SCA tools versions (keradus)

Changelog for v2.15.1
---------------------

* bug #4418 PhpUnitNamespacedFixer - properly translate classes which do not follow translation pattern (ktomk)
* bug #4419 PhpUnitTestCaseStaticMethodCallsFixer - skip anonymous classes and lambda (SpacePossum)
* bug #4420 MethodArgumentSpaceFixer - PHP7.3 trailing commas in function calls (SpacePossum)
* minor #4345 Travis: PHP 7.4 isn't allowed to fail anymore (Slamdunk)
* minor #4403 LowercaseStaticReferenceFixer - Fix invalid PHP version in example (HypeMC)
* minor #4424 DX: cleanup of composer.json - no need for branch-alias (keradus)
* minor #4425 DX: assertions are static, adjust custom assertions (keradus)
* minor #4426 DX: handle deprecations of symfony/event-dispatcher:4.3 (keradus)
* minor #4427 DX: stop using reserved T_FN in code samples (keradus)
* minor #4428 DX: update dev-tools (keradus)
* minor #4429 DX: MethodArgumentSpaceFixerTest - fix hidden merge conflict (keradus)

Changelog for v2.15.0
---------------------

* feature #3927 Add FinalClassFixer (Slamdunk)
* feature #3939 Add PhpUnitSizeClassFixer (Jefersson Nathan)
* feature #3942 SimpleToComplexStringVariableFixer - Introduction (dmvdbrugge, SpacePossum)
* feature #4113 OrderedInterfacesFixer - Introduction (dmvdbrugge)
* feature #4121 SingleTraitInsertPerStatementFixer - Introduction (SpacePossum)
* feature #4126 NativeFunctionTypeDeclarationCasingFixer - Introduction (SpacePossum)
* feature #4167 PhpUnitMockShortWillReturnFixer - Introduction (michadam-pearson)
* feature #4191 [7.3] NoWhitespaceBeforeCommaInArrayFixer - fix comma after heredoc-end (gharlan)
* feature #4288 Add Gitlab Reporter (hco)
* feature #4328 Add PhpUnitDedicateAssertInternalTypeFixer (Slamdunk)
* feature #4341 [7.3] TrailingCommaInMultilineArrayFixer - fix comma after heredoc-end (gharlan)
* feature #4342 [7.3] MethodArgumentSpaceFixer - fix comma after heredoc-end (gharlan)
* minor #4112 NoSuperfluousPhpdocTagsFixer - Add missing code sample, groom tests (keradus, SpacePossum)
* minor #4360 Add gitlab as output format in the README/help doc. (SpacePossum)
* minor #4386 Add PhpUnitMockShortWillReturnFixer to @Symfony:risky rule set (kubawerlos)
* minor #4398 New ruleset "@PHP73Migration" (gharlan)
* minor #4399 Fix 2.15 line (keradus)

Changelog for v2.14.6
---------------------

* bug #4533 Revert PHP7.4 - Add "str_split" => "mb_str_split" mapping (keradus)
* minor #4264 DX: AutoReview - ensure Travis handle all needed PHP versions (keradus)
* minor #4524 MethodArgumentSpaceFixerTest - make explicit configuration to prevent fail on configuration change (keradus)

Changelog for v2.14.5
---------------------

* bug #4132 BlankLineAfterNamespaceFixer - do not remove indent, handle comments (kubawerlos)
* bug #4384 MethodArgumentSpaceFixer - fix for on_multiline:ensure_fully_multiline with trailing comma in function call (kubawerlos)
* bug #4404 FileLintingIterator - fix current value on end/invalid (SpacePossum)
* bug #4421 FunctionTypehintSpaceFixer - Ensure single space between type declaration and parameter (localheinz)
* bug #4436 MethodArgumentSpaceFixer - handle misplaced ) (keradus)
* bug #4439 NoLeadingImportSlashFixer - Add space if needed (SpacePossum)
* bug #4453 Fix preg_match error on 7.4snapshot (kubawerlos)
* bug #4461 IsNullFixer - fix null coalescing operator handling (linniksa)
* bug #4467 ToolInfo - fix access to reference without checking existence (black-silence)
* bug #4472 Fix non-static closure unbinding this on PHP 7.4 (kelunik)
* minor #3726 Use Box 3 to build the PHAR (theofidry, keradus)
* minor #4412 PHP 7.4 - Tests for support (SpacePossum)
* minor #4431 DX: test that default config is not passed in RuleSet (kubawerlos)
* minor #4433 DX: test to ensure @PHPUnitMigration rule sets are correctly defined (kubawerlos)
* minor #4445 DX: static call of markTestSkippedOrFail (kubawerlos)
* minor #4463 Add apostrophe to possessive "team's" (ChandlerSwift)
* minor #4471 ReadmeCommandTest - use CommandTester (kubawerlos)
* minor #4477 DX: control names of public methods in test's classes (kubawerlos)
* minor #4483 NewWithBracesFixer - Fix object operator and curly brace open cases (SpacePossum)
* minor #4484 fix typos in README (Sven Ludwig)
* minor #4494 DX: Fix shell script syntax in order to fix Travis builds (drupol)
* minor #4516 DX: Lock binary SCA tools versions (keradus)

Changelog for v2.14.4
---------------------

* bug #4418 PhpUnitNamespacedFixer - properly translate classes which do not follow translation pattern (ktomk)
* bug #4419 PhpUnitTestCaseStaticMethodCallsFixer - skip anonymous classes and lambda (SpacePossum)
* bug #4420 MethodArgumentSpaceFixer - PHP7.3 trailing commas in function calls (SpacePossum)
* minor #4345 Travis: PHP 7.4 isn't allowed to fail anymore (Slamdunk)
* minor #4403 LowercaseStaticReferenceFixer - Fix invalid PHP version in example (HypeMC)
* minor #4425 DX: assertions are static, adjust custom assertions (keradus)
* minor #4426 DX: handle deprecations of symfony/event-dispatcher:4.3 (keradus)
* minor #4427 DX: stop using reserved T_FN in code samples (keradus)
* minor #4428 DX: update dev-tools (keradus)

Changelog for v2.14.3
---------------------

* bug #4298 NoTrailingWhitespaceInCommentFixer - fix for non-Unix line separators (kubawerlos)
* bug #4303 FullyQualifiedStrictTypesFixer - Fix the short type detection when a question mark (nullable) is prefixing it. (drupol)
* bug #4313 SelfAccessorFixer - fix for part qualified class name (kubawerlos, SpacePossum)
* bug #4314 PhpUnitTestCaseStaticMethodCallsFixer - fix for having property with name as method to update (kubawerlos, SpacePossum)
* bug #4316 NoUnsetCastFixer - Test for higher-precedence operators (SpacePossum)
* bug #4327 TokensAnalyzer - add concat operator to list of binary operators (SpacePossum)
* bug #4335 Cache - add indent and line ending to cache signature (dmvdbrugge)
* bug #4344 VoidReturnFixer - handle yield from (SpacePossum)
* bug #4346 BracesFixer - Do not pull close tag onto same line as a comment (SpacePossum)
* bug #4350 StrictParamFixer - Don't detect functions in use statements (bolmstedt)
* bug #4357 Fix short list syntax detection. (SpacePossum)
* bug #4365 Fix output escaping of diff for text format when line is not changed (SpacePossum)
* bug #4370 PhpUnitConstructFixer - Fix handle different casing (SpacePossum)
* bug #4379 ExplicitStringVariableFixer - add test case for variable as an array key (kubawerlos, Slamdunk)
* feature #4337 PhpUnitTestCaseStaticMethodCallsFixer - prepare for PHPUnit 8 (kubawerlos)
* minor #3799 DX: php_unit_test_case_static_method_calls - use default config (keradus)
* minor #4103 NoExtraBlankLinesFixer - fix candidate detection (SpacePossum)
* minor #4245 LineEndingFixer - BracesFixer - Priority (dmvdbrugge)
* minor #4325 Use lowercase mikey179/vfsStream in composer.json (lolli42)
* minor #4336 Collect coverage with PCOV (kubawerlos)
* minor #4338 Fix wording (kmvan, kubawerlos)
* minor #4339 Change BracesFixer to avoid indenting PHP inline braces (alecgeatches)
* minor #4340 Travis: build against 7.4snapshot instead of nightly (Slamdunk)
* minor #4351 code grooming (SpacePossum)
* minor #4353 Add more priority tests (SpacePossum)
* minor #4364 DX: MethodChainingIndentationFixer - remove unnecessary loop (Sijun Zhu)
* minor #4366 Unset the auxiliary variable $a (GrahamCampbell)
* minor #4368 Fixed TypeShortNameResolverTest::testResolver (GrahamCampbell)
* minor #4380 PHP7.4 - Add "str_split" => "mb_str_split" mapping. (SpacePossum)
* minor #4381 PHP7.4 - Add support for magic methods (un)serialize. (SpacePossum)
* minor #4393 DX: add missing explicit return types (kubawerlos)

Changelog for v2.14.2
---------------------

* minor #4306 DX: Drop HHVM conflict on Composer level to help Composer with HHVM compatibility, we still prevent HHVM on runtime (keradus)

Changelog for v2.14.1
---------------------

* bug #4240 ModernizeTypesCastingFixer - fix for operators with higher precedence (kubawerlos)
* bug #4254 PhpUnitDedicateAssertFixer - fix for count with additional operations (kubawerlos)
* bug #4260 Psr0Fixer and Psr4Fixer  - fix for multiple classes in file with anonymous class (kubawerlos)
* bug #4262 FixCommand - fix help (keradus)
* bug #4276 MethodChainingIndentationFixer, ArrayIndentationFixer - Fix priority issue (dmvdbrugge)
* bug #4280 MethodArgumentSpaceFixer - Fix method argument alignment (Billz95)
* bug #4286 IncrementStyleFixer - fix for static statement (kubawerlos)
* bug #4291 ArrayIndentationFixer - Fix indentation after trailing spaces (julienfalque, keradus)
* bug #4292 NoSuperfluousPhpdocTagsFixer - Make null only type not considered superfluous (julienfalque)
* minor #4204 DX: Tokens - do not unregister/register found tokens when collection is not changing (kubawerlos)
* minor #4235 DX: more specific @param types (kubawerlos)
* minor #4263 DX: AppVeyor - bump PHP version (keradus)
* minor #4293 Add official support for PHP 7.3 (keradus)
* minor #4295 DX: MethodArgumentSpaceFixerTest - fix edge case for handling different line ending when only expected code is provided (keradus)
* minor #4296 DX: cleanup testing with fixer config (keradus)
* minor #4299 NativeFunctionInvocationFixer - add array_key_exists (deguif, keradus)
* minor #4300 DX: cleanup testing with fixer config (keradus)

Changelog for v2.14.0
---------------------

* bug #4220 NativeFunctionInvocationFixer - namespaced strict to remove backslash (kubawerlos)
* feature #3881 Add PhpdocVarAnnotationCorrectOrderFixer (kubawerlos)
* feature #3915 Add HeredocIndentationFixer (gharlan)
* feature #4002 NoSuperfluousPhpdocTagsFixer - Allow `mixed` in superfluous PHPDoc by configuration (MortalFlesh)
* feature #4030 Add get_required_files and user_error aliases (ntzm)
* feature #4043 NativeFunctionInvocationFixer - add option to remove redundant backslashes (kubawerlos)
* feature #4102 Add NoUnsetCastFixer (SpacePossum)
* minor #4025 Add phpdoc_types_order rule to Symfony's ruleset (carusogabriel)
* minor #4213 [7.3] PHP7.3 integration tests (SpacePossum)
* minor #4233 Add official support for PHP 7.3 (keradus)

Changelog for v2.13.3
---------------------

* bug #4216 Psr4Fixer - fix for multiple classy elements in file (keradus, kubawerlos)
* bug #4217 Psr0Fixer - class with anonymous class (kubawerlos)
* bug #4219  NativeFunctionCasingFixer - handle T_RETURN_REF  (kubawerlos)
* bug #4224 FunctionToConstantFixer - handle T_RETURN_REF (SpacePossum)
* bug #4229 IsNullFixer - fix parenthesis not closed (guilliamxavier)
* minor #4193 [7.3] CombineNestedDirnameFixer - support PHP 7.3 (kubawerlos)
* minor #4198 [7.3] PowToExponentiationFixer - adding to PHP7.3 integration test (kubawerlos)
* minor #4199 [7.3] MethodChainingIndentationFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4200 [7.3] ModernizeTypesCastingFixer - support PHP 7.3 (kubawerlos)
* minor #4201 [7.3] MultilineWhitespaceBeforeSemicolonsFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4202 [7.3] ErrorSuppressionFixer - support PHP 7.3 (kubawerlos)
* minor #4205 DX: PhpdocAlignFixer - refactor to use DocBlock (kubawerlos)
* minor #4206 DX: enable multiline_whitespace_before_semicolons (keradus)
* minor #4207 [7.3] RandomApiMigrationFixerTest - tests for 7.3 (SpacePossum)
* minor #4208 [7.3] NativeFunctionCasingFixerTest - tests for 7.3 (SpacePossum)
* minor #4209 [7.3] PhpUnitStrictFixerTest - tests for 7.3 (SpacePossum)
* minor #4210 [7.3] PhpUnitConstructFixer - add test for PHP 7.3 (kubawerlos)
* minor #4211 [7.3] PhpUnitDedicateAssertFixer - support PHP 7.3 (kubawerlos)
* minor #4214 [7.3] NoUnsetOnPropertyFixerTest - tests for 7.3 (SpacePossum)
* minor #4222 [7.3] PhpUnitExpectationFixer - support PHP 7.3 (kubawerlos)
* minor #4223 [7.3] PhpUnitMockFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4230 [7.3] IsNullFixer - fix trailing comma (guilliamxavier)
* minor #4232 DX: remove Utils::splitLines (kubawerlos)
* minor #4234 [7.3] Test that "LITERAL instanceof X" is valid (guilliamxavier)

Changelog for v2.13.2
---------------------

* bug #3968 SelfAccessorFixer - support FQCN (kubawerlos)
* bug #3974 Psr4Fixer - class with anonymous class (kubawerlos)
* bug #3987 Run HeaderCommentFixer after NoBlankLinesAfterPhpdocFixer (StanAngeloff)
* bug #4009 TypeAlternationTransformer - Fix pipes in function call with constants being classified incorrectly (ntzm, SpacePossum)
* bug #4022 NoUnsetOnPropertyFixer - refactor and bugfixes (kubawerlos)
* bug #4036 ExplicitStringVariableFixer - fixes for backticks and for 2 variables next to each other (kubawerlos, Slamdunk)
* bug #4038 CommentToPhpdocFixer - handling nested PHPDoc (kubawerlos)
* bug #4064 Ignore invalid mode strings, add option to remove the "b" flag. (SpacePossum)
* bug #4071 DX: do not insert Token when calling removeLeadingWhitespace/removeTrailingWhitespace from Tokens (kubawerlos)
* bug #4073 IsNullFixer - fix function detection (kubawerlos)
* bug #4074 FileFilterIterator - do not filter out files that need fixing (SpacePossum)
* bug #4076 EregToPregFixer - fix function detection (kubawerlos)
* bug #4084 MethodChainingIndentation - fix priority with Braces (dmvdbrugge)
* bug #4099 HeaderCommentFixer - throw exception on invalid header configuration (SpacePossum)
* bug #4100 PhpdocAddMissingParamAnnotationFixer - Handle variable number of arguments and pass by reference cases (SpacePossum)
* bug #4101 ReturnAssignmentFixer - do not touch invalid code (SpacePossum)
* bug #4104 Change transformers order, fixing untransformed T_USE (dmvdbrugge)
* bug #4107 Preg::split - fix for non-UTF8 subject (ostrolucky, kubawerlos)
* bug #4109 NoBlankLines*: fix removing lines consisting only of spaces (kubawerlos, keradus)
* bug #4114 VisibilityRequiredFixer - don't remove comments (kubawerlos)
* bug #4116 OrderedImportsFixer - fix sorting without any grouping (SpacePossum)
* bug #4119 PhpUnitNoExpectationAnnotationFixer - fix extracting content from annotation (kubawerlos)
* bug #4127 LowercaseConstantsFixer - Fix case with properties using constants as their name (srathbone)
* bug #4134 [7.3] SquareBraceTransformer - nested array destructuring not handled correctly (SpacePossum)
* bug #4153 PhpUnitFqcnAnnotationFixer - handle only PhpUnit classes (kubawerlos)
* bug #4169 DirConstantFixer - Fixes for PHP7.3 syntax (SpacePossum)
* bug #4181 MultilineCommentOpeningClosingFixer - fix handling empty comment (kubawerlos)
* bug #4186 Tokens - fix removal of leading/trailing whitespace with empty token in collection (kubawerlos)
* minor #3436 Add a handful of integration tests (BackEndTea)
* minor #3774 PhpUnitTestClassRequiresCoversFixer - Remove unneeded loop and use phpunit indicator class (BackEndTea, SpacePossum)
* minor #3778 DX: Throw an exception if FileReader::read fails (ntzm)
* minor #3916 New ruleset "@PhpCsFixer" (gharlan)
* minor #4007 Fixes cookbook for fixers (greeflas)
* minor #4031 Correct FixerOptionBuilder::getOption return type (ntzm)
* minor #4046 Token - Added fast isset() path to token->equals() (staabm)
* minor #4047 Token - inline $other->getPrototype() to speedup equals() (staabm, keradus)
* minor #4048 Tokens - inlined extractTokenKind() call on the hot path (staabm)
* minor #4069 DX: Add dev-tools directory to gitattributes as export-ignore (alexmanno)
* minor #4070 Docs: Add link to a VS Code extension in readme (jakebathman)
* minor #4077 DX: cleanup - NoAliasFunctionsFixer - use FunctionsAnalyzer (kubawerlos)
* minor #4088 Add Travis test with strict types (kubawerlos)
* minor #4091 Adjust misleading sentence in CONTRIBUTING.md (ostrolucky)
* minor #4092 UseTransformer - simplify/optimize (SpacePossum)
* minor #4095 DX: Use ::class (keradus)
* minor #4096 DX: fixing typo (kubawerlos)
* minor #4097 DX: namespace casing (kubawerlos)
* minor #4110 Enhancement: Update localheinz/composer-normalize (localheinz)
* minor #4115 Changes for upcoming Travis' infra migration (sergeyklay)
* minor #4122 DX: AppVeyor - Update Composer download link (SpacePossum)
* minor #4128 DX: cleanup - AbstractFunctionReferenceFixer - use FunctionsAnalyzer (SpacePossum, kubawerlos)
* minor #4129 Fix: Symfony 4.2 deprecations (kubawerlos)
* minor #4139 DX: Fix CircleCI (kubawerlos)
* minor #4142 [7.3] NoAliasFunctionsFixer - mbregex_encoding' => 'mb_regex_encoding (SpacePossum)
* minor #4143 PhpUnitTestCaseStaticMethodCallsFixer - Add PHPUnit 7.5 new assertions (Slamdunk)
* minor #4149 [7.3] ArgumentsAnalyzer - PHP7.3 support (SpacePossum)
* minor #4161 DX: CI - show packages installed via Composer (keradus)
* minor #4162 DX: Drop symfony/lts (keradus)
* minor #4166 DX: do not use AbstractFunctionReferenceFixer when no need to (kubawerlos)
* minor #4168 DX: FopenFlagsFixer - remove useless proxy method (SpacePossum)
* minor #4171 Fix CircleCI cache (kubawerlos)
* minor #4173 [7.3] PowToExponentiationFixer - add support for PHP7.3 (SpacePossum)
* minor #4175 Fixing typo (kubawerlos)
* minor #4177 CI: Check that tag is matching version of PHP CS Fixer during deployment (keradus)
* minor #4180 Fixing typo (kubawerlos)
* minor #4182 DX: update php-cs-fixer file style (kubawerlos)
* minor #4185 [7.3] ImplodeCallFixer - add tests for PHP7.3 (kubawerlos)
* minor #4187 [7.3] IsNullFixer - support PHP 7.3 (kubawerlos)
* minor #4188 DX: cleanup (keradus)
* minor #4189 Travis - add PHP 7.3 job (keradus)
* minor #4190 Travis CI - fix config (kubawerlos)
* minor #4192 [7.3] MagicMethodCasingFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4194 [7.3] NativeFunctionInvocationFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4195 [7.3] SetTypeToCastFixer - support PHP 7.3 (kubawerlos)
* minor #4196 Update website (keradus)
* minor #4197 [7.3] StrictParamFixer - support PHP 7.3 (kubawerlos)

Changelog for v2.13.1
---------------------

* bug #3977 NoSuperfluousPhpdocTagsFixer - Fix handling of description with variable (julienfalque)
* bug #4027 PhpdocAnnotationWithoutDotFixer - add failing cases (keradus)
* bug #4028 PhpdocNoEmptyReturnFixer - handle single line PHPDoc (kubawerlos)
* bug #4034 PhpUnitTestCaseIndicator - handle anonymous class (kubawerlos)
* bug #4037 NativeFunctionInvocationFixer - fix function detection (kubawerlos)
* feature #4019 PhpdocTypesFixer - allow for configuration (keradus)
* minor #3980 Clarifies allow-risky usage (josephzidell)
* minor #4016 Bump console component due to it's bug (keradus)
* minor #4023 Enhancement: Update localheinz/composer-normalize (localheinz)
* minor #4049 use parent::offset*() methods when moving items around in insertAt() (staabm)

Changelog for v2.13.0
---------------------

* feature #3739 Add MagicMethodCasingFixer (SpacePossum)
* feature #3812 Add FopenFlagOrderFixer & FopenFlagsFixer (SpacePossum)
* feature #3826 Add CombineNestedDirnameFixer (gharlan)
* feature #3833 BinaryOperatorSpacesFixer - Add "no space" fix strategy (SpacePossum)
* feature #3841 NoAliasFunctionsFixer - add opt in option for ext-mbstring aliases (SpacePossum)
* feature #3876 NativeConstantInvocationFixer - add the scope option (stof, keradus)
* feature #3886 Add PhpUnitMethodCasingFixer (Slamdunk)
* feature #3907 Add ImplodeCallFixer (kubawerlos)
* feature #3914 NoUnreachableDefaultArgumentValueFixer - remove `null` for nullable typehints (gharlan, keradus)
* minor #3813 PhpUnitDedicateAssertFixer - fix "sizeOf" same as "count". (SpacePossum)
* minor #3873 Add the native_function_invocation fixer in the Symfony:risky ruleset (stof)
* minor #3979 DX: enable php_unit_method_casing (keradus)

Changelog for v2.12.12
----------------------

* bug #4533 Revert PHP7.4 - Add "str_split" => "mb_str_split" mapping (keradus)
* minor #4264 DX: AutoReview - ensure Travis handle all needed PHP versions (keradus)
* minor #4524 MethodArgumentSpaceFixerTest - make explicit configuration to prevent fail on configuration change (keradus)

Changelog for v2.12.11
----------------------

* bug #4132 BlankLineAfterNamespaceFixer - do not remove indent, handle comments (kubawerlos)
* bug #4384 MethodArgumentSpaceFixer - fix for on_multiline:ensure_fully_multiline with trailing comma in function call (kubawerlos)
* bug #4404 FileLintingIterator - fix current value on end/invalid (SpacePossum)
* bug #4421 FunctionTypehintSpaceFixer - Ensure single space between type declaration and parameter (localheinz)
* bug #4436 MethodArgumentSpaceFixer - handle misplaced ) (keradus)
* bug #4439 NoLeadingImportSlashFixer - Add space if needed (SpacePossum)
* bug #4453 Fix preg_match error on 7.4snapshot (kubawerlos)
* bug #4461 IsNullFixer - fix null coalescing operator handling (linniksa)
* bug #4467 ToolInfo - fix access to reference without checking existence (black-silence)
* bug #4472 Fix non-static closure unbinding this on PHP 7.4 (kelunik)
* minor #3726 Use Box 3 to build the PHAR (theofidry, keradus)
* minor #4412 PHP 7.4 - Tests for support (SpacePossum)
* minor #4431 DX: test that default config is not passed in RuleSet (kubawerlos)
* minor #4433 DX: test to ensure @PHPUnitMigration rule sets are correctly defined (kubawerlos)
* minor #4445 DX: static call of markTestSkippedOrFail (kubawerlos)
* minor #4463 Add apostrophe to possessive "team's" (ChandlerSwift)
* minor #4471 ReadmeCommandTest - use CommandTester (kubawerlos)
* minor #4477 DX: control names of public methods in test's classes (kubawerlos)
* minor #4483 NewWithBracesFixer - Fix object operator and curly brace open cases (SpacePossum)
* minor #4484 fix typos in README (Sven Ludwig)
* minor #4494 DX: Fix shell script syntax in order to fix Travis builds (drupol)
* minor #4516 DX: Lock binary SCA tools versions (keradus)

Changelog for v2.12.10
----------------------

* bug #4418 PhpUnitNamespacedFixer - properly translate classes which do not follow translation pattern (ktomk)
* bug #4419 PhpUnitTestCaseStaticMethodCallsFixer - skip anonymous classes and lambda (SpacePossum)
* bug #4420 MethodArgumentSpaceFixer - PHP7.3 trailing commas in function calls (SpacePossum)
* minor #4345 Travis: PHP 7.4 isn't allowed to fail anymore (Slamdunk)
* minor #4403 LowercaseStaticReferenceFixer - Fix invalid PHP version in example (HypeMC)
* minor #4425 DX: assertions are static, adjust custom assertions (keradus)
* minor #4426 DX: handle deprecations of symfony/event-dispatcher:4.3 (keradus)
* minor #4427 DX: stop using reserved T_FN in code samples (keradus)

Changelog for v2.12.9
---------------------

* bug #4298 NoTrailingWhitespaceInCommentFixer - fix for non-Unix line separators (kubawerlos)
* bug #4303 FullyQualifiedStrictTypesFixer - Fix the short type detection when a question mark (nullable) is prefixing it. (drupol)
* bug #4313 SelfAccessorFixer - fix for part qualified class name (kubawerlos, SpacePossum)
* bug #4314 PhpUnitTestCaseStaticMethodCallsFixer - fix for having property with name as method to update (kubawerlos, SpacePossum)
* bug #4327 TokensAnalyzer - add concat operator to list of binary operators (SpacePossum)
* bug #4335 Cache - add indent and line ending to cache signature (dmvdbrugge)
* bug #4344 VoidReturnFixer - handle yield from (SpacePossum)
* bug #4346 BracesFixer - Do not pull close tag onto same line as a comment (SpacePossum)
* bug #4350 StrictParamFixer - Don't detect functions in use statements (bolmstedt)
* bug #4357 Fix short list syntax detection. (SpacePossum)
* bug #4365 Fix output escaping of diff for text format when line is not changed (SpacePossum)
* bug #4370 PhpUnitConstructFixer - Fix handle different casing (SpacePossum)
* bug #4379 ExplicitStringVariableFixer - add test case for variable as an array key (kubawerlos, Slamdunk)
* feature #4337 PhpUnitTestCaseStaticMethodCallsFixer - prepare for PHPUnit 8 (kubawerlos)
* minor #3799 DX: php_unit_test_case_static_method_calls - use default config (keradus)
* minor #4103 NoExtraBlankLinesFixer - fix candidate detection (SpacePossum)
* minor #4245 LineEndingFixer - BracesFixer - Priority (dmvdbrugge)
* minor #4325 Use lowercase mikey179/vfsStream in composer.json (lolli42)
* minor #4336 Collect coverage with PCOV (kubawerlos)
* minor #4338 Fix wording (kmvan, kubawerlos)
* minor #4339 Change BracesFixer to avoid indenting PHP inline braces (alecgeatches)
* minor #4340 Travis: build against 7.4snapshot instead of nightly (Slamdunk)
* minor #4351 code grooming (SpacePossum)
* minor #4353 Add more priority tests (SpacePossum)
* minor #4364 DX: MethodChainingIndentationFixer - remove unnecessary loop (Sijun Zhu)
* minor #4366 Unset the auxiliary variable $a (GrahamCampbell)
* minor #4368 Fixed TypeShortNameResolverTest::testResolver (GrahamCampbell)
* minor #4380 PHP7.4 - Add "str_split" => "mb_str_split" mapping. (SpacePossum)
* minor #4393 DX: add missing explicit return types (kubawerlos)

Changelog for v2.12.8
---------------------

* minor #4306 DX: Drop HHVM conflict on Composer level to help Composer with HHVM compatibility, we still prevent HHVM on runtime (keradus)

Changelog for v2.12.7
---------------------

* bug #4240 ModernizeTypesCastingFixer - fix for operators with higher precedence (kubawerlos)
* bug #4254 PhpUnitDedicateAssertFixer - fix for count with additional operations (kubawerlos)
* bug #4260 Psr0Fixer and Psr4Fixer  - fix for multiple classes in file with anonymous class (kubawerlos)
* bug #4262 FixCommand - fix help (keradus)
* bug #4276 MethodChainingIndentationFixer, ArrayIndentationFixer - Fix priority issue (dmvdbrugge)
* bug #4280 MethodArgumentSpaceFixer - Fix method argument alignment (Billz95)
* bug #4286 IncrementStyleFixer - fix for static statement (kubawerlos)
* bug #4291 ArrayIndentationFixer - Fix indentation after trailing spaces (julienfalque, keradus)
* bug #4292 NoSuperfluousPhpdocTagsFixer - Make null only type not considered superfluous (julienfalque)
* minor #4204 DX: Tokens - do not unregister/register found tokens when collection is not changing (kubawerlos)
* minor #4235 DX: more specific @param types (kubawerlos)
* minor #4263 DX: AppVeyor - bump PHP version (keradus)
* minor #4293 Add official support for PHP 7.3 (keradus)
* minor #4295 DX: MethodArgumentSpaceFixerTest - fix edge case for handling different line ending when only expected code is provided (keradus)
* minor #4296 DX: cleanup testing with fixer config (keradus)
* minor #4299 NativeFunctionInvocationFixer - add array_key_exists (deguif, keradus)

Changelog for v2.12.6
---------------------

* bug #4216 Psr4Fixer - fix for multiple classy elements in file (keradus, kubawerlos)
* bug #4217 Psr0Fixer - class with anonymous class (kubawerlos)
* bug #4219  NativeFunctionCasingFixer - handle T_RETURN_REF  (kubawerlos)
* bug #4224 FunctionToConstantFixer - handle T_RETURN_REF (SpacePossum)
* bug #4229 IsNullFixer - fix parenthesis not closed (guilliamxavier)
* minor #4198 [7.3] PowToExponentiationFixer - adding to PHP7.3 integration test (kubawerlos)
* minor #4199 [7.3] MethodChainingIndentationFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4200 [7.3] ModernizeTypesCastingFixer - support PHP 7.3 (kubawerlos)
* minor #4201 [7.3] MultilineWhitespaceBeforeSemicolonsFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4202 [7.3] ErrorSuppressionFixer - support PHP 7.3 (kubawerlos)
* minor #4205 DX: PhpdocAlignFixer - refactor to use DocBlock (kubawerlos)
* minor #4206 DX: enable multiline_whitespace_before_semicolons (keradus)
* minor #4207 [7.3] RandomApiMigrationFixerTest - tests for 7.3 (SpacePossum)
* minor #4208 [7.3] NativeFunctionCasingFixerTest - tests for 7.3 (SpacePossum)
* minor #4209 [7.3] PhpUnitStrictFixerTest - tests for 7.3 (SpacePossum)
* minor #4210 [7.3] PhpUnitConstructFixer - add test for PHP 7.3 (kubawerlos)
* minor #4211 [7.3] PhpUnitDedicateAssertFixer - support PHP 7.3 (kubawerlos)
* minor #4214 [7.3] NoUnsetOnPropertyFixerTest - tests for 7.3 (SpacePossum)
* minor #4222 [7.3] PhpUnitExpectationFixer - support PHP 7.3 (kubawerlos)
* minor #4223 [7.3] PhpUnitMockFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4230 [7.3] IsNullFixer - fix trailing comma (guilliamxavier)
* minor #4232 DX: remove Utils::splitLines (kubawerlos)
* minor #4234 [7.3] Test that "LITERAL instanceof X" is valid (guilliamxavier)

Changelog for v2.12.5
---------------------

* bug #3968 SelfAccessorFixer - support FQCN (kubawerlos)
* bug #3974 Psr4Fixer - class with anonymous class (kubawerlos)
* bug #3987 Run HeaderCommentFixer after NoBlankLinesAfterPhpdocFixer (StanAngeloff)
* bug #4009 TypeAlternationTransformer - Fix pipes in function call with constants being classified incorrectly (ntzm, SpacePossum)
* bug #4022 NoUnsetOnPropertyFixer - refactor and bugfixes (kubawerlos)
* bug #4036 ExplicitStringVariableFixer - fixes for backticks and for 2 variables next to each other (kubawerlos, Slamdunk)
* bug #4038 CommentToPhpdocFixer - handling nested PHPDoc (kubawerlos)
* bug #4071 DX: do not insert Token when calling removeLeadingWhitespace/removeTrailingWhitespace from Tokens (kubawerlos)
* bug #4073 IsNullFixer - fix function detection (kubawerlos)
* bug #4074 FileFilterIterator - do not filter out files that need fixing (SpacePossum)
* bug #4076 EregToPregFixer - fix function detection (kubawerlos)
* bug #4084 MethodChainingIndentation - fix priority with Braces (dmvdbrugge)
* bug #4099 HeaderCommentFixer - throw exception on invalid header configuration (SpacePossum)
* bug #4100 PhpdocAddMissingParamAnnotationFixer - Handle variable number of arguments and pass by reference cases (SpacePossum)
* bug #4101 ReturnAssignmentFixer - do not touch invalid code (SpacePossum)
* bug #4104 Change transformers order, fixing untransformed T_USE (dmvdbrugge)
* bug #4107 Preg::split - fix for non-UTF8 subject (ostrolucky, kubawerlos)
* bug #4109 NoBlankLines*: fix removing lines consisting only of spaces (kubawerlos, keradus)
* bug #4114 VisibilityRequiredFixer - don't remove comments (kubawerlos)
* bug #4116 OrderedImportsFixer - fix sorting without any grouping (SpacePossum)
* bug #4119 PhpUnitNoExpectationAnnotationFixer - fix extracting content from annotation (kubawerlos)
* bug #4127 LowercaseConstantsFixer - Fix case with properties using constants as their name (srathbone)
* bug #4134 [7.3] SquareBraceTransformer - nested array destructuring not handled correctly (SpacePossum)
* bug #4153 PhpUnitFqcnAnnotationFixer - handle only PhpUnit classes (kubawerlos)
* bug #4169 DirConstantFixer - Fixes for PHP7.3 syntax (SpacePossum)
* bug #4181 MultilineCommentOpeningClosingFixer - fix handling empty comment (kubawerlos)
* bug #4186 Tokens - fix removal of leading/trailing whitespace with empty token in collection (kubawerlos)
* minor #3436 Add a handful of integration tests (BackEndTea)
* minor #3774 PhpUnitTestClassRequiresCoversFixer - Remove unneeded loop and use phpunit indicator class (BackEndTea, SpacePossum)
* minor #3778 DX: Throw an exception if FileReader::read fails (ntzm)
* minor #3916 New ruleset "@PhpCsFixer" (gharlan)
* minor #4007 Fixes cookbook for fixers (greeflas)
* minor #4031 Correct FixerOptionBuilder::getOption return type (ntzm)
* minor #4046 Token - Added fast isset() path to token->equals() (staabm)
* minor #4047 Token - inline $other->getPrototype() to speedup equals() (staabm, keradus)
* minor #4048 Tokens - inlined extractTokenKind() call on the hot path (staabm)
* minor #4069 DX: Add dev-tools directory to gitattributes as export-ignore (alexmanno)
* minor #4070 Docs: Add link to a VS Code extension in readme (jakebathman)
* minor #4077 DX: cleanup - NoAliasFunctionsFixer - use FunctionsAnalyzer (kubawerlos)
* minor #4088 Add Travis test with strict types (kubawerlos)
* minor #4091 Adjust misleading sentence in CONTRIBUTING.md (ostrolucky)
* minor #4092 UseTransformer - simplify/optimize (SpacePossum)
* minor #4095 DX: Use ::class (keradus)
* minor #4097 DX: namespace casing (kubawerlos)
* minor #4110 Enhancement: Update localheinz/composer-normalize (localheinz)
* minor #4115 Changes for upcoming Travis' infra migration (sergeyklay)
* minor #4122 DX: AppVeyor - Update Composer download link (SpacePossum)
* minor #4128 DX: cleanup - AbstractFunctionReferenceFixer - use FunctionsAnalyzer (SpacePossum, kubawerlos)
* minor #4129 Fix: Symfony 4.2 deprecations (kubawerlos)
* minor #4139 DX: Fix CircleCI (kubawerlos)
* minor #4143 PhpUnitTestCaseStaticMethodCallsFixer - Add PHPUnit 7.5 new assertions (Slamdunk)
* minor #4149 [7.3] ArgumentsAnalyzer - PHP7.3 support (SpacePossum)
* minor #4161 DX: CI - show packages installed via Composer (keradus)
* minor #4162 DX: Drop symfony/lts (keradus)
* minor #4166 DX: do not use AbstractFunctionReferenceFixer when no need to (kubawerlos)
* minor #4171 Fix CircleCI cache (kubawerlos)
* minor #4173 [7.3] PowToExponentiationFixer - add support for PHP7.3 (SpacePossum)
* minor #4175 Fixing typo (kubawerlos)
* minor #4177 CI: Check that tag is matching version of PHP CS Fixer during deployment (keradus)
* minor #4182 DX: update php-cs-fixer file style (kubawerlos)
* minor #4187 [7.3] IsNullFixer - support PHP 7.3 (kubawerlos)
* minor #4188 DX: cleanup (keradus)
* minor #4189 Travis - add PHP 7.3 job (keradus)
* minor #4190 Travis CI - fix config (kubawerlos)
* minor #4194 [7.3] NativeFunctionInvocationFixer - add tests for PHP 7.3 (kubawerlos)
* minor #4195 [7.3] SetTypeToCastFixer - support PHP 7.3 (kubawerlos)
* minor #4196 Update website (keradus)
* minor #4197 [7.3] StrictParamFixer - support PHP 7.3 (kubawerlos)

Changelog for v2.12.4
---------------------

* bug #3977 NoSuperfluousPhpdocTagsFixer - Fix handling of description with variable (julienfalque)
* bug #4027 PhpdocAnnotationWithoutDotFixer - add failing cases (keradus)
* bug #4028 PhpdocNoEmptyReturnFixer - handle single line PHPDoc (kubawerlos)
* bug #4034 PhpUnitTestCaseIndicator - handle anonymous class (kubawerlos)
* bug #4037 NativeFunctionInvocationFixer - fix function detection (kubawerlos)
* feature #4019 PhpdocTypesFixer - allow for configuration (keradus)
* minor #3980 Clarifies allow-risky usage (josephzidell)
* minor #4016 Bump console component due to it's bug (keradus)
* minor #4023 Enhancement: Update localheinz/composer-normalize (localheinz)
* minor #4049 use parent::offset*() methods when moving items around in insertAt() (staabm)

Changelog for v2.12.3
---------------------

* bug #3867 PhpdocAnnotationWithoutDotFixer - Handle trailing whitespaces (kubawerlos)
* bug #3884 NoSuperfluousPhpdocTagsFixer - handle null in every position (dmvdbrugge, julienfalque)
* bug #3885 AlignMultilineCommentFixer - ArrayIndentationFixer - Priority (dmvdbrugge)
* bug #3887 ArrayIndentFixer - Don't indent empty lines (dmvdbrugge)
* bug #3888 NoExtraBlankLinesFixer - remove blank lines after open tag (kubawerlos)
* bug #3890 StrictParamFixer - make it case-insensitive (kubawerlos)
* bug #3895 FunctionsAnalyzer - false positive for constant and function definition (kubawerlos)
* bug #3908 StrictParamFixer - fix edge case (kubawerlos)
* bug #3910 FunctionsAnalyzer - fix isGlobalFunctionCall (gharlan)
* bug #3912 FullyQualifiedStrictTypesFixer - NoSuperfluousPhpdocTagsFixer - adjust priority (dmvdbrugge)
* bug #3913 TokensAnalyzer - fix isConstantInvocation (gharlan, keradus)
* bug #3921 TypeAnalysis - Fix iterable not being detected as a reserved type (ntzm)
* bug #3924 FullyQualifiedStrictTypesFixer - space bug (dmvdbrugge)
* bug #3937 LowercaseStaticReferenceFixer - Fix "Parent" word in namespace (kubawerlos)
* bug #3944 ExplicitStringVariableFixer - fix array handling (gharlan)
* bug #3951 NoSuperfluousPhpdocTagsFixer - do not call strtolower with null (SpacePossum)
* bug #3954 NoSuperfluousPhpdocTagsFixer - Index invalid or out of range (kubawerlos)
* bug #3957 NoTrailingWhitespaceFixer - trim space after opening tag (kubawerlos)
* minor #3798 DX: enable native_function_invocation (keradus)
* minor #3882 PhpdocAnnotationWithoutDotFixer - Handle empty line in comment (kubawerlos)
* minor #3889 DX: Cleanup - remove unused variables (kubawerlos, SpacePossum)
* minor #3891 PhpdocNoEmptyReturnFixer - account for null[] (dmvdbrugge)
* minor #3892 PhpdocNoEmptyReturnFixer - fix docs (keradus)
* minor #3897 DX: FunctionsAnalyzer - simplifying return expression (kubawerlos)
* minor #3903 DX: cleanup - remove special treatment for PHP <5.6 (kubawerlos)
* minor #3905 DX: Upgrade composer-require-checker to stable version (keradus)
* minor #3919 Simplify single uses of Token::isGivenKind (ntzm)
* minor #3920 Docs: Fix typo (ntzm)
* minor #3940 DX: fix phpdoc parameter type (malukenho)
* minor #3948 DX: cleanup - remove redundant @param annotations (kubawerlos)
* minor #3950 Circle CI v2 yml (siad007)
* minor #3952 DX: AbstractFixerTestCase - drop testing method already provided by trait (keradus)
* minor #3973 Bump xdebug-handler (keradus)

Changelog for v2.12.2
---------------------

* bug #3823 NativeConstantInvocationFixer - better constant detection (gharlan, SpacePossum, keradus)
* bug #3832 "yield from" as keyword (SpacePossum)
* bug #3835 Fix priority between PHPDoc return type fixers (julienfalque, keradus)
* bug #3839 MethodArgumentSpaceFixer - add empty line incorrectly (SpacePossum)
* bug #3866 SpaceAfterSemicolonFixer - loop over all tokens (SpacePossum)
* minor #3817 Update integrations tests (SpacePossum)
* minor #3829 Fix typos in changelog (mnabialek)
* minor #3848 Add install/update instructions for PHIVE to the README (SpacePossum)
* minor #3877 NamespacesAnalyzer - Optimize performance (stof)
* minor #3878 NativeFunctionInvocationFixer - use the NamespacesAnalyzer to remove duplicated code (stof)

Changelog for v2.12.1
---------------------

* bug #3808 LowercaseStaticReferenceFixer - Fix constants handling (kubawerlos, keradus)
* bug #3815 NoSuperfluousPhpdocTagsFixer - support array/callable type hints (gharlan)
* minor #3824 DX: Support PHPUnit 7.2 (keradus)
* minor #3825 UX: Provide full diff for code samples (keradus)

Changelog for v2.12.0
---------------------

* feature #2577 Add LogicalOperatorsFixer (hkdobrev, keradus)
* feature #3060 Add ErrorSuppressionFixer (kubawerlos)
* feature #3127 Add NativeConstantInvocationFixer (Slamdunk, keradus)
* feature #3223 NativeFunctionInvocationFixer - add namespace scope and include sets (SpacePossum)
* feature #3453 PhpdocAlignFixer - add align option (robert.ahmerov)
* feature #3476 Add PhpUnitTestCaseStaticMethodCallsFixer (Slamdunk, keradus)
* feature #3524 MethodArgumentSpaceFixer - Add ensure_single_line option (julienfalque, keradus)
* feature #3534 MultilineWhitespaceBeforeSemicolonsFixer - support static calls (ntzm)
* feature #3585 Add ReturnAssignmentFixer (SpacePossum, keradus)
* feature #3640 Add PhpdocToReturnTypeFixer (Slamdunk, keradus)
* feature #3691 Add PhpdocTrimAfterDescriptionFixer (nobuf, keradus)
* feature #3698 YodaStyleFixer - Add always_move_variable option (julienfalque, SpacePossum)
* feature #3709 Add SetTypeToCastFixer (SpacePossum)
* feature #3724 BlankLineBeforeStatementFixer - Add case and default as options (dmvdbrugge)
* feature #3734 Add NoSuperfluousPhpdocTagsFixer (julienfalque)
* feature #3735 Add LowercaseStaticReferenceFixer (kubawerlos, SpacePossum)
* feature #3737 Add NoUnsetOnPropertyFixer (BackEndTea, SpacePossum)
* feature #3745 Add PhpUnitInternalClassFixer (BackEndTea, SpacePossum, keradus)
* feature #3766 Add NoBinaryStringFixer (ntzm, SpacePossum, keradus)
* feature #3780 ShortScalarCastFixer - Change binary cast to string cast as well (ntzm)
* feature #3785 PhpUnitDedicateAssertFixer - fix to assertCount too (SpacePossum)
* feature #3802 Convert PhpdocTrimAfterDescriptionFixer into PhpdocTrimConsecutiveBlankLineSeparationFixer (keradus)
* minor #3738 ReturnAssignmentFixer description update (kubawerlos)
* minor #3761 Application: when run with FUTURE_MODE, error_reporting(-1) is done in entry file instead (keradus)
* minor #3772 DX: use PhpUnitTestCaseIndicator->isPhpUnitClass to discover PHPUnit classes (keradus)
* minor #3783 CI: Split COLLECT_COVERAGE job (keradus)
* minor #3789 DX: ProjectCodeTest.testThatDataProvidersAreCorrectlyNamed - performance optimization (keradus)
* minor #3791 DX: Fix collecting code coverage (keradus)
* minor #3792 DX: Upgrade DX deps (keradus)
* minor #3797 DX: ProjectCodeTest - shall not depends on xdebug/phpdbg anymore (keradus, SpacePossum)
* minor #3800 Symfony:risky ruleset: include set_type_to_cast rule (keradus)
* minor #3801 NativeFunctionInvocationFixer - fix buggy config validation (keradus, SpacePossum)

Changelog for v2.11.2
---------------------

* bug #3233 PhpdocAlignFixer - Fix linebreak inconsistency (SpacePossum, keradus)
* bug #3445 Rewrite NoUnusedImportsFixer (kubawerlos, julienfalque)
* bug #3528 MethodChainingIndentationFixer - nested params bugfix (Slamdunk)
* bug #3547 MultilineWhitespaceBeforeSemicolonsFixer - chained call for a return fix (egircys, keradus)
* bug #3597 DeclareStrictTypesFixer - fix bug of removing line (kubawerlos, keradus)
* bug #3605 DoctrineAnnotationIndentationFixer - Fix indentation with mixed lines (julienfalque)
* bug #3606 PhpdocToCommentFixer - allow multiple ( (SpacePossum)
* bug #3614 Refactor PhpdocToCommentFixer - extract checking to CommentsAnalyzer (kubawerlos)
* bug #3668 Rewrite NoUnusedImportsFixer (kubawerlos, julienfalque)
* bug #3670 PhpdocTypesOrderFixer - Fix ordering of nested generics (julienfalque)
* bug #3671 ArrayIndentationFixer - Fix indentation in HTML (julienfalque)
* bug #3673 PhpdocScalarFixer - Add "types" option (julienfalque, keradus)
* bug #3674 YodaStyleFixer - Fix variable detection for multidimensional arrays (julienfalque, SpacePossum)
* bug #3684 PhpUnitStrictFixer - Do not fix if not correct # of arguments are used (SpacePossum)
* bug #3708 EscapeImplicitBackslashesFixer - Fix escaping multiple backslashes (julienfalque)
* bug #3715 SingleImportPerStatementFixer - Fix handling whitespace before opening brace (julienfalque)
* bug #3731 PhpdocIndentFixer -  crash fix (SpacePossum)
* bug #3755 YodaStyleFixer - handle space between var name and index (SpacePossum)
* bug #3765 Fix binary-prefixed double-quoted strings to single quotes (ntzm)
* bug #3770 Handle binary flags in heredoc_to_nowdoc (ntzm)
* bug #3776 ExplicitStringVariableFixer - handle binary strings (ntzm)
* bug #3777 EscapeImplicitBackslashesFixer - handle binary strings (ntzm)
* bug #3790 ProcessLinter - don't execute external process without timeout! It can freeze! (keradus)
* minor #3188 AppVeyor - add PHP 7.x (keradus, julienfalque)
* minor #3451 Update findPHPUnit functions (BackEndTea, SpacePossum, keradus)
* minor #3548 Make shell scripts POSIX-compatible (EvgenyOrekhov, keradus)
* minor #3568 New Autoreview: Correct option casing (ntzm)
* minor #3578 Add interface for deprecated options (julienfalque, keradus)
* minor #3590 Use XdebugHandler to avoid perormance penalty (AJenbo, keradus)
* minor #3607 PhpdocVarWithoutNameFixer - update sample with @ type (SpacePossum)
* minor #3617 Tests stability patches (Tom Klingenberg, keradus)
* minor #3622 Docs: Update descriptions (localheinz)
* minor #3627 Fix tests execution under phpdbg (keradus)
* minor #3629 ProjectFixerConfigurationTest - test rules are sorted (SpacePossum)
* minor #3639 DX: use benefits of symfony/force-lowest (keradus)
* minor #3641 Update check_trailing_spaces script with upstream (keradus)
* minor #3646 Extract SameStringsConstraint and XmlMatchesXsdConstraint (keradus)
* minor #3647 DX: Add CachingLinter for tests (keradus)
* minor #3649 Update check_trailing_spaces script with upstream (keradus)
* minor #3652 CiIntegrationTest - run tests with POSIX sh, not Bash (keradus)
* minor #3656 DX: Clean ups (SpacePossum)
* minor #3657 update phpunitgoodpractices/traits (SpacePossum, keradus)
* minor #3658 DX: Clean ups (SpacePossum)
* minor #3660 Fix  do not rely on order of fixing in CiIntegrationTest (kubawerlos)
* minor #3661  Fix: covers annotation for NoAlternativeSyntaxFixerTest (kubawerlos)
* minor #3662 DX: Add Smoke/InstallViaComposerTest (keradus)
* minor #3663 DX: During deployment, run all smoke tests and don't allow to skip phar-related ones (keradus)
* minor #3665 CircleCI fix (kubawerlos)
* minor #3666 Use "set -eu" in shell scripts (EvgenyOrekhov)
* minor #3669 Document possible values for subset options (julienfalque, keradus)
* minor #3672 Remove SameStringsConstraint and XmlMatchesXsdConstraint (keradus)
* minor #3676 RunnerTest - workaround for failing Symfony v2.8.37 (kubawerlos)
* minor #3680 DX: Tokens - removeLeadingWhitespace and removeTrailingWhitespace must act in same way (SpacePossum)
* minor #3686 README.rst - Format all code-like strings in fixer descriptions (ntzm, keradus)
* minor #3692 DX: Optimize tests (julienfalque)
* minor #3700 README.rst - Format all code-like strings in fixer description (ntzm)
* minor #3701 Use correct casing for "PHPDoc" (ntzm)
* minor #3703 DX: InstallViaComposerTest - groom naming (keradus)
* minor #3704 DX: Tokens - fix naming (keradus)
* minor #3706 Update homebrew installation instructions (ntzm)
* minor #3713 Use HTTPS whenever possible (fabpot)
* minor #3723 Extend tests coverage (ntzm)
* minor #3733 Disable Composer optimized autoloader by default (julienfalque)
* minor #3748 PhpUnitStrictFixer - extend risky note (jnvsor)
* minor #3749 Make sure PHPUnit is cased correctly in fixers descriptions (kubawerlos)
* minor #3768 Improve deprecation messages (julienfalque, SpacePossum)
* minor #3773 AbstractFixerWithAliasedOptionsTestCase - don't export (keradus)
* minor #3775 Add tests for binary strings in string_line_ending (ntzm)
* minor #3779 Misc fixes (ntzm, keradus)
* minor #3796 DX: StdinTest - do not assume name of folder, into which project was cloned (keradus)
* minor #3803 NoEmptyPhpdocFixer/PhpdocAddMissingParamAnnotationFixer - missing priority test (SpacePossum, keradus)
* minor #3804 Cleanup: remove useless constructor comment (kubawerlos)
* minor #3805 Cleanup: add missing @param type (kubawerlos, keradus)

Changelog for v2.11.1
---------------------

* bug #3626 ArrayIndentationFixer: priority bug with BinaryOperatorSpacesFixer and MethodChainingIndentationFixer (Slamdunk)
* bug #3632 DateTimeImmutableFixer bug with adding tokens while iterating over them (kubawerlos)
* minor #3478 PhpUnitDedicateAssertFixer: handle static calls (Slamdunk)
* minor #3618 DateTimeImmutableFixer - grooming (keradus)

Changelog for v2.11.0
---------------------

* feature #3135 Add ArrayIndentationFixer (julienfalque)
* feature #3235 Implement StandardizeIncrementFixer (ntzm, SpacePossum)
* feature #3260 Add DateTimeImmutableFixer (kubawerlos)
* feature #3276 Transform Fully Qualified parameters and return types to short version (veewee, keradus)
* feature #3299 SingleQuoteFixer - fix single quote char (Slamdunk)
* feature #3340 Verbose LintingException after fixing (Slamdunk)
* feature #3423 FunctionToConstantFixer - add fix "get_called_class" option (SpacePossum)
* feature #3434 Add PhpUnitSetUpTearDownVisibilityFixer (BackEndTea, SpacePossum)
* feature #3442 Add CommentToPhpdocFixer (kubawerlos, keradus)
* feature #3448 OrderedClassElementsFixer - added sortAlgorithm option (meridius)
* feature #3454 Add StringLineEndingFixer (iluuu1994, SpacePossum, keradus, julienfalque)
* feature #3477 PhpUnitStrictFixer: handle static calls (Slamdunk)
* feature #3479 PhpUnitConstructFixer: handle static calls (Slamdunk)
* feature #3507 Add PhpUnitOrderedCoversFixer (Slamdunk)
* feature #3545 Add the 'none' sort algorithm to OrderedImportsFixer (EvgenyOrekhov)
* feature #3588 Add NoAlternativeSyntaxFixer (eddmash, keradus)
* minor #3414 DescribeCommand: add fixer class when verbose (Slamdunk)
* minor #3432 ConfigurationDefinitionFixerInterface - fix deprecation notice (keradus)
* minor #3527 Deprecate last param of Tokens::findBlockEnd (ntzm, keradus)
* minor #3539 Update UnifiedDiffOutputBuilder from gecko-packages/gecko-diff-output-builder usage after it was incorporated into sebastian/diff (keradus)
* minor #3549 DescribeCommand - use our Differ wrapper class, not external one directly (keradus)
* minor #3592 Support PHPUnit 7 (keradus)
* minor #3619 Travis - extend additional files list (keradus)

Changelog for v2.10.5
---------------------

* bug #3344 Fix method chaining indentation in HTML (julienfalque)
* bug #3594 ElseifFixer - Bug with alternative syntax (kubawerlos)
* bug #3600 StrictParamFixer - Fix issue when functions are imported (ntzm, keradus)
* minor #3589 FixerFactoryTest - add missing test (SpacePossum, keradus)
* minor #3610 make phar extension optional (Tom Klingenberg, keradus)
* minor #3612 Travis - allow for hhvm failures (keradus)
* minor #3615 Detect fabbot.io (julienfalque, keradus)
* minor #3616 FixerFactoryTest - Don't rely on autovivification (keradus)
* minor #3621 FixerFactoryTest - apply CS (keradus)

Changelog for v2.10.4
---------------------

* bug #3446 Add PregWrapper (kubawerlos)
* bug #3464 IncludeFixer - fix incorrect order of fixing (kubawerlos, SpacePossum)
* bug #3496 Bug in Tokens::removeLeadingWhitespace (kubawerlos, SpacePossum, keradus)
* bug #3557 AbstractDoctrineAnnotationFixer: edge case bugfix (Slamdunk)
* bug #3574 GeneralPhpdocAnnotationRemoveFixer - remove PHPDoc if no content is left (SpacePossum)
* minor #3563 DX add missing covers annotations (keradus)
* minor #3564 Use ::class keyword when possible (keradus)
* minor #3565 Use EventDispatcherInterface instead of EventDispatcher when possible (keradus)
* minor #3566 Update PHPUnitGoodPractices\Traits (keradus)
* minor #3572 DX: allow for more phpunit-speedtrap versions to support more PHPUnit versions (keradus)
* minor #3576 Fix Doctrine Annotation test cases merging (julienfalque)
* minor #3577 DoctrineAnnotationArrayAssignmentFixer - Add test case (julienfalque)

Changelog for v2.10.3
---------------------

* bug #3504 NoBlankLinesAfterPhpdocFixer - allow blank line before declare statement (julienfalque)
* bug #3522 Remove LOCK_EX (SpacePossum)
* bug #3560 SelfAccessorFixer is risky (Slamdunk)
* minor #3435 Add tests for general_phpdoc_annotation_remove (BackEndTea)
* minor #3484 Create Tokens::findBlockStart (ntzm)
* minor #3512 Add missing array typehints (ntzm)
* minor #3513 Making AppVeyor happy (kubawerlos)
* minor #3516 Use `null|type` instead of `?type` in PHPDocs (ntzm)
* minor #3518 FixerFactoryTest - Test each priority test file is listed as test (SpacePossum)
* minor #3519 Fix typo (SpacePossum)
* minor #3520 Fix typos: ran vs. run (SpacePossum)
* minor #3521 Use HTTPS (carusogabriel)
* minor #3526 Remove gecko dependency (SpacePossum, keradus, julienfalque)
* minor #3531 Backport PHPMD to LTS version to ease maintainability (keradus)
* minor #3532 Implement Tokens::findOppositeBlockEdge (ntzm)
* minor #3533 DX: SCA - drop src/Resources exclusion (keradus)
* minor #3538 Don't use third parameter of Tokens::findBlockStart (ntzm)
* minor #3542 Enhancement: Run composer-normalize on Travis CI (localheinz, keradus)
* minor #3550 AutoReview\FixerFactoryTest - fix missing priority test, mark not fully valid test as incomplete (keradus)
* minor #3555 DX: composer.json - drop branch-alias, branch is already following the version (keradus)
* minor #3556 DX: Add AutoReview/ComposerTest (keradus)
* minor #3559 Don't expose new files under Test namespace (keradus)
* minor #3561 PHPUnit5 - add in place missing compat layer for PHPUnit6 (keradus)

Changelog for v2.10.2
---------------------

* bug #3502 Fix missing file in export (keradus)

Changelog for v2.10.1
---------------------

* bug #3265 YodaFixer - fix problems of block statements followed by ternary statements (weareoutman, keradus, SpacePossum)
* bug #3367 NoUnusedImportsFixer - fix comment handling (SpacePossum, keradus)
* bug #3438 PhpUnitTestAnnotationFixer: Do not prepend with test if method is test() (localheinz, SpacePossum)
* bug #3455 NoEmptyCommentFixer - comment block detection for line ending different than LF (kubawerlos, SpacePossum)
* bug #3458 SilencedDeprecationErrorFixer - fix edge cases (kubawerlos)
* bug #3466 no_whitespace_in_blank_line and no_blank_lines_after_phpdoc fixers bug (kubawerlos, keradus)
* bug #3472  YodaStyleFixer - do not un-Yoda if right side is assignment (SpacePossum, keradus)
* bug #3492 PhpdocScalarFixer - Add callback pseudo-type to callable type (carusogabriel)
* minor #3354 Added missing types to the PhpdocTypesFixer (GrahamCampbell)
* minor #3406 Fix for escaping in README (kubawerlos)
* minor #3430 Fix integration test (SpacePossum)
* minor #3431 Add missing tests (SpacePossum)
* minor #3440 Add a handful of integration tests (BackEndTea)
* minor #3443 ConfigurableFixerInterface - not deprecated but TODO (SpacePossum)
* minor #3444 IntegrationTest - ensure tests in priority dir are priority tests indeed (keradus)
* minor #3494 Add missing PHPDoc param type (ntzm)
* minor #3495 Swap @var type and element (ntzm)
* minor #3498 NoUnusedImportsFixer - fix deprecation (keradus)

Changelog for v2.10.0
---------------------

* feature #3290 Add PhpdocOpeningClosingFixer (Slamdunk, keradus)
* feature #3327 Add MultilineWhitespaceBeforeSemicolonsFixer (egircys, keradus)
* feature #3351 PhuUnit: migrate getMock to createPartialMock when arguments count is 2 (Slamdunk)
* feature #3362 Add BacktickToShellExecFixer (Slamdunk)
* minor #3285 PHPUnit - use protective traits (keradus)
* minor #3329 ConfigurationResolver - detect deprecated fixers (keradus, SpacePossum)
* minor #3343 Tokens - improve block end lookup (keradus)
* minor #3360 Adjust Symfony ruleset (keradus)
* minor #3361 no_extra_consecutive_blank_lines - rename to no_extra_blank_lines (with BC layer) (keradus)
* minor #3363 progress-type - name main option value 'dots' (keradus)
* minor #3404 Deprecate "use_yoda_style" in IsNullFixer (kubawerlos, keradus)
* minor #3418 ConfigurableFixerInterface, ConfigurationDefinitionFixerInterface - update deprecations (keradus)
* minor #3419 Dont use deprecated fixer in itest (keradus)

Changelog for v2.9.3
--------------------

* bug #3502 Fix missing file in export (keradus)

Changelog for v2.9.2
--------------------

* bug #3265 YodaFixer - fix problems of block statements followed by ternary statements (weareoutman, keradus, SpacePossum)
* bug #3367 NoUnusedImportsFixer - fix comment handling (SpacePossum, keradus)
* bug #3438 PhpUnitTestAnnotationFixer: Do not prepend with test if method is test() (localheinz, SpacePossum)
* bug #3455 NoEmptyCommentFixer - comment block detection for line ending different than LF (kubawerlos, SpacePossum)
* bug #3458 SilencedDeprecationErrorFixer - fix edge cases (kubawerlos)
* bug #3466 no_whitespace_in_blank_line and no_blank_lines_after_phpdoc fixers bug (kubawerlos, keradus)
* bug #3472  YodaStyleFixer - do not un-Yoda if right side is assignment (SpacePossum, keradus)
* minor #3354 Added missing types to the PhpdocTypesFixer (GrahamCampbell)
* minor #3406 Fix for escaping in README (kubawerlos)
* minor #3430 Fix integration test (SpacePossum)
* minor #3431 Add missing tests (SpacePossum)
* minor #3440 Add a handful of integration tests (BackEndTea)
* minor #3444 IntegrationTest - ensure tests in priority dir are priority tests indeed (keradus)
* minor #3494 Add missing PHPDoc param type (ntzm)
* minor #3495 Swap @var type and element (ntzm)
* minor #3498 NoUnusedImportsFixer - fix deprecation (keradus)

Changelog for v2.9.1
--------------------

* bug #3298 DiffConsoleFormatter - fix output escaping. (SpacePossum)
* bug #3312 PhpUnitTestAnnotationFixer: Only remove prefix if it really is a prefix (localheinz)
* bug #3318 SingleLineCommentStyleFixer - fix closing tag inside comment causes an error (kubawerlos)
* bug #3334 ExplicitStringVariableFixer: handle parsed array and object (Slamdunk)
* bug #3337 BracesFixer: nowdoc bug on template files (Slamdunk)
* bug #3349 Fix stdin handling and add tests for it (keradus)
* bug #3350 PhpUnitNoExpectationAnnotationFixer - fix handling of multiline expectedExceptionMessage annotation (Slamdunk)
* bug #3352 FunctionToConstantFixer - bugfix for get_class with leading backslash (kubawerlos)
* bug #3359 BracesFixer - handle comment for content outside of given block (keradus)
* bug #3371 IsNullFixer must be run before YodaStyleFixer (kubawerlos)
* bug #3373 PhpdocAlignFixer - Fix removing of everything after @ when there is a space after the @ (ntzm)
* bug #3415 FileFilterIterator - input checks and utests (SpacePossum, keradus)
* bug #3420 SingleLineCommentStyleFixer - fix 'strpos() expects parameter 1 to be string, boolean given' (keradus, SpacePossum)
* bug #3428 Fix archive analysing (keradus)
* bug #3429 Fix archive analysing (keradus)
* minor #3137 PHPUnit - use common base class (keradus)
* minor #3311 FinalInternalClassFixer - fix typo (localheinz)
* minor #3328 Remove duplicated space in exceptions (keradus)
* minor #3342 PhpUnitDedicateAssertFixer - Remove unexistent method is_boolean  (carusogabriel)
* minor #3345 StdinFileInfo - fix __toString (keradus)
* minor #3346 StdinFileInfo - drop getContents (keradus)
* minor #3347 DX: reapply newest CS (keradus)
* minor #3365 COOKBOOK-FIXERS.md - update to provide definition instead of description (keradus)
* minor #3370 AbstractFixer - FQCN in in exceptions (Slamdunk)
* minor #3372 ProjectCodeTest - fix comment (keradus)
* minor #3393 Method call typos (Slamdunk, keradus)
* minor #3402 Always provide delimiter to `preg_quote` calls (ntzm)
* minor #3403 Remove unused import (ntzm)
* minor #3405 Fix `fopen` mode (ntzm)
* minor #3407 CombineConsecutiveIssetsFixer - Improve description (kubawerlos)
* minor #3408 Improving fixers descriptions (kubawerlos)
* minor #3409 move itests from misc to priority (keradus)
* minor #3411 Better type hinting for AbstractFixerTestCase::$fixer (kubawerlos)
* minor #3412 Convert `strtolower` inside `strpos` to just `stripos` (ntzm)
* minor #3421 DX: Use ::class (keradus)
* minor #3424 AbstractFixerTest: fix expectException arguments (Slamdunk, keradus)
* minor #3425 FixerFactoryTest - test that priority pair fixers have itest (keradus, SpacePossum)
* minor #3427 ConfigurationResolver: fix @return annotation (Slamdunk)

Changelog for v2.9.0
--------------------

* feature #3063 Method chaining indentation fixer (boliev, julienfalque)
* feature #3076 Add ExplicitStringVariableFixer (Slamdunk, keradus)
* feature #3098 MethodSeparationFixer - add class elements separation options (SpacePossum, keradus)
* feature #3155 Add EscapeImplicitBackslashesFixer (Slamdunk)
* feature #3164 Add ExplicitIndirectVariableFixer (Slamdunk, keradus)
* feature #3183 FinalInternalClassFixer introduction (keradus, SpacePossum)
* feature #3187 StaticLambdaFixer - introduction (SpacePossum, keradus)
* feature #3209 PhpdocAlignFixer - Make @method alignable (ntzm)
* feature #3275 Add PhpUnitTestAnnotationFixer (BackEndTea, keradus)

Changelog for v2.8.4
--------------------

* bug #3281 SelfAccessorFixer - stop modifying traits (kubawerlos)
* minor #3195 Add self-update command test (julienfalque)
* minor #3287 FileCacheManagerTest - drop duplicated line (keradus)
* minor #3292 PHPUnit - set memory limit (veewee)
* minor #3306 Token - better input validation (keradus)
* minor #3310 Upgrade PHP Coveralls (keradus)

Changelog for v2.8.3
--------------------

* bug #3173 SimplifiedNullReturnFixer - handle nullable return types (Slamdunk)
* bug #3268 PhpUnitNoExpectationAnnotationFixer - add case with backslashes (keradus, Slamdunk)
* bug #3272 PhpdocTrimFixer - unicode support (SpacePossum)

Changelog for v2.8.2
--------------------

* bug #3225 PhpdocTrimFixer - Fix handling of lines without leading asterisk (julienfalque)
* bug #3241 NoExtraConsecutiveBlankLinesFixer - do not crash on ^M LF only (SpacePossum)
* bug #3242 PhpUnitNoExpectationAnnotationFixer - fix ' handling (keradus)
* bug #3243 PhpUnitExpectationFixer - don't create ->expectExceptionMessage(null) (keradus)
* bug #3244 PhpUnitNoExpectationAnnotationFixer - expectation extracted from annotation shall be separated from rest of code with one blank line (keradus)
* bug #3259 PhpUnitNamespacedFixer - fix isCandidate to not rely on class declaration (keradus)
* bug #3261 PhpUnitNamespacedFixer - properly fix next usage of already fixed class (keradus)
* bug #3262 ToolInfo - support installation by branch as well (keradus)
* bug #3263 NoBreakCommentFixer - Fix handling comment text with PCRE characters (julienfalque)
* bug #3266 PhpUnitConstructFixer - multiple asserts bug (kubawerlos)
* minor #3239 Improve contributing guide and issue template (julienfalque)
* minor #3246 Make ToolInfo methods non-static (julienfalque)
* minor #3249 PhpUnitNoExpectationAnnotationFixerTest - fix hidden conflict (keradus)
* minor #3250 Travis: fail early, spare resources, save the Earth (Slamdunk, keradus)
* minor #3251 Create Title for config file docs section (IanEdington)
* minor #3254 AutoReview/FixerFactoryTest::testFixersPriority: verbose assertion message (Slamdunk)
* minor #3255 IntegrationTest: output exception stack trace (Slamdunk)
* minor #3257 README.rst - Fixed bullet list formatting (moebrowne)

Changelog for v2.8.1
--------------------

* bug #3199 TokensAnalyzer - getClassyElements (SpacePossum)
* bug #3208 BracesFixer - Fix for instantiation in control structures (julienfalque, SpacePossum)
* bug #3215 BinaryOperatorSpacesFixer - Fix spaces around multiple exception catching (ntzm)
* bug #3216 AbstractLinesBeforeNamespaceFixer - add min. and max. option, not only single target count (SpacePossum)
* bug #3217 TokenizerLinter - fix lack of linting when code is cached (SpacePossum, keradus)
* minor #3200 Skip slow test when Xdebug is loaded (julienfalque)
* minor #3211 Use udiff format in CI (julienfalque)
* minor #3212 Handle rulesets unknown to fabbot.io (julienfalque)
* minor #3219 Normalise references to GitHub in docs (ntzm)
* minor #3226 Remove unused imports (ntzm)
* minor #3231 Fix typos (ntzm)
* minor #3234 Simplify Cache\Signature::equals (ntzm)
* minor #3237 UnconfigurableFixer - use only LF (keradus)
* minor #3238 AbstractFixerTest - fix @cover annotation (keradus)

Changelog for v2.8.0
--------------------

* feature #3065 Add IncrementStyleFixer (kubawerlos)
* feature #3119 Feature checkstyle reporter (K-Phoen)
* feature #3162 Add udiff as diff format (SpacePossum, keradus)
* feature #3170 Add CompactNullableTypehintFixer (jfcherng)
* feature #3189 Add PHP_CS_FIXER_FUTURE_MODE env (keradus)
* feature #3201 Add PHPUnit Migration rulesets and fixers (keradus)
* minor #3149 AbstractProxyFixer - Support multiple proxied fixers (julienfalque)
* minor #3160 Add DeprecatedFixerInterface (kubawerlos)
* minor #3185 IndentationTypeFixerTest - clean up (SpacePossum, keradus)
* minor #3198 Cleanup: add test that there is no deprecated fixer in rule set (kubawerlos)

Changelog for v2.7.5
--------------------

* bug #3225 PhpdocTrimFixer - Fix handling of lines without leading asterisk (julienfalque)
* bug #3241 NoExtraConsecutiveBlankLinesFixer - do not crash on ^M LF only (SpacePossum)
* bug #3262 ToolInfo - support installation by branch as well (keradus)
* bug #3263 NoBreakCommentFixer - Fix handling comment text with PCRE characters (julienfalque)
* bug #3266 PhpUnitConstructFixer - multiple asserts bug (kubawerlos)
* minor #3239 Improve contributing guide and issue template (julienfalque)
* minor #3246 Make ToolInfo methods non-static (julienfalque)
* minor #3250 Travis: fail early, spare resources, save the Earth (Slamdunk, keradus)
* minor #3251 Create Title for config file docs section (IanEdington)
* minor #3254 AutoReview/FixerFactoryTest::testFixersPriority: verbose assertion message (Slamdunk)
* minor #3255 IntegrationTest: output exception stack trace (Slamdunk)

Changelog for v2.7.4
--------------------

* bug #3199 TokensAnalyzer - getClassyElements (SpacePossum)
* bug #3208 BracesFixer - Fix for instantiation in control structures (julienfalque, SpacePossum)
* bug #3215 BinaryOperatorSpacesFixer - Fix spaces around multiple exception catching (ntzm)
* bug #3216 AbstractLinesBeforeNamespaceFixer - add min. and max. option, not only single target count (SpacePossum)
* bug #3217 TokenizerLinter - fix lack of linting when code is cached (SpacePossum, keradus)
* minor #3200 Skip slow test when Xdebug is loaded (julienfalque)
* minor #3219 Normalise references to GitHub in docs (ntzm)
* minor #3226 Remove unused imports (ntzm)
* minor #3231 Fix typos (ntzm)
* minor #3234 Simplify Cache\Signature::equals (ntzm)
* minor #3237 UnconfigurableFixer - use only LF (keradus)
* minor #3238 AbstractFixerTest - fix @cover annotation (keradus)

Changelog for v2.7.3
--------------------

* bug #3114 SelfAccessorFixer - Fix type declarations replacement (julienfalque)

Changelog for v2.7.2
--------------------

* bug #3062 BraceClassInstantiationTransformer - Fix instantiation inside method call braces case (julienfalque, keradus)
* bug #3083 SingleBlankLineBeforeNamespaceFixer - Fix handling namespace right after opening tag (mlocati)
* bug #3109 SwitchCaseSemicolonToColonFixer - Fix bug with nested constructs (SpacePossum)
* bug #3117 Multibyte character in array key makes alignment incorrect (kubawerlos)
* bug #3123 Cache - File permissions (SpacePossum)
* bug #3138 NoHomoglyphNamesFixer - fix crash on non-ascii but not mapped either (SpacePossum)
* bug #3172 IndentationTypeFixer - do not touch whitespace that is not indentation (SpacePossum)
* bug #3176 NoMultilineWhitespaceBeforeSemicolonsFixer - SpaceAfterSemicolonFixer - priority fix (SpacePossum)
* bug #3193 TokensAnalyzer::getClassyElements - sort result before returning (SpacePossum)
* bug #3196 SelfUpdateCommand - fix exit status when can't determine newest version (julienfalque)
* minor #3107 ConfigurationResolver - improve error message when rule is not found (SpacePossum)
* minor #3113 Add WordMatcher (keradus)
* minor #3128 README: remove deprecated rule from CLI examples (chteuchteu)
* minor #3133 Unify Reporter tests (keradus)
* minor #3134 Allow Symfony 4 (keradus, garak)
* minor #3136 PHPUnit - call hooks from parent class as well (keradus)
* minor #3141 Unify description of deprecated fixer (kubawerlos)
* minor #3144 PhpUnitDedicateAssertFixer - Sort map and array by function name (localheinz)
* minor #3145 misc - Typo (localheinz)
* minor #3150 Fix CircleCI (julienfalque)
* minor #3151 Update gitattributes to ignore next file (keradus)
* minor #3156 Update php-coveralls (keradus)
* minor #3166 README - add link to new gitter channel. (SpacePossum)
* minor #3174 Update UPGRADE.md (vitek-rostislav)
* minor #3180 Fix usage of static variables (kubawerlos)
* minor #3182 Add support for PHPUnit 6, drop PHPUnit 4 (keradus)
* minor #3184 Code grooming - sort content of arrays (keradus)
* minor #3191 Travis - add nightly build to allow_failures due to Travis issues (keradus)
* minor #3197 DX groom CS (keradus)

Changelog for v2.7.1
--------------------

* bug #3115 NoUnneededFinalMethodFixer - fix edge case (Slamdunk)

Changelog for v2.7.0
--------------------

* feature #2573 BinaryOperatorSpaces reworked (SpacePossum, keradus)
* feature #3073 SpaceAfterSemicolonFixer - Add option to remove space in empty for expressions (julienfalque)
* feature #3089 NoAliasFunctionsFixer - add imap aliases (Slamdunk)
* feature #3093 NoUnneededFinalMethodFixer - Remove final keyword from private methods (localheinz, keradus)
* minor #3068 Symfony:risky ruleset - add no_homoglyph_names (keradus)
* minor #3074 [IO] Replace Diff with fork version (SpacePossum)

Changelog for v2.6.1
--------------------

* bug #3052 Fix false positive warning about paths overridden by provided as command arguments (kubawerlos)
* bug #3053 CombineConsecutiveIssetsFixer - fix priority (SpacePossum)
* bug #3058 IsNullFixer - fix whitespace handling (roukmoute)
* bug #3069 MethodArgumentSpaceFixer - new test case (keradus)
* bug #3072 IsNullFixer - fix non_yoda_style edge case (keradus)
* bug #3088 Drop dedicated Phar stub (keradus)
* bug #3100 NativeFunctionInvocationFixer - Fix test if previous token is already namespace separator (SpacePossum)
* bug #3104 DoctrineAnnotationIndentationFixer - Fix str_repeat() error (julienfalque)
* minor #3038 Support PHP 7.2 (SpacePossum, keradus)
* minor #3064 Fix couple of typos (KKSzymanowski)
* minor #3070 YodaStyleFixer - Clarify configuration parameters (SteveJobzniak)
* minor #3078 ConfigurationResolver - hide context while including config file (keradus)
* minor #3080 Direct function call instead of by string (kubawerlos)
* minor #3085 CiIntegrationTest - skip when no git is available (keradus)
* minor #3087 phar-stub.php - allow PHP 7.2 (keradus)
* minor #3092 .travis.yml - fix matrix for PHP 7.1 (keradus)
* minor #3094 NoUnneededFinalMethodFixer - Add test cases (julienfalque)
* minor #3111 DoctrineAnnotationIndentationFixer - Restore test case (julienfalque)

Changelog for v2.6.0
--------------------

* bug #3039 YodaStyleFixer - Fix echo case (SpacePossum, keradus)
* feature #2446 Add YodaStyleFixer (SpacePossum)
* feature #2940 Add NoHomoglyphNamesFixer (mcfedr, keradus)
* feature #3012 Add CombineConsecutiveIssetsFixer (SpacePossum)
* minor #3037 Update SF rule set (SpacePossum)

Changelog for v2.5.1
--------------------

* bug #3002 Bugfix braces (mnabialek)
* bug #3010 Fix handling of Github releases (julienfalque, keradus)
* bug #3015 Fix exception arguments (julienfalque)
* bug #3016 Verify phar file (keradus)
* bug #3021 Risky rules cleanup (kubawerlos)
* bug #3023 RandomApiMigrationFixer - "rand();" to "random_int(0, getrandmax());" fixing (SpacePossum)
* bug #3024 ConfigurationResolver - Handle empty "rules" value (SpacePossum, keradus)
* bug #3031 IndentationTypeFixer - fix handling tabs in indented comments (keradus)
* minor #2999 Notice when paths from config file are overridden by command arguments (julienfalque, keradus)
* minor #3007 Add PHP 7.2 to Travis build matrix (Jean85)
* minor #3009 CiIntegrationTest - run local (SpacePossum)
* minor #3013 Adjust phpunit configuration (localheinz)
* minor #3017 Fix: Risky tests (localheinz)
* minor #3018 Fix: Make sure that data providers are named correctly (localheinz, keradus)
* minor #3032 .php_cs.dist - handling UnexpectedValueException (keradus)
* minor #3033 Use ::class (keradus)
* minor #3034 Follow newest CS (keradus)
* minor #3036 Drop not existing Standalone group from PHPUnit configuration and duplicated internal tags (keradus)
* minor #3042 Update gitter address (keradus)

Changelog for v2.5.0
--------------------

* feature #2770 DoctrineAnnotationSpaces - split assignments options (julienfalque)
* feature #2843 Add estimating-max progress output type (julienfalque)
* feature #2885 Add NoSuperfluousElseifFixer (julienfalque)
* feature #2929 Add NoUnneededCurlyBracesFixer (SpacePossum)
* feature #2944 FunctionToConstantFixer - handle get_class() -> __CLASS__ as well (SpacePossum)
* feature #2953 BlankLineBeforeStatementFixer - Add more statements (localheinz, keradus)
* feature #2972 Add NoUnneededFinalMethodFixer (Slamdunk, keradus)
* feature #2992 Add Doctrine Annotation ruleset (julienfalque)
* minor #2926 Token::getNameForId (SpacePossum)

Changelog for v2.4.2
--------------------

* bug #3002 Bugfix braces (mnabialek)
* bug #3010 Fix handling of Github releases (julienfalque, keradus)
* bug #3015 Fix exception arguments (julienfalque)
* bug #3016 Verify phar file (keradus)
* bug #3021 Risky rules cleanup (kubawerlos)
* bug #3023 RandomApiMigrationFixer - "rand();" to "random_int(0, getrandmax());" fixing (SpacePossum)
* bug #3024 ConfigurationResolver - Handle empty "rules" value (SpacePossum, keradus)
* bug #3031 IndentationTypeFixer - fix handling tabs in indented comments (keradus)
* minor #2999 Notice when paths from config file are overridden by command arguments (julienfalque, keradus)
* minor #3007 Add PHP 7.2 to Travis build matrix (Jean85)
* minor #3009 CiIntegrationTest - run local (SpacePossum)
* minor #3013 Adjust phpunit configuration (localheinz)
* minor #3017 Fix: Risky tests (localheinz)
* minor #3018 Fix: Make sure that data providers are named correctly (localheinz, keradus)
* minor #3032 .php_cs.dist - handling UnexpectedValueException (keradus)
* minor #3033 Use ::class (keradus)
* minor #3034 Follow newest CS (keradus)
* minor #3036 Drop not existing Standalone group from PHPUnit configuration and duplicated internal tags (keradus)
* minor #3042 Update gitter address (keradus)

Changelog for v2.4.1
--------------------

* bug #2925 Improve CI integration suggestion (julienfalque)
* bug #2928 TokensAnalyzer::getClassyElements - Anonymous class support (SpacePossum)
* bug #2931 Psr0Fixer, Psr4Fixer - ignore "new class" syntax (dg, keradus)
* bug #2934 Config - fix handling rule without value (keradus, SpacePossum)
* bug #2939 NoUnusedImportsFixer - Fix extra blank line (julienfalque)
* bug #2941 PHP 7.2 - Group imports with trailing comma support (SpacePossum, julienfalque)
* bug #2954 NoBreakCommentFixer - Disable case sensitivity (julienfalque)
* bug #2959 MethodArgumentSpaceFixer - Skip body of fixed function (greg0ire)
* bug #2984 AlignMultilineCommentFixer - handle uni code (SpacePossum)
* bug #2987 Fix incorrect indentation of comments in `braces` fixer (rob006)
* minor #2924 Add missing Token deprecations (julienfalque)
* minor #2927 WhiteSpaceConfig - update message copy and more strict tests (SpacePossum, keradus)
* minor #2930 Trigger website build (keradus)
* minor #2932 Integrate CircleCI (keradus, aidantwoods)
* minor #2933 ProcessLinterTest - Ensure Windows test only runs on Windows, add a Mac test execution (aidantwoods)
* minor #2935 special handling of fabbot.io service if it's using too old PHP CS Fixer version (keradus)
* minor #2937 Travis: execute 5.3 job on precise (keradus)
* minor #2938 Tests fix configuration of project (SpacePossum, keradus)
* minor #2943 FunctionToConstantFixer - test with diff. arguments than fixable (SpacePossum)
* minor #2945 BlankLineBeforeStatementFixerTest - Fix covered class (julienfalque)
* minor #2946 Detect extra old installations (keradus)
* minor #2947 Test suggested CI integration (keradus)
* minor #2951 AccessibleObject - remove most of usage (keradus)
* minor #2952 BlankLineBeforeStatementFixer - Reference fixer instead of test class (localheinz)
* minor #2955 Travis - stop using old TASK_SCA residue (keradus)
* minor #2968 AssertTokensTrait - don't use AccessibleObject (keradus)
* minor #2969 Shrink down AccessibleObject usage (keradus)
* minor #2982 TrailingCommaInMultilineArrayFixer - simplify isMultilineArray condition (TomasVotruba)
* minor #2989 CiIntegrationTest - fix min supported PHP versions (keradus)

Changelog for v2.4.0
--------------------

* bug #2880 NoBreakCommentFixer - fix edge case (julienfalque)
* bug #2900 VoidReturnFixer - handle functions containing anonymous functions/classes (bendavies, keradus)
* bug #2902 Fix test classes constructor (julienfalque)
* feature #2384 Add BlankLineBeforeStatementFixer (localheinz, keradus, SpacePossum)
* feature #2440 MethodArgumentSpaceFixer - add ensure_fully_multiline option (greg0ire)
* feature #2649 PhpdocAlignFixer - make fixer configurable (ntzm)
* feature #2664 Add DoctrineAnnotationArrayAssignmentFixer (julienfalque)
* feature #2667 Add NoBreakCommentFixer (julienfalque)
* feature #2684 BracesFixer - new options for braces position after control structures and anonymous constructs (aidantwoods, keradus)
* feature #2701 NoExtraConsecutiveBlankLinesFixer - Add more configuration options related to switch statements (SpacePossum)
* feature #2740 Add VoidReturnFixer (mrmark)
* feature #2765 DoctrineAnnotationIndentationFixer - add option to indent mixed lines (julienfalque)
* feature #2815 NonPrintableCharacterFixer - Add option to replace with escape sequences (julienfalque, keradus)
* feature #2822 Add NoNullPropertyInitializationFixer (ntzm, julienfalque, SpacePossum)
* feature #2825 Add PhpdocTypesOrderFixer (julienfalque, keradus)
* feature #2856 CastSpacesFixer - add space option (kubawerlos, keradus)
* feature #2857 Add AlignMultilineCommentFixer (Slamdunk, keradus)
* feature #2866 Add SingleLineCommentStyleFixer, deprecate HashToSlashCommentFixer (Slamdunk, keradus)
* minor #2773 Travis - use stages (keradus)
* minor #2794 Drop HHVM support (keradus, julienfalque)
* minor #2801 ProjectCodeTest - Fix typo in deprecation message (SpacePossum)
* minor #2818 Token become immutable, performance optimizations (keradus)
* minor #2877 Fix PHPMD report (julienfalque)
* minor #2894 NonPrintableCharacterFixer - fix handling required PHP version on PHPUnit 4.x (keradus)
* minor #2921 InvalidForEnvFixerConfigurationException - fix handling in tests on 2.4 line (keradus)

Changelog for v2.3.3
--------------------

* bug #2807 NoUselessElseFixer - Fix detection of conditional block (SpacePossum)
* bug #2809 Phar release - fix readme generation (SpacePossum, keradus)
* bug #2827 MethodArgumentSpaceFixer - Always remove trailing spaces (julienfalque)
* bug #2835 SelfAccessorFixer - class property fix (mnabialek)
* bug #2848 PhpdocIndentFixer - fix edge case with inline phpdoc (keradus)
* bug #2849 BracesFixer - Fix indentation issues with comments (julienfalque)
* bug #2851 Tokens - ensureWhitespaceAtIndex (GrahamCampbell, SpacePossum)
* bug #2854 NoLeadingImportSlashFixer - Removing leading slash from import even when in global space (kubawerlos)
* bug #2858 Support generic types (keradus)
* bug #2869 Fix handling required configuration (keradus)
* bug #2881 NoUnusedImportsFixer - Bug when trying to insert empty token (GrahamCampbell, keradus)
* bug #2882 DocBlock\Annotation - Fix parsing of collections with multiple key types (julienfalque)
* bug #2886 NoSpacesInsideParenthesisFixer - Do not remove whitespace if next token is comment (SpacePossum)
* bug #2888 SingleImportPerStatementFixer - Add support for function and const (SpacePossum)
* bug #2901 Add missing files to archive files (keradus)
* bug #2914 HeredocToNowdocFixer - works with CRLF line ending (dg)
* bug #2920 RuleSet - Update deprecated configuration of fixers (SpacePossum, keradus)
* minor #1531 Update docs for few generic types (keradus)
* minor #2793 COOKBOOK-FIXERS.md - update to current version, fix links (keradus)
* minor #2812 ProcessLinter - compatibility with Symfony 3.3 (keradus)
* minor #2816 Tokenizer - better docs and validation (keradus)
* minor #2817 Tokenizer - use future-compatible interface (keradus)
* minor #2819 Fix benchmark (keradus)
* minor #2820 MagicConstantCasingFixer - Remove defined check (SpacePossum)
* minor #2823 Tokenizer - use future-compatible interface (keradus)
* minor #2824 code grooming (keradus)
* minor #2826 Exceptions - provide utests (localheinz)
* minor #2828 Enhancement: Reference phpunit.xsd from phpunit.xml.dist (localheinz)
* minor #2830 Differs - add tests (localheinz)
* minor #2832 Fix: Use all the columns (localheinz)
* minor #2833 Doctrine\Annotation\Token - provide utests (localheinz)
* minor #2839 Use PHP 7.2 polyfill instead of xml one (keradus)
* minor #2842 Move null to first position in PHPDoc types (julienfalque)
* minor #2850 ReadmeCommandTest - Prevent diff output (julienfalque)
* minor #2859 Fixed typo and dead code removal (GrahamCampbell)
* minor #2863 FileSpecificCodeSample - add tests (localheinz)
* minor #2864 WhitespacesAwareFixerInterface clean up (Slamdunk)
* minor #2865 AutoReview\FixerTest - test configuration samples (SpacePossum, keradus)
* minor #2867 VersionSpecification - Fix copy-paste typo (SpacePossum)
* minor #2870 Tokens - ensureWhitespaceAtIndex - Clear tokens before compare. (SpacePossum)
* minor #2874 LineTest - fix typo (keradus)
* minor #2875 HelpCommand - recursive layout fix (SpacePossum)
* minor #2883 DescribeCommand - Show which sample uses the default configuration  (SpacePossum)
* minor #2887 Housekeeping - Strict whitespace checks (SpacePossum)
* minor #2895 ProjectCodeTest - check that classes in no-tests exception exist (keradus)
* minor #2896 Move testing related classes from src to tests (keradus)
* minor #2904 Reapply CS (keradus)
* minor #2910 PhpdocAnnotationWithoutDotFixer - Restrict lowercasing (oschwald)
* minor #2913 Tests - tweaks (SpacePossum, keradus)
* minor #2916 FixerFactory - drop return in sortFixers(), never used (TomasVotruba)

Changelog for v2.3.2
--------------------

* bug #2682 DoctrineAnnotationIndentationFixer - fix handling nested annotations (edhgoose, julienfalque)
* bug #2700 Fix Doctrine Annotation end detection (julienfalque)
* bug #2715 OrderedImportsFixer - handle indented groups (pilgerone)
* bug #2732 HeaderCommentFixer - fix handling blank lines (s7b4)
* bug #2745 Fix Doctrine Annotation newlines (julienfalque)
* bug #2752 FixCommand - fix typo in warning message (mnapoli)
* bug #2757 GeckoPHPUnit is not dev dependency (keradus)
* bug #2759 Update gitattributes (SpacePossum)
* bug #2763 Fix describe command with PSR-0 fixer (julienfalque)
* bug #2768 Tokens::ensureWhitespaceAtIndex - clean up comment check, add check for T_OPEN (SpacePossum)
* bug #2783 Tokens::ensureWhitespaceAtIndex - Fix handling line endings (SpacePossum)
* minor #2304 DX: use PHPMD (keradus)
* minor #2663 Use colors for keywords in commands output (julienfalque, keradus)
* minor #2706 Update README (SpacePossum)
* minor #2714 README.rst - fix wrong value in example (mleko)
* minor #2718 Remove old Symfony exception message expectation (julienfalque)
* minor #2721 Update phpstorm article link to a fresh blog post (valeryan)
* minor #2725 Use method chaining for configuration definitions (julienfalque)
* minor #2727 PHPUnit - use speedtrap (keradus)
* minor #2728 SelfUpdateCommand - verify that it's possible to replace current file (keradus)
* minor #2729 DescribeCommand - add decorated output test (julienfalque)
* minor #2731 BracesFixer - properly pass config in utest dataProvider (keradus)
* minor #2738 Upgrade tests to use new, namespaced PHPUnit TestCase class (keradus)
* minor #2742 Code cleanup (GrahamCampbell, keradus)
* minor #2743 Fixing example and description for GeneralPhpdocAnnotationRemoveFixer (kubawerlos)
* minor #2744 AbstractDoctrineAnnotationFixerTestCase - split fixers test cases (julienfalque)
* minor #2755 Fix compatibility with PHPUnit 5.4.x (keradus)
* minor #2758 Readme - improve CI integration guidelines (keradus)
* minor #2769 Psr0Fixer - remove duplicated example (julienfalque)
* minor #2774 AssertTokens Trait (keradus)
* minor #2775 NoExtraConsecutiveBlankLinesFixer - remove duplicate code sample. (SpacePossum)
* minor #2778 AutoReview - watch that code samples are unique (keradus)
* minor #2787 Add warnings about missing dom ext and require json ext (keradus)
* minor #2792 Use composer-require-checker (keradus)
* minor #2796 Update .gitattributes (SpacePossum)
* minor #2797 Update .gitattributes (SpacePossum)
* minor #2800 PhpdocTypesFixerTest - Fix typo in covers annotation (SpacePossum)

Changelog for v2.3.1
--------------------

Port of v2.2.3.

* bug #2724 Revert #2554 Add short diff. output format (keradus)

Changelog for v2.3.0
--------------------

* feature #2450 Add ListSyntaxFixer (SpacePossum)
* feature #2708 Add PhpUnitTestClassRequiresCoversFixer (keradus)
* minor #2568 Require PHP 5.6+ (keradus)
* minor #2672 Bump symfony/* deps (keradus)

Changelog for v2.2.20
---------------------

* bug #3233 PhpdocAlignFixer - Fix linebreak inconsistency (SpacePossum, keradus)
* bug #3445 Rewrite NoUnusedImportsFixer (kubawerlos, julienfalque)
* bug #3597 DeclareStrictTypesFixer - fix bug of removing line (kubawerlos, keradus)
* bug #3605 DoctrineAnnotationIndentationFixer - Fix indentation with mixed lines (julienfalque)
* bug #3606 PhpdocToCommentFixer - allow multiple ( (SpacePossum)
* bug #3684 PhpUnitStrictFixer - Do not fix if not correct # of arguments are used (SpacePossum)
* bug #3715 SingleImportPerStatementFixer - Fix handling whitespace before opening brace (julienfalque)
* bug #3731 PhpdocIndentFixer -  crash fix (SpacePossum)
* bug #3765 Fix binary-prefixed double-quoted strings to single quotes (ntzm)
* bug #3770 Handle binary flags in heredoc_to_nowdoc (ntzm)
* bug #3790 ProcessLinter - don't execute external process without timeout! It can freeze! (keradus)
* minor #3548 Make shell scripts POSIX-compatible (EvgenyOrekhov, keradus)
* minor #3568 New Autoreview: Correct option casing (ntzm)
* minor #3590 Use XdebugHandler to avoid performance penalty (AJenbo, keradus)
* minor #3607 PhpdocVarWithoutNameFixer - update sample with @ type (SpacePossum)
* minor #3617 Tests stability patches (Tom Klingenberg, keradus)
* minor #3627 Fix tests execution under phpdbg (keradus)
* minor #3629 ProjectFixerConfigurationTest - test rules are sorted (SpacePossum)
* minor #3639 DX: use benefits of symfony/force-lowest (keradus)
* minor #3641 Update check_trailing_spaces script with upstream (keradus)
* minor #3646 Extract SameStringsConstraint and XmlMatchesXsdConstraint (keradus)
* minor #3647 DX: Add CachingLinter for tests (keradus)
* minor #3649 Update check_trailing_spaces script with upstream (keradus)
* minor #3652 CiIntegrationTest - run tests with POSIX sh, not Bash (keradus)
* minor #3656 DX: Clean ups (SpacePossum)
* minor #3660 Fix  do not rely on order of fixing in CiIntegrationTest (kubawerlos)
* minor #3662 DX: Add Smoke/InstallViaComposerTest (keradus)
* minor #3663 DX: During deployment, run all smoke tests and don't allow to skip phar-related ones (keradus)
* minor #3665 CircleCI fix (kubawerlos)
* minor #3666 Use "set -eu" in shell scripts (EvgenyOrekhov)
* minor #3669 Document possible values for subset options (julienfalque, keradus)
* minor #3676 RunnerTest - workaround for failing Symfony v2.8.37 (kubawerlos)
* minor #3680 DX: Tokens - removeLeadingWhitespace and removeTrailingWhitespace must act in same way (SpacePossum)
* minor #3686 README.rst - Format all code-like strings in fixer descriptions (ntzm, keradus)
* minor #3692 DX: Optimize tests (julienfalque)
* minor #3701 Use correct casing for "PHPDoc" (ntzm)
* minor #3703 DX: InstallViaComposerTets - groom naming (keradus)
* minor #3704 DX: Tokens - fix naming (keradus)
* minor #3706 Update homebrew installation instructions (ntzm)
* minor #3713 Use HTTPS whenever possible (fabpot)
* minor #3723 Extend tests coverage (ntzm)
* minor #3733 Disable Composer optimized autoloader by default (julienfalque)
* minor #3748 PhpUnitStrictFixer - extend risky note (jnvsor)
* minor #3749 Make sure PHPUnit is cased correctly in fixers descriptions (kubawerlos)
* minor #3773 AbstractFixerWithAliasedOptionsTestCase - don't export (keradus)
* minor #3796 DX: StdinTest - do not assume name of folder, into which project was cloned (keradus)
* minor #3803 NoEmptyPhpdocFixer/PhpdocAddMissingParamAnnotationFixer - missing priority test (SpacePossum, keradus)
* minor #3804 Cleanup: remove useless constructor comment (kubawerlos)

Changelog for v2.2.19
---------------------

* bug #3594 ElseifFixer - Bug with alternative syntax (kubawerlos)
* bug #3600 StrictParamFixer - Fix issue when functions are imported (ntzm, keradus)
* minor #3589 FixerFactoryTest - add missing test (SpacePossum, keradus)
* minor #3610 make phar extension optional (Tom Klingenberg, keradus)
* minor #3612 Travis - allow for hhvm failures (keradus)
* minor #3615 Detect fabbot.io (julienfalque, keradus)
* minor #3616 FixerFactoryTest - Don't rely on autovivification (keradus)

Changelog for v2.2.18
---------------------

* bug #3446 Add PregWrapper (kubawerlos)
* bug #3464 IncludeFixer - fix incorrect order of fixing (kubawerlos, SpacePossum)
* bug #3496 Bug in Tokens::removeLeadingWhitespace (kubawerlos, SpacePossum, keradus)
* bug #3557 AbstractDoctrineAnnotationFixer: edge case bugfix (Slamdunk)
* bug #3574 GeneralPhpdocAnnotationRemoveFixer - remove PHPDoc if no content is left (SpacePossum)
* minor #3563 DX add missing covers annotations (keradus)
* minor #3565 Use EventDispatcherInterface instead of EventDispatcher when possible (keradus)
* minor #3572 DX: allow for more phpunit-speedtrap versions to support more PHPUnit versions (keradus)
* minor #3576 Fix Doctrine Annotation test cases merging (julienfalque)

Changelog for v2.2.17
---------------------

* bug #3504 NoBlankLinesAfterPhpdocFixer - allow blank line before declare statement (julienfalque)
* bug #3522 Remove LOCK_EX (SpacePossum)
* bug #3560 SelfAccessorFixer is risky (Slamdunk)
* minor #3435 Add tests for general_phpdoc_annotation_remove (BackEndTea)
* minor #3484 Create Tokens::findBlockStart (ntzm)
* minor #3512 Add missing array typehints (ntzm)
* minor #3516 Use `null|type` instead of `?type` in PHPDocs (ntzm)
* minor #3518 FixerFactoryTest - Test each priority test file is listed as test (SpacePossum)
* minor #3520 Fix typos: ran vs. run (SpacePossum)
* minor #3521 Use HTTPS (carusogabriel)
* minor #3526 Remove gecko dependency (SpacePossum, keradus, julienfalque)
* minor #3531 Backport PHPMD to LTS version to ease maintainability (keradus)
* minor #3532 Implement Tokens::findOppositeBlockEdge (ntzm)
* minor #3533 DX: SCA - drop src/Resources exclusion (keradus)
* minor #3538 Don't use third parameter of Tokens::findBlockStart (ntzm)
* minor #3542 Enhancement: Run composer-normalize on Travis CI (localheinz, keradus)
* minor #3555 DX: composer.json - drop branch-alias, branch is already following the version (keradus)
* minor #3556 DX: Add AutoReview/ComposerTest (keradus)
* minor #3559 Don't expose new files under Test namespace (keradus)

Changelog for v2.2.16
---------------------

* bug #3502 Fix missing file in export (keradus)

Changelog for v2.2.15
---------------------

* bug #3367 NoUnusedImportsFixer - fix comment handling (SpacePossum, keradus)
* bug #3455 NoEmptyCommentFixer - comment block detection for line ending different than LF (kubawerlos, SpacePossum)
* bug #3458 SilencedDeprecationErrorFixer - fix edge cases (kubawerlos)
* bug #3466 no_whitespace_in_blank_line and no_blank_lines_after_phpdoc fixers bug (kubawerlos, keradus)
* minor #3354 Added missing types to the PhpdocTypesFixer (GrahamCampbell)
* minor #3406 Fix for escaping in README (kubawerlos)
* minor #3431 Add missing tests (SpacePossum)
* minor #3440 Add a handful of integration tests (BackEndTea)
* minor #3444 IntegrationTest - ensure tests in priority dir are priority tests indeed (keradus)
* minor #3494 Add missing PHPDoc param type (ntzm)
* minor #3495 Swap @var type and element (ntzm)
* minor #3498 NoUnusedImportsFixer - fix deprecation (keradus)

Changelog for v2.2.14
---------------------

* bug #3298 DiffConsoleFormatter - fix output escaping. (SpacePossum)
* bug #3337 BracesFixer: nowdoc bug on template files (Slamdunk)
* bug #3349 Fix stdin handling and add tests for it (keradus)
* bug #3359 BracesFixer - handle comment for content outside of given block (keradus)
* bug #3415 FileFilterIterator - input checks and utests (SpacePossum, keradus)
* bug #3429 Fix archive analysing (keradus)
* minor #3137 PHPUnit - use common base class (keradus)
* minor #3342 PhpUnitDedicateAssertFixer - Remove unexistent method is_boolean  (carusogabriel)
* minor #3345 StdinFileInfo - fix `__toString` (keradus)
* minor #3346 StdinFileInfo - drop getContents (keradus)
* minor #3347 DX: reapply newest CS (keradus)
* minor #3365 COOKBOOK-FIXERS.md - update to provide definition instead of description (keradus)
* minor #3370 AbstractFixer - FQCN in in exceptions (Slamdunk)
* minor #3372 ProjectCodeTest - fix comment (keradus)
* minor #3402 Always provide delimiter to `preg_quote` calls (ntzm)
* minor #3403 Remove unused import (ntzm)
* minor #3405 Fix `fopen` mode (ntzm)
* minor #3408 Improving fixers descriptions (kubawerlos)
* minor #3409 move itests from misc to priority (keradus)
* minor #3411 Better type hinting for AbstractFixerTestCase::$fixer (kubawerlos)
* minor #3412 Convert `strtolower` inside `strpos` to just `stripos` (ntzm)
* minor #3425 FixerFactoryTest - test that priority pair fixers have itest (keradus, SpacePossum)
* minor #3427 ConfigurationResolver: fix @return annotation (Slamdunk)

Changelog for v2.2.13
---------------------

* bug #3281 SelfAccessorFixer - stop modifying traits (kubawerlos)
* minor #3195 Add self-update command test (julienfalque)
* minor #3292 PHPUnit - set memory limit (veewee)
* minor #3306 Token - better input validation (keradus)

Changelog for v2.2.12
---------------------

* bug #3173 SimplifiedNullReturnFixer - handle nullable return types (Slamdunk)
* bug #3272 PhpdocTrimFixer - unicode support (SpacePossum)

Changelog for v2.2.11
---------------------

* bug #3225 PhpdocTrimFixer - Fix handling of lines without leading asterisk (julienfalque)
* bug #3262 ToolInfo - support installation by branch as well (keradus)
* bug #3266 PhpUnitConstructFixer - multiple asserts bug (kubawerlos)
* minor #3239 Improve contributing guide and issue template (julienfalque)
* minor #3246 Make ToolInfo methods non-static (julienfalque)
* minor #3250 Travis: fail early, spare resources, save the Earth (Slamdunk, keradus)
* minor #3251 Create Title for config file docs section (IanEdington)
* minor #3254 AutoReview/FixerFactoryTest::testFixersPriority: verbose assertion message (Slamdunk)

Changelog for v2.2.10
---------------------

* bug #3199 TokensAnalyzer - getClassyElements (SpacePossum)
* bug #3208 BracesFixer - Fix for instantiation in control structures (julienfalque, SpacePossum)
* bug #3215 BinaryOperatorSpacesFixer - Fix spaces around multiple exception catching (ntzm)
* bug #3216 AbstractLinesBeforeNamespaceFixer - add min. and max. option, not only single target count (SpacePossum)
* bug #3217 TokenizerLinter - fix lack of linting when code is cached (SpacePossum, keradus)
* minor #3200 Skip slow test when Xdebug is loaded (julienfalque)
* minor #3219 Normalise references to GitHub in docs (ntzm)
* minor #3226 Remove unused imports (ntzm)
* minor #3231 Fix typos (ntzm)
* minor #3234 Simplify Cache\Signature::equals (ntzm)
* minor #3237 UnconfigurableFixer - use only LF (keradus)
* minor #3238 AbstractFixerTest - fix @cover annotation (keradus)

Changelog for v2.2.9
--------------------

* bug #3062 BraceClassInstantiationTransformer - Fix instantiation inside method call braces case (julienfalque, keradus)
* bug #3083 SingleBlankLineBeforeNamespaceFixer - Fix handling namespace right after opening tag (mlocati)
* bug #3109 SwitchCaseSemicolonToColonFixer - Fix bug with nested constructs (SpacePossum)
* bug #3123 Cache - File permissions (SpacePossum)
* bug #3172 IndentationTypeFixer - do not touch whitespace that is not indentation (SpacePossum)
* bug #3176 NoMultilineWhitespaceBeforeSemicolonsFixer - SpaceAfterSemicolonFixer - priority fix (SpacePossum)
* bug #3193 TokensAnalyzer::getClassyElements - sort result before returning (SpacePossum)
* bug #3196 SelfUpdateCommand - fix exit status when can't determine newest version (julienfalque)
* minor #3107 ConfigurationResolver - improve error message when rule is not found (SpacePossum)
* minor #3113 Add WordMatcher (keradus)
* minor #3133 Unify Reporter tests (keradus)
* minor #3134 Allow Symfony 4 (keradus, garak)
* minor #3136 PHPUnit - call hooks from parent class as well (keradus)
* minor #3145 misc - Typo (localheinz)
* minor #3150 Fix CircleCI (julienfalque)
* minor #3151 Update gitattributes to ignore next file (keradus)
* minor #3156 Update php-coveralls (keradus)
* minor #3166 README - add link to new gitter channel. (SpacePossum)
* minor #3174 Update UPGRADE.md (vitek-rostislav)
* minor #3180 Fix usage of static variables (kubawerlos)
* minor #3184 Code grooming - sort content of arrays (keradus)
* minor #3191 Travis - add nightly build to allow_failures due to Travis issues (keradus)
* minor #3197 DX groom CS (keradus)

Changelog for v2.2.8
--------------------

* bug #3052 Fix false positive warning about paths overridden by provided as command arguments (kubawerlos)
* bug #3058 IsNullFixer - fix whitespace handling (roukmoute)
* bug #3072 IsNullFixer - fix non_yoda_style edge case (keradus)
* bug #3088 Drop dedicated Phar stub (keradus)
* bug #3100 NativeFunctionInvocationFixer - Fix test if previous token is already namespace separator (SpacePossum)
* bug #3104 DoctrineAnnotationIndentationFixer - Fix str_repeat() error (julienfalque)
* minor #3038 Support PHP 7.2 (SpacePossum, keradus)
* minor #3064 Fix couple of typos (KKSzymanowski)
* minor #3078 ConfigurationResolver - hide context while including config file (keradus)
* minor #3080 Direct function call instead of by string (kubawerlos)
* minor #3085 CiIntegrationTest - skip when no git is available (keradus)
* minor #3087 phar-stub.php - allow PHP 7.2 (keradus)

Changelog for v2.2.7
--------------------

* bug #3002 Bugfix braces (mnabialek)
* bug #3010 Fix handling of Github releases (julienfalque, keradus)
* bug #3015 Fix exception arguments (julienfalque)
* bug #3016 Verify phar file (keradus)
* bug #3021 Risky rules cleanup (kubawerlos)
* bug #3023 RandomApiMigrationFixer - "rand();" to "random_int(0, getrandmax());" fixing (SpacePossum)
* bug #3024 ConfigurationResolver - Handle empty "rules" value (SpacePossum, keradus)
* bug #3031 IndentationTypeFixer - fix handling tabs in indented comments (keradus)
* minor #2999 Notice when paths from config file are overridden by command arguments (julienfalque, keradus)
* minor #3007 Add PHP 7.2 to Travis build matrix (Jean85)
* minor #3009 CiIntegrationTest - run local (SpacePossum)
* minor #3013 Adjust phpunit configuration (localheinz)
* minor #3017 Fix: Risky tests (localheinz)
* minor #3018 Fix: Make sure that data providers are named correctly (localheinz, keradus)
* minor #3032 .php_cs.dist - handling UnexpectedValueException (keradus)
* minor #3034 Follow newest CS (keradus)
* minor #3036 Drop not existing Standalone group from PHPUnit configuration and duplicated internal tags (keradus)
* minor #3042 Update gitter address (keradus)

Changelog for v2.2.6
--------------------

* bug #2925 Improve CI integration suggestion (julienfalque)
* bug #2928 TokensAnalyzer::getClassyElements - Anonymous class support (SpacePossum)
* bug #2931 Psr0Fixer, Psr4Fixer - ignore "new class" syntax (dg, keradus)
* bug #2934 Config - fix handling rule without value (keradus, SpacePossum)
* bug #2939 NoUnusedImportsFixer - Fix extra blank line (julienfalque)
* bug #2941 PHP 7.2 - Group imports with trailing comma support (SpacePossum, julienfalque)
* bug #2987 Fix incorrect indentation of comments in `braces` fixer (rob006)
* minor #2927 WhiteSpaceConfig - update message copy and more strict tests (SpacePossum, keradus)
* minor #2930 Trigger website build (keradus)
* minor #2932 Integrate CircleCI (keradus, aidantwoods)
* minor #2933 ProcessLinterTest - Ensure Windows test only runs on Windows, add a Mac test execution (aidantwoods)
* minor #2935 special handling of fabbot.io service if it's using too old PHP CS Fixer version (keradus)
* minor #2937 Travis: execute 5.3 job on precise (keradus)
* minor #2938 Tests fix configuration of project (SpacePossum, keradus)
* minor #2943 FunctionToConstantFixer - test with diff. arguments than fixable (SpacePossum)
* minor #2946 Detect extra old installations (keradus)
* minor #2947 Test suggested CI integration (keradus)
* minor #2951 AccessibleObject - remove most of usage (keradus)
* minor #2969 Shrink down AccessibleObject usage (keradus)
* minor #2982 TrailingCommaInMultilineArrayFixer - simplify isMultilineArray condition (TomasVotruba)

Changelog for v2.2.5
--------------------

* bug #2807 NoUselessElseFixer - Fix detection of conditional block (SpacePossum)
* bug #2809 Phar release - fix readme generation (SpacePossum, keradus)
* bug #2827 MethodArgumentSpaceFixer - Always remove trailing spaces (julienfalque)
* bug #2835 SelfAccessorFixer - class property fix (mnabialek)
* bug #2848 PhpdocIndentFixer - fix edge case with inline phpdoc (keradus)
* bug #2849 BracesFixer - Fix indentation issues with comments (julienfalque)
* bug #2851 Tokens - ensureWhitespaceAtIndex (GrahamCampbell, SpacePossum)
* bug #2854 NoLeadingImportSlashFixer - Removing leading slash from import even when in global space (kubawerlos)
* bug #2858 Support generic types (keradus)
* bug #2869 Fix handling required configuration (keradus)
* bug #2881 NoUnusedImportsFixer - Bug when trying to insert empty token (GrahamCampbell, keradus)
* bug #2882 DocBlock\Annotation - Fix parsing of collections with multiple key types (julienfalque)
* bug #2886 NoSpacesInsideParenthesisFixer - Do not remove whitespace if next token is comment (SpacePossum)
* bug #2888 SingleImportPerStatementFixer - Add support for function and const (SpacePossum)
* bug #2901 Add missing files to archive files (keradus)
* bug #2914 HeredocToNowdocFixer - works with CRLF line ending (dg)
* bug #2920 RuleSet - Update deprecated configuration of fixers (SpacePossum, keradus)
* minor #1531 Update docs for few generic types (keradus)
* minor #2793 COOKBOOK-FIXERS.md - update to current version, fix links (keradus)
* minor #2812 ProcessLinter - compatibility with Symfony 3.3 (keradus)
* minor #2816 Tokenizer - better docs and validation (keradus)
* minor #2817 Tokenizer - use future-compatible interface (keradus)
* minor #2819 Fix benchmark (keradus)
* minor #2824 code grooming (keradus)
* minor #2826 Exceptions - provide utests (localheinz)
* minor #2828 Enhancement: Reference phpunit.xsd from phpunit.xml.dist (localheinz)
* minor #2830 Differs - add tests (localheinz)
* minor #2832 Fix: Use all the columns (localheinz)
* minor #2833 Doctrine\Annotation\Token - provide utests (localheinz)
* minor #2839 Use PHP 7.2 polyfill instead of xml one (keradus)
* minor #2842 Move null to first position in PHPDoc types (julienfalque)
* minor #2850 ReadmeCommandTest - Prevent diff output (julienfalque)
* minor #2859 Fixed typo and dead code removal (GrahamCampbell)
* minor #2863 FileSpecificCodeSample - add tests (localheinz)
* minor #2864 WhitespacesAwareFixerInterface clean up (Slamdunk)
* minor #2865 AutoReview\FixerTest - test configuration samples (SpacePossum, keradus)
* minor #2867 VersionSpecification - Fix copy-paste typo (SpacePossum)
* minor #2874 LineTest - fix typo (keradus)
* minor #2875 HelpCommand - recursive layout fix (SpacePossum)
* minor #2883 DescribeCommand - Show which sample uses the default configuration  (SpacePossum)
* minor #2887 Housekeeping - Strict whitespace checks (SpacePossum)
* minor #2895 ProjectCodeTest - check that classes in no-tests exception exist (keradus)
* minor #2896 Move testing related classes from src to tests (keradus)
* minor #2904 Reapply CS (keradus)
* minor #2910 PhpdocAnnotationWithoutDotFixer - Restrict lowercasing (oschwald)
* minor #2913 Tests - tweaks (SpacePossum, keradus)
* minor #2916 FixerFactory - drop return in sortFixers(), never used (TomasVotruba)

Changelog for v2.2.4
--------------------

* bug #2682 DoctrineAnnotationIndentationFixer - fix handling nested annotations (edhgoose, julienfalque)
* bug #2700 Fix Doctrine Annotation end detection (julienfalque)
* bug #2715 OrderedImportsFixer - handle indented groups (pilgerone)
* bug #2732 HeaderCommentFixer - fix handling blank lines (s7b4)
* bug #2745 Fix Doctrine Annotation newlines (julienfalque)
* bug #2752 FixCommand - fix typo in warning message (mnapoli)
* bug #2757 GeckoPHPUnit is not dev dependency (keradus)
* bug #2759 Update gitattributes (SpacePossum)
* bug #2763 Fix describe command with PSR-0 fixer (julienfalque)
* bug #2768 Tokens::ensureWhitespaceAtIndex - clean up comment check, add check for T_OPEN (SpacePossum)
* bug #2783 Tokens::ensureWhitespaceAtIndex - Fix handling line endings (SpacePossum)
* minor #2663 Use colors for keywords in commands output (julienfalque, keradus)
* minor #2706 Update README (SpacePossum)
* minor #2714 README.rst - fix wrong value in example (mleko)
* minor #2721 Update phpstorm article link to a fresh blog post (valeryan)
* minor #2727 PHPUnit - use speedtrap (keradus)
* minor #2728 SelfUpdateCommand - verify that it's possible to replace current file (keradus)
* minor #2729 DescribeCommand - add decorated output test (julienfalque)
* minor #2731 BracesFixer - properly pass config in utest dataProvider (keradus)
* minor #2738 Upgrade tests to use new, namespaced PHPUnit TestCase class (keradus)
* minor #2743 Fixing example and description for GeneralPhpdocAnnotationRemoveFixer (kubawerlos)
* minor #2744 AbstractDoctrineAnnotationFixerTestCase - split fixers test cases (julienfalque)
* minor #2755 Fix compatibility with PHPUnit 5.4.x (keradus)
* minor #2758 Readme - improve CI integration guidelines (keradus)
* minor #2769 Psr0Fixer - remove duplicated example (julienfalque)
* minor #2775 NoExtraConsecutiveBlankLinesFixer - remove duplicate code sample. (SpacePossum)
* minor #2778 AutoReview - watch that code samples are unique (keradus)
* minor #2787 Add warnings about missing dom ext and require json ext (keradus)
* minor #2792 Use composer-require-checker (keradus)
* minor #2796 Update .gitattributes (SpacePossum)
* minor #2800 PhpdocTypesFixerTest - Fix typo in covers annotation (SpacePossum)

Changelog for v2.2.3
--------------------

* bug #2724 Revert #2554 Add short diff. output format (keradus)

Changelog for v2.2.2
--------------------

Warning, this release breaks BC due to introduction of:

* minor #2554 Add short diff. output format (SpacePossum, keradus)

That PR was reverted in v2.2.3, which should be used instead of v2.2.2.

* bug #2545 RuleSet - fix rule set subtraction (SpacePossum)
* bug #2686 Commands readme and describe - fix rare casing when not displaying some possible options of configuration (keradus)
* bug #2711 FixCommand - fix diff optional value handling (keradus)
* minor #2688 AppVeyor - Remove github oauth (keradus)
* minor #2703 Clean ups - No mixed annotations (SpacePossum)
* minor #2704 Create PHP70Migration:risky ruleset (keradus)
* minor #2707 Deprecate other than "yes" or "no" for input options (SpacePossum)
* minor #2709 code grooming (keradus)
* minor #2710 Travis - run more rules on TASK_SCA (keradus)

Changelog for v2.2.1
--------------------

* bug #2621 Tokenizer - fix edge cases with empty code, registered found tokens and code hash (SpacePossum, keradus)
* bug #2674 SemicolonAfterInstructionFixer - Fix case where block ends with an opening curly brace (ntzm)
* bug #2675 ProcessOutputTest - update tests to pass on newest Symfony components under Windows (keradus)
* minor #2651 Fix UPGRADE.md table syntax so it works in GitHub (ntzm, keradus)
* minor #2665 Travis - Improve trailing spaces detection (julienfalque)
* minor #2666 TransformersTest - move test to auto-review group (keradus)
* minor #2668 add covers annotation (keradus)
* minor #2669 TokensTest - grooming (SpacePossum)
* minor #2670 AbstractFixer: use applyFix instead of fix (Slamdunk)
* minor #2677 README: Correct progressbar option support (Laurens St�tzel)

Changelog for v2.2.0
--------------------

* bug #2640 NoExtraConsecutiveBlankLinesFixer - Fix single indent characters not working (ntzm)
* feature #2220 Doctrine annotation fixers (julienfalque)
* feature #2431 MethodArgumentSpaceFixer: allow to retain multiple spaces after comma (Slamdunk)
* feature #2459 BracesFixer - Add option for keeping opening brackets on the same line (jtojnar, SpacePossum)
* feature #2486 Add FunctionToConstantFixer (SpacePossum, keradus)
* feature #2505 FunctionDeclarationFixer - Make space after anonymous function configurable (jtojnar, keradus)
* feature #2509 FullOpeningTagFixer - Ensure opening PHP tag is lowercase (jtojnar)
* feature #2532 FixCommand - add stop-on-violation option (keradus)
* feature #2591 Improve process output (julienfalque)
* feature #2603 Add InvisibleSymbols Fixer (ivan1986, keradus)
* feature #2642 Add MagicConstantCasingFixer (ntzm)
* feature #2657 PhpdocToCommentFixer - Allow phpdoc for language constructs (ceeram, SpacePossum)
* minor #2500 Configuration resolver (julienfalque, SpacePossum, keradus)
* minor #2566 Show more details on errors and exceptions. (SpacePossum, julienfalque)
* minor #2597 HHVM - bump required version to 3.18 (keradus)
* minor #2606 FixCommand - fix missing comment close tag (keradus)
* minor #2623 OrderedClassElementsFixer - remove dead code (SpacePossum)
* minor #2625 Update Symfony and Symfony:risky rulesets (keradus)
* minor #2626 TernaryToNullCoalescingFixer - adjust ruleset membership and description (keradus)
* minor #2635 ProjectCodeTest - watch that all classes have dedicated tests (keradus)
* minor #2647 DescribeCommandTest - remove deprecated code usage (julienfalque)
* minor #2648 Move non-code covering tests to AutoReview subnamespace (keradus)
* minor #2652 NoSpacesAroundOffsetFixerTest - fix deprecation (keradus)
* minor #2656 Code grooming (keradus)
* minor #2659 Travis - speed up preparation for phar building (keradus)
* minor #2660 Fixed typo in suggest for ext-mbstring (pascal-hofmann)
* minor #2661 NonPrintableCharacterFixer - include into Symfony:risky ruleset (keradus)

Changelog for v2.1.3
--------------------

* bug #2358 Cache - Deal with signature encoding (keradus, GrahamCampbell)
* bug #2475 Add shorthand array destructing support (SpacePossum, keradus)
* bug #2595 NoUnusedImportsFixer - Fix import usage detection with properties (julienfalque)
* bug #2605 PhpdocAddMissingParamAnnotationFixer, PhpdocOrderFixer - fix priority issue (SpacePossum)
* bug #2607 Fixers - better comments handling (SpacePossum)
* bug #2612 BracesFixer - Fix early bracket close for do-while loop inside an if without brackets (felixgomez)
* bug #2614 Ensure that '*Fixer::fix()' won't crash when running on non-candidate collection (keradus)
* bug #2630 HeaderCommentFixer - Fix trailing whitespace not removed after <?php (julienfalque)
* bug #2637 ToolInfo - use static dir check for composer discovery (Slamdunk)
* bug #2639 SemicolonAfterInstructionFixer - Handle alternative syntax (SpacePossum)
* bug #2645 HHVM: handle T_HH_ERROR (keradus)
* bug #2653 IsNullFixer - fix edge case (localheinz, kalessil)
* bug #2654 PhpdocAddMissingParamAnnotationFixer - handle one-line docblocks (keradus)
* minor #2594 Travis - generate coverage report at 7.1 and clean up build matrix (keradus)
* minor #2613 HeaderCommentFixer - add missing case for exception raising (keradus)
* minor #2615 Add DescribeCommand test (julienfalque)
* minor #2616 Exclude more tests in phar version (keradus)
* minor #2618 Update README.rst (mhitza)
* minor #2620 Finder - Remove `*.twig` as default (SpacePossum)
* minor #2641 Cookbook - remove information about levels (keradus)
* minor #2644 DescribeCommandTest - fix test execution on decorated console (keradus)
* minor #2655 AppVeyor - Cache Composer Installation (julienfalque)

Changelog for v2.1.2
--------------------

* bug #2580 NoSpacesAfterFunctionNameFixer - Fix after dynamic call (SpacePossum, keradus)
* bug #2586 NoUnusedImportsFixerTest - handle FQCN import (keradus)
* bug #2587 NoClosingTagFixerTest - handle file without operations (keradus, SpacePossum)
* minor #2552 Initial compatibility with PHP 7.2-DEV (keradus)
* minor #2582 Improve AppVeyor and Travis CI build time (julienfalque)
* minor #2584 NoUnreachableDefaultArgumentValueFixer - fix typo (chadburrus)
* minor #2593 PhpUnitFqcnAnnotationFixer - move test to proper namespace (keradus)
* minor #2596 AppVeyor - update PHP versions (keradus)

Changelog for v2.1.1
--------------------

* bug #2547 NoUnneededControlParenthesesFixer - Handle T_COALESCE in clone (keksa)
* bug #2557 BracesFixer - Better comments handling (SpacePossum)
* bug #2558 require symfony/polyfill-xml (SpacePossum)
* bug #2560 PhpdocNoAliasTagFixer - Fix circular replacements detection (julienfalque)
* bug #2567 Filename with spaces usage (jaymecd)
* bug #2572 NoUnreachableDefaultArgumentValueFixer - Mark as risky (SpacePossum)
* minor #2533 AppVeyor - adjust phpunit version (keradus)
* minor #2535 Make .gitignore entries more specific (julienfalque)
* minor #2541 README.rst - provide download link for latest version (keradus)
* minor #2562 Add schema.json (keradus)
* minor #2563 Add deprecation notices tests (julienfalque)
* minor #2564 Add rules configuration by passing json encode config by CLI (SpacePossum)
* minor #2569 Make symfony/phpunit-bridge a dev dependency only (julienfalque)
* minor #2574 Add xml.xsd (keradus)

Changelog for v2.1.0
--------------------

* feature #2124 Add TernaryToNullCoalescingFixer (Slamdunk, SpacePossum)
* feature #2280 Configurable OrderedImportsFixer (DarkaOnLine)
* feature #2351 Enhancement: Allow to configure return_type_declaration rule (localheinz)
* feature #2359 Add PhpdocNoUselessInheritdocFixer (SpacePossum, keradus)
* feature #2414 Add PhpdocReturnSelfReferenceFixer (SpacePossum)
* feature #2415 Add IsNullFixer (kalessil, keradus)
* feature #2421 BracesFixer - Add allow_single_line_closure configuration (keradus)
* feature #2461 PhpdocNoUselessInheritdocFixer - support multiline docblock (keradus)
* feature #2462 Add NativeFunctionInvocationFixer (localheinz, keradus, Slamdunk)
* feature #2478 DeclareEqualNormalizeFixer - Add config option (SpacePossum)
* feature #2494 FixCommand - Support rules with params (ptcong, keradus)
* minor #2452 Provide rules definitions (keradus)
* minor #2460 RuleSet - extend Symfony (keradus)
* minor #2483 DX: AbstractIntegrationTestCase does not use IntegrationCase::shouldCheckPriority, logic is now automated and method is now deprecated (keradus)
* minor #2488 IsNullFixer - Fix bug when calling without params (SpacePossum)
* minor #2519 remove trailing whitespace (keradus)

Changelog for v2.0.1
--------------------

* bug #2357 Better handling of file name that is the same in multiple finder paths (keradus)
* bug #2373 FunctionDeclarationFixer - Fix static anonymous functions (SpacePossum)
* bug #2377 PhpdocSeparationFixer - Ignore incorrect PHPDoc (SpacePossum, keradus)
* bug #2388 PhpdocAlignFixer - unicode characters support (SpacePossum)
* bug #2399 HashToSlashCommentFixer - Fix edge cases (SpacePossum)
* bug #2403 ClassDefinitionFixer - Anonymous classes format by PSR12 (SpacePossum)
* bug #2408 SingleClassElementPerStatementFixer, PhpdocSeparationFixer - add missing WhitespacesAwareFixerInterface interface (keradus)
* bug #2425 ClassKeywordRemoveFixer - Fix handling leading backslash and comments (SpacePossum)
* bug #2430 PhpdocAlignFixer - Fix alignment of variadic params. (SpacePossum)
* bug #2437 NoWhitespaceInBlankLineFixer - Fix more cases (SpacePossum)
* bug #2444 MbStrFunctionsFixer - handle return reference in method declaration (SpacePossum)
* bug #2449 PhpdocAlignFixer - don't crash poorly formatted phpdoc (GrahamCampbell)
* bug #2477 BracesFixer - Do not remove white space inside declare statement (SpacePossum)
* bug #2481 Fix priorities between declare_strict_types and blank_line_after_opening_tag (juliendufresne, keradus)
* bug #2507 NoClosingTagFixer - Do not insert semicolon in comment (SpacePossum)
* minor #2347 UPGRADE.md - Fix multi-row description (drAlberT, keradus)
* minor #2352 Corrected method visibility (GrahamCampbell)
* minor #2353 Fix: Typos (localheinz)
* minor #2354 Enhancement: Allow to specify minimum and maximum PHP versions for code samples (localheinz)
* minor #2356 Fixed spelling on "blank line" (GrahamCampbell)
* minor #2361 ConfigurationResolver - Reject unknown rules (localheinz)
* minor #2368 clean ups (SpacePossum, localheinz)
* minor #2380 DescribeCommand - filter code samples and output note when none can be demonstrated (localheinz)
* minor #2381 Tests - Do not use annotations for asserting exceptions (localheinz, keradus)
* minor #2382 Consistently provide a default configuration field (localheinz)
* minor #2383 update .php_cs.dist configuration (keradus)
* minor #2386 PHP7.1 Integration test - Add features added in PHP7.1. (SpacePossum)
* minor #2392 FixCommandHelp - fix typo (keradus)
* minor #2393 Remove overcomplete tests (SpacePossum)
* minor #2394 Update .gitattributes (SpacePossum)
* minor #2395 NoEmptyCommentFixer - Fix typo (fritz-c)
* minor #2396 MethodArgumentSpaceFixer - scope down endpoint (SpacePossum)
* minor #2397 RuleSet - Check risky (SpacePossum, keradus)
* minor #2400 Add Fixer descriptions (SpacePossum)
* minor #2401 Fix UPGRADE.md (issei-m)
* minor #2405 Transformers - Must be final (SpacePossum)
* minor #2406 ProtectedToPrivateFixer - Use backticks for visibility in description (localheinz)
* minor #2407 Add tests for not abusing interfaces (keradus)
* minor #2410 DX: Keep packages sorted (localheinz)
* minor #2412 Enhancement: Add more descriptions (localheinz)
* minor #2413 Update Symfony ruleset (fabpot)
* minor #2419 README.rst - use double backticks for code pieces in rule descriptions (keradus)
* minor #2422 BracesFixer - cleanup code after introducing CT::T_FUNCTION_IMPORT (keradus)
* minor #2426 .php_cs.dist - update local CS config (keradus)
* minor #2428 SCA with Php Inspections (EA Extended) (kalessil)
* minor #2433 AbstractFixerTestCase - give all the details available during catch (Slamdunk)
* minor #2434 COOKBOOK-FIXERS.md - Replace reference to outdated class with current (greg0ire)
* minor #2436 MethodArgumentSpaceFixer - Remove duplicate class name (greg0ire)
* minor #2441 IndentationTypeFixer - Fix description and upgrade guide (SpacePossum)
* minor #2443 AppVeyor - update configuration (keradus)
* minor #2447 .php_cs.dist - update local CS config (keradus)
* minor #2452 Provide rules definitions (keradus)
* minor #2455 NoMultilineWhitespaceAroundDoubleArrowFixer - Add missing priority test (SpacePossum)
* minor #2466 Provide rules definitions (keradus)
* minor #2470 README.rst - explain the usage of "--path-mode" parameter (kalimatas)
* minor #2474 Housekeeping (SpacePossum)
* minor #2487 UPGRADE.md - Fix typo (SpacePossum)
* minor #2493 FixCommand - Output warning message when both config and rules options are passed (SpacePossum)
* minor #2496 DX: Travis - check for trailing spaces (keradus)
* minor #2499 FileSpecificCodeSample - Specify class name relative to root namespace (localheinz, keradus)
* minor #2506 SCA (SpacePossum)
* minor #2515 Fix code indentation (keradus)
* minor #2521 SCA trailing spces check - output lines with trailing white space (SpacePossum)
* minor #2522 Fix docs and small code issues (keradus)

Changelog for v2.0.0
--------------------

* bug #1001 MethodArgumentSpaceFixer - no need for multiple executions (keradus)
* bug #1006 NewWithBracesFixer - fix by adding BraceClassInstantiationTransformer (sstok)
* bug #1077 ConfigInterface - add missing methods (localheinz)
* bug #1103 added missing keyword token (gharlan)
* bug #1107 Added ImportTransformer (gharlan)
* bug #1157 Prevent token collection corruption by fixers (keradus, stof)
* bug #1256 Do not write the fixed output twice (SpacePossum)
* bug #1405 Linter - fix ignoring input parameter for constructor (keradus)
* bug #1414 Linter - fix escaping the php binary (GrahamCampbell, keradus)
* bug #1606 Fixer - remove duplicate file_get_contents call (gharlan)
* bug #1629 Fix linting test cases (gharlan)
* bug #1800 ConfigurationResolver - Fix resolving intersection path (keradus)
* bug #1809 NoMultilineWhitespaceBeforeSemicolonsFixer - Semicolon should not be moved into comment (SpacePossum)
* bug #1838 BracesFixer - Removes line break (SpacePossum)
* bug #1847 Runner - always cache files, not only when something is changed (gharlan)
* bug #1852 ConfigurationResolver - disallow empty rule name (keradus)
* bug #1855 FixCommand - fix passing NullLinter to Runner (keradus)
* bug #1926 NoUselessElseFixer - fix wrong if handling (SpacePossum)
* bug #1946 NoEmptyCommentFixer - Only remove complete empty comment blocks (SpacePossum)
* bug #1965 AbstractPsrAutoloadingFixer - fix edge case of halting compiler for PHP 5.3 (keradus)
* bug #1974 composer.json - fix dependencies for PHP 5.3.6 (keradus)
* bug #2025 NoShortEchoTagFixer - adjust isCandidate check for hhvm (keradus)
* bug #2039 NoExtraConsecutiveBlankLinesFixer - Fix curly brace open false positive (SpacePossum)
* bug #2044 SingleClassElementPerStatementFixer - fix array handling (keradus)
* bug #2063 ConfigurationResolver - passing non-existing path is ignored (keradus)
* bug #2236 .gitattributes - fix ignoring tests during export (keradus)
* bug #2241 XmlReporter - fix used getter (keradus)
* bug #2283 FixCommand - Fix resolving format option (SpacePossum, keradus)
* bug #2287 NoExtraConsecutiveBlankLinesFixer - fix bug that removes empty line already after scope end (keradus)
* bug #2290 FixCommand - fix progress (keradus)
* bug #2292 GeneralPhpdocAnnotation*Fixer::configure - add missing return (keradus)
* bug #2305 ProcessLinter - fix running under phpdbg (keradus)
* feature #1076 Enhancement: Allow to specify cache file (localheinz, keradus)
* feature #1088 JoinFunctionFixer -> AliasFunctionsFixer (kalessil)
* feature #1275 Added PhpdocInlineTagFixer (SpacePossum, keradus)
* feature #1292 Added MethodSeparationFixer (SpacePossum)
* feature #1383 Introduce rules and sets (keradus)
* feature #1416 Mark fixers as risky (keradus)
* feature #1440 Made AbstractFixerTestCase and AbstractIntegrationTestCase public (keradus)
* feature #1489 Added Psr4Fixer (GrahamCampbell)
* feature #1497 ExtraEmptyLinesFixer - allow to remove empty blank lines after configured tags (SpacePossum)
* feature #1529 Added PhpdocPropertyFixer, refactored Tag and Annotation (GrahamCampbell)
* feature #1628 Added OrderedClassElementsFixer (gharlan)
* feature #1742 path argument is used to create an intersection with existing finder (keradus, gharlan)
* feature #1779 Added GeneralPhpdocAnnotationRemoveFixer, GeneralPhpdocAnnotationRenameFixer (keradus)
* feature #1811 Added NoSpacesInsideOffsetFixer (phansys)
* feature #1819 Added DirConstantFixer, ModernizeTypesCastingFixer, RandomApiMigrationFixer (kalessil, SpacePossum, keradus)
* feature #1825 Added junit format (ekho)
* feature #1862 FixerFactory - Do not allow conflicting fixers (SpacePossum)
* feature #1888 Cache refactoring, better cache handling in dry-run mode (localheinz)
* feature #1889 Added SingleClassElementPerStatementFixer (phansys, SpacePossum)
* feature #1903 FixCommand - allow to pass multiple path argument (keradus)
* feature #1913 Introduce path-mode CLI option (keradus)
* feature #1949 Added DeclareStrictTypesFixer, introduce options for HeaderCommentFixer (Seldaek, SpacePossum, keradus)
* feature #1955 Introduce CT_ARRAY_INDEX_CURLY_BRACE_OPEN and CT_ARRAY_INDEX_CURLY_BRACE_CLOSE (keradus)
* feature #1958 Added NormalizeIndexBraceFixer (keradus)
* feature #2069 Add semicolon after instruction fixer (SpacePossum)
* feature #2089 Add `no_spaces_around_offset` fixer (phansys)
* feature #2179 BinaryOperatorSpacesFixer - add (un)align configuration options (SpacePossum)
* feature #2192 Add PowToExponentiationFixer (SpacePossum, keradus)
* feature #2207 Added ReturnTypeDeclarationFixer (keradus)
* feature #2213 VisibilityRequiredFixer - Add support for class const visibility added in PHP7.1. (SpacePossum)
* feature #2221 Add support for user-defined whitespaces (keradus)
* feature #2244 Config cleanup (keradus, SpacePossum)
* feature #2247 PhpdocAnnotationWithoutDotFixer - support more cases (keradus)
* feature #2289 Add PhpdocAddMissingParamAnnotationFixer (keradus)
* feature #2331 Add DescribeCommand (keradus, SpacePossum)
* feature #2332 New colours of diff on console (keradus)
* feature #829 add support for .php_cs.dist file (keradus)
* feature #998 MethodArgumentSpaceFixer - enhance, now only one space after comma (trilopin, keradus)
* minor #1007 Simplify Transformers (keradus)
* minor #1050 Make Config's setDir() fluent like the rest of methods (gonzaloserrano)
* minor #1062 Added NamespaceOperatorTransformer (gharlan)
* minor #1078 Exit status should be 0 if there are no errors (gharlan)
* minor #1101 CS: fix project itself (localheinz)
* minor #1102 Enhancement: List errors occurred before, during and after fixing (localheinz)
* minor #1105 Token::isStructureAlternativeEnd - remove unused method (keradus)
* minor #1106 readme grooming (SpacePossum, keradus)
* minor #1115 Fixer - simplify flow (keradus)
* minor #1118 Process output refactor (SpacePossum)
* minor #1132 Linter - public methods should be first (keradus)
* minor #1134 Token::isWhitespace - simplify interface (keradus)
* minor #1140 FixerInterface - check if fixer should be applied by isCandidate method (keradus)
* minor #1146 Linter - detect executable (keradus)
* minor #1156 deleted old ConfigurationResolver class (keradus)
* minor #1160 Grammar fix to README (Falkirks)
* minor #1174 DefaultFinder - boost performance by not filtering when files array is empty (keradus)
* minor #1179 Exit with non-zero if invalid files were detected prior to fixing (localheinz)
* minor #1186 Finder - do not search for .xml and .yml files (keradus)
* minor #1206 BracesFixer::getClassyTokens - remove duplicated method (keradus)
* minor #1222 Made fixers final (GrahamCampbell)
* minor #1229 Tokens - Fix PHPDoc (SpacePossum)
* minor #1241 More details on exceptions. (SpacePossum)
* minor #1263 Made internal classes final (GrahamCampbell)
* minor #1272 Readme - Add spaces around PHP-CS-Fixer headers (Soullivaneuh)
* minor #1283 Error - Fixed type phpdoc (GrahamCampbell)
* minor #1284 Token - Fix PHPDoc (SpacePossum)
* minor #1314 Added missing internal annotations (keradus)
* minor #1329 Psr0Fixer - move to contrib level (gharlan)
* minor #1340 Clean ups (SpacePossum)
* minor #1341 Linter - throw exception when write fails (SpacePossum)
* minor #1348 Linter - Prefer error output when throwing a linting exception (GrahamCampbell)
* minor #1350 Add "phpt" as a valid extension (henriquemoody)
* minor #1376 Add time and memory to XML report (junichi11)
* minor #1387 Made all test classes final (keradus)
* minor #1388 Made all tests internal (keradus)
* minor #1390 Added ProjectCodeTest that tests if all classes inside tests are internal and final or abstract (keradus)
* minor #1391 Fixer::getLevelAsString is no longer static (keradus)
* minor #1392 Add report to XML report as the root node (junichi11)
* minor #1394 Stop mixing level from config file and fixers from CLI arg when one of fixers has dash (keradus)
* minor #1426 MethodSeparationFixer - Fix spacing around comments (SpacePossum, keradus)
* minor #1432 Fixer check on factory (Soullivaneuh)
* minor #1434 Add Test\AccessibleObject class (keradus)
* minor #1442 FixerFactory - disallow to register multiple fixers with same name (keradus)
* minor #1477 rename PhpdocShortDescriptionFixer into PhpdocSummaryFixer (keradus)
* minor #1481 Fix running the tests (keradus)
* minor #1482 move AbstractTransformerTestBase class outside Tests dir (keradus)
* minor #1530 Added missing internal annotation (GrahamCampbell)
* minor #1534 Clean ups (SpacePossum)
* minor #1536 Typo fix (fabpot)
* minor #1555 Fixed indentation in composer.json (GrahamCampbell)
* minor #1558 [2.0] Cleanup the tags property in the abstract phpdoc types fixer (GrahamCampbell)
* minor #1567 PrintToEchoFixer - add to symfony rule set (gharlan)
* minor #1607 performance improvement (gharlan)
* minor #1621 Switch to PSR-4 (keradus)
* minor #1631 Configuration exceptions exception cases on master. (SpacePossum)
* minor #1646 Remove non-default Config/Finder classes (keradus)
* minor #1648 Fixer - avoid extra calls to getFileRelativePathname (GrahamCampbell)
* minor #1649 Consider the php version when caching (GrahamCampbell)
* minor #1652 Rename namespace "Symfony\CS" to "PhpCsFixer" (gharlan)
* minor #1666 new Runner, ProcessOutputInterface, DifferInterface and ResultInterface (keradus)
* minor #1674 Config - add addCustomFixers method (PedroTroller)
* minor #1677 Enhance tests (keradus)
* minor #1695 Rename Fixers (keradus)
* minor #1702 Upgrade guide (keradus)
* minor #1707 ExtraEmptyLinesFixer - fix configure docs (keradus)
* minor #1712 NoExtraConsecutiveBlankLinesFixer - Remove blankline after curly brace open (SpacePossum)
* minor #1718 CLI: rename --config-file argument (keradus)
* minor #1722 Renamed not_operators_with_space to not_operator_with_space (GrahamCampbell)
* minor #1728 PhpdocNoSimplifiedNullReturnFixer - rename back to PhpdocNoEmptyReturnFixer (keradus)
* minor #1729 Renamed whitespacy_lines to no_whitespace_in_blank_lines (GrahamCampbell)
* minor #1731 FixCommand - value for config option is required (keradus)
* minor #1732 move fixer classes from level subdirs to thematic subdirs (gharlan, keradus)
* minor #1733 ConfigurationResolver - look for .php_cs file in cwd as well (keradus)
* minor #1737 RuleSet/FixerFactory - sort arrays content (keradus)
* minor #1751 FixerInterface::configure - method should always override configuration, not patch it (keradus)
* minor #1752 Remove unused code (keradus)
* minor #1756 Finder - clean up code (keradus)
* minor #1757 Psr0Fixer - change way of configuring the fixer (keradus)
* minor #1762 Remove ConfigInterface::getDir, ConfigInterface::setDir, Finder::setDir and whole FinderInterface (keradus)
* minor #1764 Remove ConfigAwareInterface (keradus)
* minor #1780 AbstractFixer - throw error on configuring non-configurable Fixer (keradus)
* minor #1782 rename fixers (gharlan)
* minor #1815 NoSpacesInsideParenthesisFixer - simplify implementation (keradus)
* minor #1821 Ensure that PhpUnitDedicateAssertFixer runs after NoAliasFunctionsFixer, clean up NoEmptyCommentFixer (SpacePossum)
* minor #1824 Reporting extracted to separate classes (ekho, keradus, SpacePossum)
* minor #1826 Fixer - remove measuring fixing time per file (keradus)
* minor #1843 FileFilterIterator - add missing import (GrahamCampbell)
* minor #1845 FileCacheManager - Allow linting to determine the cache state too (GrahamCampbell)
* minor #1846 FileFilterIterator - Corrected an iterator typehint (GrahamCampbell)
* minor #1848 DocBlock - Remove some old unused phpdoc tags (GrahamCampbell)
* minor #1856 NoDuplicateSemicolonsFixer - Remove overcomplete fixer (SpacePossum)
* minor #1861 Fix: Offset should be Offset (localheinz)
* minor #1867 Print non-report output to stdErr (SpacePossum, keradus)
* minor #1873 Enhancement: Show path to cache file if it exists (localheinz)
* minor #1875 renamed Composer package (fabpot)
* minor #1882 Runner - Handle throwables too (GrahamCampbell)
* minor #1886 PhpdocScalarFixer - Fix lowercase str to string too (GrahamCampbell)
* minor #1940 README.rst - update CI example (keradus)
* minor #1947 SCA, CS, add more tests (SpacePossum, keradus)
* minor #1954 tests - stop using deprecated method (sebastianbergmann)
* minor #1962 TextDiffTest - tests should not produce cache file (keradus)
* minor #1973 Introduce fast PHP7 based linter (keradus)
* minor #1999 Runner - No need to determine relative file name twice (localheinz)
* minor #2002 FileCacheManagerTest - Adjust name of test and variable (localheinz)
* minor #2010 NoExtraConsecutiveBlankLinesFixer - SF rule set, add 'extra' (SpacePossum)
* minor #2013 no_whitespace_in_blank_lines -> no_whitespace_in_blank_line (SpacePossum)
* minor #2024 AbstractFixerTestCase - check if there is no duplicated Token instance inside Tokens collection (keradus)
* minor #2031 COOKBOOK-FIXERS.md - update calling doTest method (keradus)
* minor #2032 code grooming (keradus)
* minor #2068 Code grooming (keradus)
* minor #2073 DeclareStrictTypesFixer - Remove fix CS fix logic from fixer. (SpacePossum)
* minor #2088 TokenizerLintingResult - expose line number of parsing error (keradus)
* minor #2093 Tokens - add block type BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE (SpacePossum)
* minor #2095 Transformers - add required PHP version (keradus)
* minor #2096 Introduce CT for PHP7 (keradus)
* minor #2119 Create @Symfony:risky ruleset (keradus)
* minor #2163 ClassKeywordRemoveFixerTest - Fix tests (SpacePossum)
* minor #2180 FixCommand - don't refer to renamed rules (keradus)
* minor #2181 Disallow to disable linter (keradus)
* minor #2194 semicolon_after_instruction,no_unneeded_control_parentheses prio issue (SpacePossum)
* minor #2199 make fixers less risky (SpacePossum)
* minor #2206 Add PHP70Migration ruleset (keradus)
* minor #2217 SelfUpdateCommand - Print version of update fixer (SpacePossum)
* minor #2223 update integration test format (keradus)
* minor #2227 Stop polluting global namespace with CT (keradus)
* minor #2237 DX: extend integration tests for PSR2 and Symfony rulesets (keradus)
* minor #2240 Make some objects immutable (keradus)
* minor #2251 ProtectedToPrivateFixer - fix priority, fix comments with new fixer names (SpacePossum)
* minor #2252 ClassDefinitionFixer - Set configuration of the fixer in the RuleSet of SF. (SpacePossum)
* minor #2257 extend Symfony_whitespaces itest (keradus)
* minor #2258 README.rst - indicate configurable rules (keradus)
* minor #2267 RuleSet - validate set (keradus)
* minor #2268 Use strict parameters for PHP functions (keradus)
* minor #2273 fixed typo (fabpot)
* minor #2274 ShortArraySyntaxFixer/LongArraySyntaxFixer - Merge conflicting fixers (SpacePossum)
* minor #2275 Clean ups (SpacePossum)
* minor #2278 Concat*Fixer - unify concat fixers (SpacePossum, keradus)
* minor #2279 Use Prophecy (keradus)
* minor #2284 Code grooming (SpacePossum)
* minor #2285 IntegrationCase is now aware about RuleSet but not Fixers (keradus, SpacePossum)
* minor #2286 Phpdoc*Fixer - unify rename fixers (SpacePossum, keradus)
* minor #2288 FixerInterface::configure(null) reset fixer to use default configuration (keradus)
* minor #2291 Make fixers ready to use directly after creation (keradus)
* minor #2295 Code grooming (keradus)
* minor #2296 ProjectCodeTest - make test part of regular testsuite, not standalone one (keradus)
* minor #2298 ConfigurationResolver - grooming (SpacePossum)
* minor #2300 Simplify rule set (SpacePossum, keradus)
* minor #2306 DeclareStrictTypesFixer - do not move tokens (SpacePossum)
* minor #2312 RuleSet - sort rules (localheinz)
* minor #2313 DX: provide doctyping for tests (keradus)
* minor #2317 Add utests (keradus)
* minor #2318 *TestCase - Reduce visibility of setUp() (localheinz)
* minor #2319 Code grooming (keradus)
* minor #2322 DX: use whitemessy aware assertion (keradus)
* minor #2324 `Echo|Print*Fixer` - unify printing fixers (SpacePossum, keradus)
* minor #2337 Normalize rule naming (keradus)
* minor #2338 Drop hacks for unsupported HHVM (keradus)
* minor #2339 Add some Fixer descriptions (SpacePossum, keradus)
* minor #2343 PowToExponentiationFixer - allow to run on 5.6.0 as well (keradus)
* minor #767 Add @internal tag (keradus)
* minor #807 Tokens::isMethodNameIsMagic - remove unused method (keradus)
* minor #809 Split Tokens into Tokens and TokensAnalyzer (keradus)
* minor #844 Renamed phpdoc_params to phpdoc_align (GrahamCampbell)
* minor #854 Change default level to PSR2 (keradus)
* minor #873 Config - using cache by default (keradus)
* minor #902 change FixerInterface (keradus)
* minor #911 remove Token::$line (keradus)
* minor #914 All Transformer classes should be named with Transformer as suffix (keradus)
* minor #915 add UseTransformer (keradus)
* minor #916 add ArraySquareBraceTransformer (keradus)
* minor #917 clean up Transformer tests (keradus)
* minor #919 CurlyBraceTransformer - one transformer to handle all curly braces transformations (keradus)
* minor #928 remove Token::getLine (keradus)
* minor #929 add WhitespacyCommentTransformer (keradus)
* minor #937 fix docs/typehinting in few classes (keradus)
* minor #958 FileCacheManager - remove code for BC support (keradus)
* minor #979 Improve Tokens::clearEmptyTokens performance (keradus)
* minor #981 Tokens - code grooming (keradus)
* minor #988 Fixers - no need to search for tokens of given kind in extra loop (keradus)
* minor #989 No need for loop in Token::equals (keradus)

Changelog for v1.13.3
---------------------

* minor #3042 Update gitter address (keradus)

Changelog for v1.13.2
---------------------

* minor #2946 Detect extra old installations (keradus)

Changelog for v1.13.1
---------------------

* minor #2342 Application - adjust test to not depend on symfony/console version (keradus)
* minor #2344 AppVeyor: enforce PHP version (keradus)

Changelog for v1.13.0
---------------------

* bug #2303 ClassDefinitionFixer - Anonymous classes fixing (SpacePossum)
* feature #2208 Added fixer for PHPUnit's @expectedException annotation (ro0NL)
* feature #2249 Added ProtectedToPrivateFixer (Slamdunk, SpacePossum)
* feature #2264 SelfUpdateCommand - Do not update to next major version by default (SpacePossum)
* feature #2328 ClassDefinitionFixer - Anonymous classes format by PSR12 (SpacePossum)
* feature #2333 PhpUnitFqcnAnnotationFixer - support more annotations (keradus)
* minor #2256 EmptyReturnFixer - it's now risky fixer due to null vs void (keradus)
* minor #2281 Add issue template (SpacePossum)
* minor #2307 Update .editorconfig (SpacePossum)
* minor #2310 CI: update AppVeyor to use newest PHP, silence the composer (keradus)
* minor #2315 Token - Deprecate getLine() (SpacePossum)
* minor #2320 Clear up status code on 1.x (SpacePossum)

Changelog for v1.12.4
---------------------

* bug #2235 OrderedImportsFixer - PHP 7 group imports support (SpacePossum)
* minor #2276 Tokens cleanup (keradus)
* minor #2277 Remove trailing spaces (keradus)
* minor #2294 Improve Travis configuration (keradus)
* minor #2297 Use phpdbg instead of xdebug (keradus)
* minor #2299 Travis: proper xdebug disabling (keradus)
* minor #2301 Travis: update platform adjusting (keradus)

Changelog for v1.12.3
---------------------

* bug #2155 ClassDefinitionFixer - overhaul (SpacePossum)
* bug #2187 MultipleUseFixer - Fix handling comments (SpacePossum)
* bug #2209 LinefeedFixer - Fix in a safe way (SpacePossum)
* bug #2228 NoEmptyLinesAfterPhpdocs, SingleBlankLineBeforeNamespace - Fix priority (SpacePossum)
* bug #2230 FunctionDeclarationFixer - Fix T_USE case (SpacePossum)
* bug #2232 Add a test for style of variable declaration : var (daiglej)
* bug #2246 Fix itest requirements (keradus)
* minor #2238 .gitattributes - specified line endings (keradus)
* minor #2239 IntegrationCase - no longer internal (keradus)

Changelog for v1.12.2
---------------------

* bug #2191 PhpdocToCommentFixer - fix false positive for docblock of variable (keradus)
* bug #2193 UnneededControlParenthesesFixer - Fix more return cases. (SpacePossum)
* bug #2198 FileCacheManager - fix exception message and undefined property (j0k3r)
* minor #2170 Add dollar sign prefix for consistency (bcremer)
* minor #2190 .travis.yml - improve Travis speed for tags (keradus)
* minor #2196 PhpdocTypesFixer - support iterable type (GrahamCampbell)
* minor #2197 Update cookbook and readme (g105b, SpacePossum)
* minor #2203 README.rst - change formatting (ro0NL)
* minor #2204 FixCommand - clean unused var (keradus)
* minor #2205 Add integration test for iterable type (keradus)

Changelog for v1.12.1
---------------------

* bug #2144 Remove temporary files not deleted by destructor on failure (adawolfa)
* bug #2150 SelfUpdateCommand: resolve symlink (julienfalque)
* bug #2162 Fix issue where an exception is thrown if the cache file exists but is empty. (ikari7789)
* bug #2164 OperatorsSpacesFixer - Do not unalign double arrow and equals operators (SpacePossum)
* bug #2167 Rewrite file removal (keradus)
* minor #2152 Code cleanup (keradus)
* minor #2154 ShutdownFileRemoval - Fixed file header (GrahamCampbell)

Changelog for v1.12.0
---------------------

* feature #1493 Added MethodArgumentDefaultValueFixer (lmanzke)
* feature #1495 BracesFixer - added support for declare (EspadaV8)
* feature #1518 Added ClassDefinitionFixer (SpacePossum)
* feature #1543 [PSR-2] Switch case space fixer (Soullivaneuh)
* feature #1577 Added SpacesAfterSemicolonFixer (SpacePossum)
* feature #1580 Added HeredocToNowdocFixer (gharlan)
* feature #1581 UnneededControlParenthesesFixer - add "break" and "continue" support (gharlan)
* feature #1610 HashToSlashCommentFixer - Add (SpacePossum)
* feature #1613 ScalarCastFixer - LowerCaseCastFixer - Add (SpacePossum)
* feature #1659 NativeFunctionCasingFixer - Add (SpacePossum)
* feature #1661 SwitchCaseSemicolonToColonFixer - Add (SpacePossum)
* feature #1662 Added CombineConsecutiveUnsetsFixer (SpacePossum)
* feature #1671 Added NoEmptyStatementFixer (SpacePossum)
* feature #1705 Added NoUselessReturnFixer (SpacePossum, keradus)
* feature #1735 Added NoTrailingWhitespaceInCommentFixer (keradus)
* feature #1750 Add PhpdocSingleLineVarSpacingFixer (SpacePossum)
* feature #1765 Added NoEmptyPhpdocFixer (SpacePossum)
* feature #1773 Add NoUselessElseFixer (gharlan, SpacePossum)
* feature #1786 Added NoEmptyCommentFixer (SpacePossum)
* feature #1792 Add PhpUnitDedicateAssertFixer. (SpacePossum)
* feature #1894 BracesFixer - correctly fix indents of anonymous functions/classes (gharlan)
* feature #1985 Added ClassKeywordRemoveFixer (Soullivaneuh)
* feature #2020 Added PhpdocAnnotationWithoutDotFixer (keradus)
* feature #2067 Added DeclareEqualNormalizeFixer (keradus)
* feature #2078 Added SilencedDeprecationErrorFixer (HeahDude)
* feature #2082 Added MbStrFunctionsFixer (Slamdunk)
* bug #1657 SwitchCaseSpaceFixer - Fix spacing between 'case' and semicolon (SpacePossum)
* bug #1684 SpacesAfterSemicolonFixer - fix loops handling (SpacePossum, keradus)
* bug #1700 Fixer - resolve import conflict (keradus)
* bug #1836 NoUselessReturnFixer - Do not remove return if last statement in short if statement (SpacePossum)
* bug #1879 HeredocToNowdocFixer - Handle space in heredoc token (SpacePossum)
* bug #1896 FixCommand - Fix escaping of diff output (SpacePossum)
* bug #2034 IncludeFixer - fix support for close tag (SpacePossum)
* bug #2040 PhpdocAnnotationWithoutDotFixer - fix crash on odd character (keradus)
* bug #2041 DefaultFinder should implement FinderInterface (keradus)
* bug #2050 PhpdocAnnotationWithoutDotFixer - handle ellipsis (keradus)
* bug #2051 NativeFunctionCasingFixer - call to constructor with default NS of class with name matching native function name fix (SpacePossum)
* minor #1538 Added possibility to lint tests (gharlan)
* minor #1569 Add sample to get a specific version of the fixer (Soullivaneuh)
* minor #1571 Enhance integration tests (keradus)
* minor #1578 Code grooming (keradus)
* minor #1583 Travis - update matrix (keradus)
* minor #1585 Code grooming - Improve utests code coverage (SpacePossum)
* minor #1586 Add configuration exception classes and exit codes (SpacePossum)
* minor #1594 Fix invalid PHP code samples in utests  (SpacePossum)
* minor #1597 MethodArgumentDefaultValueFixer - refactoring and fix closures with "use" clause (gharlan)
* minor #1600 Added more integration tests (SpacePossum, keradus)
* minor #1605 integration tests - swap EXPECT and INPUT (optional INPUT) (gharlan)
* minor #1608 Travis - change matrix order for faster results (gharlan)
* minor #1609 CONTRIBUTING.md - Don't rebase always on master (SpacePossum)
* minor #1616 IncludeFixer - fix and test more cases (SpacePossum)
* minor #1622 AbstractIntegratationTest - fix linting test cases (gharlan)
* minor #1624 fix invalid code in test cases (gharlan)
* minor #1625 Travis - switch to trusty (keradus)
* minor #1627 FixCommand - fix output (keradus)
* minor #1630 Pass along the exception code. (SpacePossum)
* minor #1632 Php Inspections (EA Extended): SCA for 1.12 (kalessil)
* minor #1633 Fix CS for project itself (keradus)
* minor #1634 Backport some minor changes from 2.x line (keradus)
* minor #1637 update PHP Coveralls (keradus)
* minor #1639 Revert "Travis - set dist to trusty" (keradus)
* minor #1641 AppVeyor/Travis - use GITHUB_OAUTH_TOKEN (keradus)
* minor #1642 AppVeyor - install dev deps as well (keradus)
* minor #1647 Deprecate non-default Configs and Finders (keradus)
* minor #1654 Split output to stderr and stdout (SpacePossum)
* minor #1660 update phpunit version (gharlan)
* minor #1663 DuplicateSemicolonFixer - Remove duplicate semicolons even if there are comments between those (SpacePossum)
* minor #1664 IncludeFixer - Add missing test case (SpacePossum)
* minor #1668 Code grooming (keradus)
* minor #1669 NativeFunctionCasingFixer - move to Symfony level (keradus)
* minor #1670 Backport Finder and Config classes from 2.x line (keradus)
* minor #1682 ElseifFixer - handle comments (SpacePossum)
* minor #1689 AbstractIntegrationTest - no need for single-char group and docs grooming (keradus)
* minor #1690 Integration tests - allow to not check priority, introduce IntegrationCase (keradus)
* minor #1701 Fixer - Renamed import alias (GrahamCampbell)
* minor #1708 Update composer.json requirements (keradus)
* minor #1734 Travis: Turn on linting (keradus)
* minor #1736 Integration tests - don't check priority for tests using short_tag fixer (keradus)
* minor #1739 NoTrailingWhitespaceInCommentFixer - move to PSR2 level (keradus)
* minor #1763 Deprecate ConfigInterface::getDir, ConfigInterface::setDir, Finder::setDir (keradus)
* minor #1777 NoTrailingWhitespaceInCommentFixer - fix parent class (keradus)
* minor #1816 PhpUnitDedicateAssertFixer - configuration is not required anymore (keradus)
* minor #1849 DocBlock - The category tag should be together with package (GrahamCampbell)
* minor #1870 Update README.rst (glensc)
* minor #1880 FixCommand - fix stdErr detection (SpacePossum)
* minor #1881 NoEmptyStatementFixer - handle anonymous classes correctly (gharlan)
* minor #1906 .php_cs - use no_useless_else rule (keradus)
* minor #1915 NoEmptyComment - move to Symfony level (SpacePossum)
* minor #1917 BracesFixer - fixed comment handling (gharlan)
* minor #1919 EmptyReturnFixer - move fixer outside of Symfony level (keradus)
* minor #2036 OrderedUseFixer - adjust tests (keradus)
* minor #2056 Travis - run nightly PHP (keradus)
* minor #2061 UnusedUseFixer and LineAfterNamespace - add new integration test (keradus)
* minor #2097 Add lambda tests for 7.0 and 7.1 (SpacePossum)
* minor #2111 .travis.yml - rename PHP 7.1 env (keradus)
* minor #2112 Fix 1.12 line (keradus)
* minor #2118 SilencedDeprecationErrorFixer - adjust level (keradus)
* minor #2132 composer.json - rename package name (keradus)
* minor #2133 Apply ordered_class_elements rule (keradus)
* minor #2138 composer.json - disallow to run on PHP 7.2+ (keradus)

Changelog for v1.11.8
---------------------

* bug #2143 ReadmeCommand - fix running command on phar file (keradus)
* minor #2129 Add .gitattributes to remove unneeded files (Slamdunk)
* minor #2141 Move phar building to PHP 5.6 job as newest box.phar is no longer working on 5.3 (keradus)

Changelog for v1.11.7
---------------------

* bug #2108 ShortArraySyntaxFixer, TernarySpacesFixer, UnalignEqualsFixer - fix priority bug (SpacePossum)
* bug #2092 ConcatWithoutSpacesFixer, OperatorsSpacesFixer - fix too many spaces, fix incorrect fixing of lines with comments (SpacePossum)

Changelog for v1.11.6
---------------------

* bug #2086 Braces - fix bug with comment in method prototype (keradus)
* bug #2077 SingleLineAfterImportsFixer - Do not remove lines between use cases (SpacePossum)
* bug #2079 TernarySpacesFixer - Remove multiple spaces (SpacePossum)
* bug #2087 Fixer - handle PHP7 Errors as well (keradus)
* bug #2072 LowercaseKeywordsFixer - handle CT_CLASS_CONSTANT (tgabi333)
* bug #2066 LineAfterNamespaceFixer - Handle close tag (SpacePossum)
* bug #2057 LineAfterNamespaceFixer - adding too much extra lines where namespace is last statement (keradus)
* bug #2059 OperatorsSpacesFixer - handle declare statement (keradus)
* bug #2060 UnusedUseFixer - fix handling whitespaces around removed import (keradus)
* minor #2071 ShortEchoTagFixer - allow to run tests on PHP 5.3 (keradus)

Changelog for v1.11.5
---------------------

* bug #2012 Properly build phar file for lowest supported PHP version (keradus)
* bug #2037 BracesFixer - add support for anonymous classes (keradus)
* bug #1989 Add support for PHP 7 namespaces (SpacePossum)
* bug #2019 Fixing newlines added after curly brace string index access (jaydiablo)
* bug #1840 [Bug] BracesFixer - Do add a line before close tag (SpacePossum)
* bug #1994 EchoToPrintFixer - Fix T_OPEN_TAG_WITH_ECHO on hhvm (keradus)
* bug #1970 Tokens - handle semi-reserved PHP 7 keywords (keradus)
* minor #2017 PHP7 integration tests (keradus)
* minor #1465 Bump supported HHVM version, improve ShortEchoTagFixer on HHVM (keradus)
* minor #1995 Rely on own phpunit, not one from CI service (keradus)

Changelog for v1.11.4
---------------------

* bug #1956 SelfUpdateCommand - don't update to non-stable version (keradus)
* bug #1963 Fix not wanted unneeded_control_parentheses fixer for clone (Soullivaneuh)
* bug #1960 Fix invalid test cases (keradus)
* bug #1939 BracesFixer - fix handling comment around control token (keradus)
* minor #1927 NewWithBracesFixer - remove invalid testcase (keradus)

Changelog for v1.11.3
---------------------

* bug #1868 NewWithBracesFixer - fix handling more neighbor tokens (keradus)
* bug #1893 BracesFixer - handle comments inside lambda function prototype (keradus)
* bug #1806 SelfAccessorFixer - skip anonymous classes (gharlan)
* bug #1813 BlanklineAfterOpenTagFixer, NoBlankLinesBeforeNamespaceFixer - fix priority (SpacePossum)
* minor #1807 Tokens - simplify isLambda() (gharlan)

Changelog for v1.11.2
---------------------

* bug #1776 EofEndingFixer - new line on end line comment is allowed (Slamdunk)
* bug #1775 FileCacheManager - ignore corrupted serialized data (keradus)
* bug #1769 FunctionDeclarationFixer - fix more cases (keradus)
* bug #1747 Fixer - Fix ordering of fixer when both level and custom fixers are used (SpacePossum)
* bug #1744 Fixer - fix rare situation when file was visited twice (keradus)
* bug #1710 LowercaseConstantFixer - Fix comment cases. (SpacePossum)
* bug #1711 FunctioncallSpaceFixer - do not touch function declarations. (SpacePossum)
* minor #1798 LintManager - meaningful tempnam (Slamdunk)
* minor #1759 UniqueFileIterator - performance improvement (GrahamCampbell)
* minor #1745 appveyor - fix build (keradus)

Changelog for v1.11.1
---------------------

* bug #1680 NewWithBracesFixer - End tags  (SpacePossum)
* bug #1685 EmptyReturnFixer - Make independent of LowercaseConstantsFixer (SpacePossum)
* bug #1640 IntegrationTest - fix directory separator (keradus)
* bug #1595 ShortTagFixer - fix priority (keradus)
* bug #1576 SpacesBeforeSemicolonFixer - do not remove space before semicolon if that space is after a semicolon (SpacePossum)
* bug #1570 UnneededControlParenthesesFixer - fix test samples (keradus)
* minor #1653 Update license year (gharlan)

Changelog for v1.11
-------------------

* feature #1550 Added UnneededControlParenthesesFixer (Soullivaneuh, keradus)
* feature #1532 Added ShortBoolCastFixer (SpacePossum)
* feature #1523 Added EchoToPrintFixer and PrintToEchoFixer (Soullivaneuh)
* feature #1552 Warn when running with xdebug extension (SpacePossum)
* feature #1484 Added ArrayElementNoSpaceBeforeCommaFixer and ArrayElementWhiteSpaceAfterCommaFixer (amarczuk)
* feature #1449 PhpUnitConstructFixer - Fix more use cases (SpacePossum)
* feature #1382 Added PhpdocTypesFixer (GrahamCampbell)
* feature #1384 Add integration tests (SpacePossum)
* feature #1349 Added FunctionTypehintSpaceFixer (keradus)
* minor #1562 Fix invalid PHP code samples in utests (SpacePossum)
* minor #1560 Fixed project name in xdebug warning (gharlan)
* minor #1545 Fix invalid PHP code samples in utests (SpacePossum)
* minor #1554 Alphabetically sort entries in .gitignore (GrahamCampbell)
* minor #1527 Refactor the way types work on annotations (GrahamCampbell)
* minor #1546 Update coding guide in cookbook (keradus)
* minor #1526 Support more annotations when fixing types in phpdoc (GrahamCampbell)
* minor #1535 clean ups (SpacePossum)
* minor #1510 Added Symfony 3.0 support (Ener-Getick)
* minor #1520 Code grooming (keradus)
* minor #1515 Support property, property-read and property-write tags (GrahamCampbell)
* minor #1488 Added more inline phpdoc tests (GrahamCampbell)
* minor #1496 Add docblock to AbstractFixerTestBase::makeTest (lmanzke)
* minor #1467 PhpdocShortDescriptionFixer - add support for Japanese sentence-ending characters (fritz-c)
* minor #1453 remove calling array_keys in foreach loops (keradus)
* minor #1448 Code grooming (keradus)
* minor #1437 Added import fixers integration test (GrahamCampbell)
* minor #1433 phpunit.xml.dist - disable gc (keradus)
* minor #1427 Change arounded to surrounded in README.rst (36degrees)
* minor #1420 AlignDoubleArrowFixer, AlignEqualsFixer - add integration tests (keradus)
* minor #1423 appveyor.yml - do not cache C:\tools, its internal forAppVeyor (keradus)
* minor #1400 appveyor.yml - add file (keradus)
* minor #1396 AbstractPhpdocTypesFixer - instance method should be called on instance (keradus)
* minor #1395 code grooming (keradus)
* minor #1393 boost .travis.yml file (keradus)
* minor #1372 Don't allow PHP 7 to fail (GrahamCampbell)
* minor #1332 PhpUnitConstructFixer - fix more functions (keradus)
* minor #1339 CONTRIBUTING.md - add link to PSR-5 (keradus)
* minor #1346 Core grooming (SpacePossum)
* minor #1328 Tokens: added typehint for Iterator elements (gharlan)

Changelog for v1.10.3
---------------------

* bug #1559 WhitespacyLinesFixer - fix bug cases (SpacePossum, keradus)
* bug #1541 Psr0Fixer - Ignore filenames that are a reserved keyword or predefined constant (SpacePossum)
* bug #1537 Psr0Fixer - ignore file without name or with name started by digit (keradus)
* bug #1516 FixCommand - fix wrong message for dry-run (SpacePossum)
* bug #1486 ExtraEmptyLinesFixer - Remove extra lines after comment lines too (SpacePossum)
* bug #1503 Psr0Fixer - fix case with comments lying around (GrahamCampbell)
* bug #1474 PhpdocToCommentFixer - fix not properly fixing for block right after namespace (GrahamCampbell)
* bug #1478 BracesFixer - do not remove empty lines after class opening (keradus)
* bug #1468 Add missing ConfigInterface::getHideProgress() (Eugene Leonovich, rybakit)
* bug #1466 Fix bad indent on align double arrow fixer (Soullivaneuh, keradus)
* bug #1479 Tokens - fix detection of short array (keradus)

Changelog for v1.10.2
---------------------

* bug #1461 PhpUnitConstructFixer - fix case when first argument is an expression (keradus)
* bug #1460 AlignDoubleArrowFixer - fix handling of nested arrays (Soullivaneuh, keradus)

Changelog for v1.10.1
---------------------

* bug #1424 Fixed the import fixer priorities (GrahamCampbell)
* bug #1444 OrderedUseFixer - fix next case (keradus)
* bug #1441 BracesFixer - fix next case (keradus)
* bug #1422 AlignDoubleArrowFixer - fix handling of nested array (SpacePossum)
* bug #1425 PhpdocInlineTagFixerTest - fix case when met invalid PHPDoc (keradus)
* bug #1419 AlignDoubleArrowFixer, AlignEqualsFixer - fix priorities (keradus)
* bug #1415 BlanklineAfterOpenTagFixer - Do not add a line break if there is one already. (SpacePossum)
* bug #1410 PhpdocIndentFixer - Fix for open tag (SpacePossum)
* bug #1401 PhpdocVarWithoutNameFixer - Fixed the var without name fixer for inline docs (keradus, GrahamCampbell)
* bug #1369 Fix not well-formed XML output (junichi11)
* bug #1356 Psr0Fixer - disallow run on StdinFileInfo (keradus)

Changelog for v1.10
-------------------

* feature #1306 Added LogicalNotOperatorsWithSuccessorSpaceFixer (phansys)
* feature #1286 Added PhpUnitConstructFixer (keradus)
* feature #1316 Added PhpdocInlineTagFixer (SpacePossum, keradus)
* feature #1303 Added LogicalNotOperatorsWithSpacesFixer (phansys)
* feature #1279 Added PhpUnitStrictFixer (keradus)
* feature #1267 SingleQuoteFixer fix more use cases (SpacePossum)
* minor #1319 PhpUnitConstructFixer - fix performance and add to local .php_cs (keradus)
* minor #1280 Fix non-utf characters in docs (keradus)
* minor #1274 Cookbook - No change auto-test note (Soullivaneuh)

Changelog for v1.9.3
--------------------

* bug #1327 DocBlock\Tag - keep the case of tags (GrahamCampbell)

Changelog for v1.9.2
--------------------

* bug #1313 AlignDoubleArrowFixer - fix aligning after UTF8 chars (keradus)
* bug #1296 PhpdocScalarFixer - fix property annotation too (GrahamCampbell)
* bug #1299 WhitespacyLinesFixer - spaces on next valid line must not be fixed (Slamdunk)

Changelog for v1.9.1
--------------------

* bug #1288 TrimArraySpacesFixer - fix moving first comment (keradus)
* bug #1287 PhpdocParamsFixer - now works on any indentation level (keradus)
* bug #1278 Travis - fix PHP7 build (keradus)
* bug #1277 WhitespacyLinesFixer - stop changing non-whitespacy tokens (SpacePossum, SamBurns-awin, keradus)
* bug #1224 TrailingSpacesFixer - stop changing non-whitespacy tokens (SpacePossum, SamBurns-awin, keradus)
* bug #1266 FunctionCallSpaceFixer - better detection of function call (funivan)
* bug #1255 make sure some phpdoc fixers are run in right order (SpacePossum)

Changelog for v1.9
------------------

* feature #1097 Added ShortEchoTagFixer (vinkla)
* minor #1238 Fixed error handler to respect current error_reporting (JanJakes)
* minor #1234 Add class to exception message, use sprintf for exceptions (SpacePossum)
* minor #1210 set custom error handler for application run (keradus)
* minor #1214 Tokens::isMonolithicPhp - enhance performance (keradus)
* minor #1207 Update code documentation (keradus)
* minor #1202 Update IDE tool urls (keradus)
* minor #1195 PreIncrementFixer - move to Symfony level (gharlan)

Changelog for v1.8.1
--------------------

* bug #1193 EofEndingFixer - do not add an empty line at EOF if the PHP tags have been closed (SpacePossum)
* bug #1209 PhpdocParamsFixer - fix corrupting following custom annotation (keradus)
* bug #1205 BracesFixer - fix missing indentation fixes for class level (keradus)
* bug #1204 Tag - fix treating complex tag as simple PhpDoc tag (keradus)
* bug #1198 Tokens - fixed unary/binary operator check for type-hinted reference arguments (gharlan)
* bug #1201 Php4ConstructorFixer - fix invalid handling of subnamespaces (gharlan)
* minor #1221 Add more tests (SpacePossum)
* minor #1216 Tokens - Add unit test for array detection (SpacePossum)

Changelog for v1.8
------------------

* feature #1168 Added UnalignEqualsFixer (keradus)
* feature #1167 Added UnalignDoubleArrowFixer (keradus)
* bug #1169 ToolInfo - Fix way to find script dir (sp-ian-monge)
* minor #1181 composer.json - Update description (SpacePossum)
* minor #1180 create Tokens::overrideAt method (keradus)

Changelog for v1.7.1
--------------------

* bug #1165 BracesFixer - fix bug when comment is a first statement in control structure without braces (keradus)

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
* bug #1048 MultilineArrayTrailingCommaFixer, SingleArrayNoTrailingCommaFixer - using heredoc inside array not causing to treat it as multiline array (keradus)
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
* minor #938 Psr0Fixer - remove unneeded assignment (keradus)
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
* bug #908 BracesFixer - fix invalid inserting brace for control structure without brace and lambda inside of it (keradus)
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
* minor #761 align => (keradus)
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
