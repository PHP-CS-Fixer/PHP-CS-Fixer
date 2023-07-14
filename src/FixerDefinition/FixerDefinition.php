<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\FixerDefinition;

use PhpCsFixer\Console\Application;
use PhpCsFixer\Utils;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FixerDefinition implements FixerDefinitionInterface
{
    /**
     * Fixers without description.
     * Should only shrink. Do not add anything here.
     */
    private const MISSING_DESCRIPTION_EXCEPTIONS = [
        \PhpCsFixer\Fixer\Alias\ArrayPushFixer::class => true,
        \PhpCsFixer\Fixer\Alias\EregToPregFixer::class => true,
        \PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer::class => true,
        \PhpCsFixer\Fixer\Alias\ModernizeStrposFixer::class => true,
        \PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer::class => true,
        \PhpCsFixer\Fixer\Alias\NoAliasLanguageConstructCallFixer::class => true,
        \PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer::class => true,
        \PhpCsFixer\Fixer\Alias\PowToExponentiationFixer::class => true,
        \PhpCsFixer\Fixer\Alias\RandomApiMigrationFixer::class => true,
        \PhpCsFixer\Fixer\Alias\SetTypeToCastFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer::class => true,
        \PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer::class => true,
        \PhpCsFixer\Fixer\Basic\BracesFixer::class => true,
        \PhpCsFixer\Fixer\Basic\CurlyBracesPositionFixer::class => true,
        \PhpCsFixer\Fixer\Basic\EncodingFixer::class => true,
        \PhpCsFixer\Fixer\Basic\NoMultipleStatementsPerLineFixer::class => true,
        \PhpCsFixer\Fixer\Basic\NoTrailingCommaInSinglelineFixer::class => true,
        \PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer::class => true,
        \PhpCsFixer\Fixer\Basic\OctalNotationFixer::class => true,
        \PhpCsFixer\Fixer\Basic\PsrAutoloadingFixer::class => true,
        \PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer::class => true,
        \PhpCsFixer\Fixer\Casing\ClassReferenceNameCasingFixer::class => true,
        \PhpCsFixer\Fixer\Casing\ConstantCaseFixer::class => true,
        \PhpCsFixer\Fixer\Casing\IntegerLiteralCaseFixer::class => true,
        \PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer::class => true,
        \PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer::class => true,
        \PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer::class => true,
        \PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer::class => true,
        \PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer::class => true,
        \PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer::class => true,
        \PhpCsFixer\Fixer\CastNotation\CastSpacesFixer::class => true,
        \PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer::class => true,
        \PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer::class => true,
        \PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer::class => true,
        \PhpCsFixer\Fixer\CastNotation\NoUnsetCastFixer::class => true,
        \PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\FinalInternalClassFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\OrderedInterfacesFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\OrderedTraitsFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\OrderedTypesFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\SelfStaticAccessorFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\SingleTraitInsertPerStatementFixer::class => true,
        \PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class => true,
        \PhpCsFixer\Fixer\ClassUsage\DateTimeImmutableFixer::class => true,
        \PhpCsFixer\Fixer\Comment\CommentToPhpdocFixer::class => true,
        \PhpCsFixer\Fixer\Comment\HeaderCommentFixer::class => true,
        \PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer::class => true,
        \PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer::class => true,
        \PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer::class => true,
        \PhpCsFixer\Fixer\Comment\SingleLineCommentSpacingFixer::class => true,
        \PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer::class => true,
        \PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\ControlStructureBracesFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\ControlStructureContinuationPositionFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\ElseifFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\EmptyLoopBodyFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\EmptyLoopConditionFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\IncludeFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\NoAlternativeSyntaxFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\SimplifiedIfReturnFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\SwitchContinueToBreakFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::class => true,
        \PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer::class => true,
        \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer::class => true,
        \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationBracesFixer::class => true,
        \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\CombineNestedDirnameFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\FopenFlagOrderFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\FopenFlagsFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\ImplodeCallFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\LambdaNotUsedImportFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\NoTrailingCommaInSinglelineFunctionCallFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\NoUnreachableDefaultArgumentValueFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\NoUselessSprintfFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\PhpdocToParamTypeFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\PhpdocToPropertyTypeFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\PhpdocToReturnTypeFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\RegularCallableCallFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\SingleLineThrowFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer::class => true,
        \PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class => true,
        \PhpCsFixer\Fixer\Import\FullyQualifiedStrictTypesFixer::class => true,
        \PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer::class => true,
        \PhpCsFixer\Fixer\Import\GroupImportFixer::class => true,
        \PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer::class => true,
        \PhpCsFixer\Fixer\Import\NoUnneededImportAliasFixer::class => true,
        \PhpCsFixer\Fixer\Import\NoUnusedImportsFixer::class => true,
        \PhpCsFixer\Fixer\Import\OrderedImportsFixer::class => true,
        \PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer::class => true,
        \PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\ClassKeywordRemoveFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\DeclareParenthesesFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\ErrorSuppressionFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\GetClassToClassKeywordFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\NoUnsetOnPropertyFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\NullableTypeDeclarationFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAfterConstructFixer::class => true,
        \PhpCsFixer\Fixer\LanguageConstruct\SingleSpaceAroundConstructFixer::class => true,
        \PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer::class => true,
        \PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer::class => true,
        \PhpCsFixer\Fixer\NamespaceNotation\BlankLinesBeforeNamespaceFixer::class => true,
        \PhpCsFixer\Fixer\NamespaceNotation\CleanNamespaceFixer::class => true,
        \PhpCsFixer\Fixer\NamespaceNotation\NoBlankLinesBeforeNamespaceFixer::class => true,
        \PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer::class => true,
        \PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer::class => true,
        \PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer::class => true,
        \PhpCsFixer\Fixer\Operator\AssignNullCoalescingToCoalesceEqualFixer::class => true,
        \PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class => true,
        \PhpCsFixer\Fixer\Operator\ConcatSpaceFixer::class => true,
        \PhpCsFixer\Fixer\Operator\IncrementStyleFixer::class => true,
        \PhpCsFixer\Fixer\Operator\LogicalOperatorsFixer::class => true,
        \PhpCsFixer\Fixer\Operator\NewWithBracesFixer::class => true,
        \PhpCsFixer\Fixer\Operator\NoSpaceAroundDoubleColonFixer::class => true,
        \PhpCsFixer\Fixer\Operator\NoUselessConcatOperatorFixer::class => true,
        \PhpCsFixer\Fixer\Operator\NoUselessNullsafeOperatorFixer::class => true,
        \PhpCsFixer\Fixer\Operator\NotOperatorWithSpaceFixer::class => true,
        \PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer::class => true,
        \PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer::class => true,
        \PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer::class => true,
        \PhpCsFixer\Fixer\Operator\StandardizeIncrementFixer::class => true,
        \PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer::class => true,
        \PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer::class => true,
        \PhpCsFixer\Fixer\Operator\TernaryToElvisOperatorFixer::class => true,
        \PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer::class => true,
        \PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer::class => true,
        \PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer::class => true,
        \PhpCsFixer\Fixer\PhpTag\EchoTagSyntaxFixer::class => true,
        \PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer::class => true,
        \PhpCsFixer\Fixer\PhpTag\LinebreakAfterOpeningTagFixer::class => true,
        \PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderNameFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitDataProviderStaticFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertInternalTypeFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitExpectationFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitInternalClassFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitMockFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitMockShortWillReturnFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitNoExpectationAnnotationFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitSetUpTearDownVisibilityFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitStrictFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer::class => true,
        \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestClassRequiresCoversFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocAnnotationWithoutDotFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagNormalizerFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocOrderByValueFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocParamOrderFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTagCasingFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTagTypeFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocToCommentFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocVarAnnotationCorrectOrderFixer::class => true,
        \PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer::class => true,
        \PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer::class => true,
        \PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer::class => true,
        \PhpCsFixer\Fixer\ReturnNotation\SimplifiedNullReturnFixer::class => true,
        \PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer::class => true,
        \PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer::class => true,
        \PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer::class => true,
        \PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer::class => true,
        \PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer::class => true,
        \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer::class => true,
        \PhpCsFixer\Fixer\Strict\StrictComparisonFixer::class => true,
        \PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer::class => true,
        \PhpCsFixer\Fixer\StringNotation\NoBinaryStringFixer::class => true,
        \PhpCsFixer\Fixer\StringNotation\NoTrailingWhitespaceInStringFixer::class => true,
        \PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class => true,
        \PhpCsFixer\Fixer\StringNotation\StringLengthToEmptyFixer::class => true,
        \PhpCsFixer\Fixer\StringNotation\StringLineEndingFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\BlankLineBetweenImportGroupsFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\HeredocIndentationFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\LineEndingFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\StatementIndentationFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer::class => true,
        \PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer::class => true,
    ];

    private string $summary;

    /**
     * @var list<CodeSampleInterface>
     */
    private array $codeSamples;

    /**
     * Description of Fixer and benefit of using it.
     */
    private ?string $description;

    /**
     * Description why Fixer is risky.
     */
    private ?string $riskyDescription;

    /**
     * @param list<CodeSampleInterface> $codeSamples      array of samples, where single sample is [code, configuration]
     * @param null|string               $riskyDescription null for non-risky fixer
     */
    public function __construct(
        string $summary,
        array $codeSamples,
        ?string $description = null,
        ?string $riskyDescription = null
    ) {
        $this->summary = $summary;
        $this->codeSamples = $codeSamples;
        $this->description = $description;
        $this->riskyDescription = $riskyDescription;

        $callingClass = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'];
        if (null === $this->description) {
            if (!isset(self::MISSING_DESCRIPTION_EXCEPTIONS[$callingClass])) {
                Utils::triggerDeprecation(new \InvalidArgumentException(sprintf(
                    'Not passing "description" parameter for "%s" (constructed in "%s") is deprecated and will not be allowed in version %d.0.',
                    __CLASS__,
                    $callingClass,
                    Application::getMajorVersion() + 1
                )));
            }
        } elseif (isset(self::MISSING_DESCRIPTION_EXCEPTIONS[$callingClass])) {
            throw new \LogicException(sprintf(
                'Remove "%s" from "%s::MISSING_DESCRIPTION_EXCEPTIONS", as "description" is present now.',
                $callingClass,
                __CLASS__
            ));
        }
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRiskyDescription(): ?string
    {
        return $this->riskyDescription;
    }

    public function getCodeSamples(): array
    {
        return $this->codeSamples;
    }
}
