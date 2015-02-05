<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Matteo Beccati <matteo@beccati.com>
 */
class Php4ConstructorFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        $classes = array_keys($tokens->findGivenKind(T_CLASS));

        for ($i = 0; $i < count($classes); ++$i) {
            $index = $classes[$i];

            // is it inside a namespace?
            $nspIndex = $tokens->getPrevTokenOfKind($index, array(array(T_NAMESPACE, 'namespace')));
            if ($nspIndex) {
                $nspName = $tokens->getNextMeaningfulToken($nspIndex); // name
                $nspBrace = $tokens->getNextMeaningfulToken($nspName); // { or ;

                if ($tokens[$nspBrace]->equals(';')) {
                    // the class is inside a (non-block) namespace, no PHP4-code should be in there
                    break;
                } else {
                    $nspEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $nspBrace);
                    if ($index < $nspEnd) {
                        // the class is inside a block namespace, skip other classes that might be in it
                        for ($j = $i + 1; $j < count($classes); ++$j) {
                            if ($classes[$j] < $nspEnd) {
                                ++$i;
                            }
                        }
                        continue;
                    }
                }
            }

            $classNameIndex = $tokens->getNextMeaningfulToken($index);
            $className = $tokens[$classNameIndex]->getContent();
            $classStart = $tokens->getNextTokenOfKind($classNameIndex, array('{'));
            $classEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classStart);

            $this->fixConstructor($tokens, $className, $classStart, $classEnd);
            $this->fixParent($tokens, $classStart, $classEnd);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'php4_constructor';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Convert PHP4-style constructors to __construct. Warning! This could change code behavior.';
    }

    /**
     * Fix constructor within a class, if possible
     *
     * @param Tokens $tokens     the Tokens instance
     * @param string $className  the class name
     * @param int    $classStart the class start index
     * @param int    $classEnd   the class end index
     */
    private function fixConstructor(Tokens $tokens, $className, $classStart, $classEnd)
    {
        $p4 = $this->findFunction($tokens, $className, $classStart, $classEnd);

        if (null === $p4) {
            // no PHP4-constructor!
            return;
        }

        $p5 = $this->findFunction($tokens, '__construct', $classStart, $classEnd);

        if (null === $p5) {
            // no PHP5-constructor, we can rename the old one to __construct
            $tokens[$p4['nameIdx']]->setContent('__construct');

            return;
        }

        // does the PHP4-constructor only call $this->__construct($args, ...)?
        list($seq, $case) = $this->getWrapperMethodSequence($tokens, '__construct', $p4['startIdx'], $p4['bodyIdx']);
        if (null !== $tokens->findSequence($seq, $p4['bodyIdx'] - 1, $p4['endIdx'], $case)) {
            // good, delete it!
            for ($i = $p4['startIdx']; $i <= $p4['endIdx']; ++$i) {
                $tokens[$i]->clear();
            }

            return;
        }

        // does __construct only call the PHP4-constructor (with the same args)?
        list($seq, $case) = $this->getWrapperMethodSequence($tokens, $className, $p4['startIdx'], $p4['bodyIdx']);
        if (null !== $tokens->findSequence($seq, $p5['bodyIdx'] - 1, $p5['endIdx'], $case)) {
            // that was a weird choice, but we can safely delete it and...
            for ($i = $p5['startIdx']; $i <= $p5['endIdx']; ++$i) {
                $tokens[$i]->clear();
            }
            // rename the PHP4 one to __construct
            $tokens[$p4['nameIdx']]->setContent('__construct');
        }
    }

    /**
     * Fix calls to the parent constructor within a class
     *
     * @param Tokens $tokens     the Tokens instance
     * @param int    $classStart the class start index
     * @param int    $classEnd   the class end index
     */
    private function fixParent(Tokens $tokens, $classStart, $classEnd)
    {
        // check calls to the parent constructor
        foreach ($tokens->findGivenKind(T_EXTENDS) as $index => $token) {
            $parIndex = $tokens->getNextMeaningfulToken($index);
            $parentClass = $tokens[$parIndex]->getContent();

            // using parent::ParentClassName()
            $parentSeq = $tokens->findSequence(array(
                array(T_STRING, 'parent'),
                array(T_PAAMAYIM_NEKUDOTAYIM),
                array(T_STRING, $parentClass),
                '(',
            ), $classStart, $classEnd, array(false, true, false, true));

            if (null !== $parentSeq) {
                // we only need indexes
                $parentSeq = array_keys($parentSeq);

                // replace method name with __construct
                $tokens[$parentSeq[2]]->setContent('__construct');
            }

            // using $this->ParentClassName()
            $parentSeq = $tokens->findSequence(array(
                array(T_VARIABLE, '$this'),
                array(T_OBJECT_OPERATOR),
                array(T_STRING, $parentClass),
                '(',
            ), $classStart, $classEnd, array(true, true, false, true));

            if (null !== $parentSeq) {
                // we only need indexes
                $parentSeq = array_keys($parentSeq);

                // replace call with parent::__construct()
                $tokens[$parentSeq[0]] = new Token(array(
                    T_STRING,
                    'parent',
                    $tokens[$parentSeq[0]]->getLine(),
                ));
                $tokens[$parentSeq[1]] = new Token(array(
                    T_PAAMAYIM_NEKUDOTAYIM,
                    '::',
                    $tokens[$parentSeq[1]]->getLine(),
                ));
                $tokens[$parentSeq[2]]->setContent('__construct');
            }
        }
    }

    /**
     * Generate the sequence of tokens necessary for the body of a wrapper method that simply
     * calls $this->{$method}( [args...] ) with the same arguments as its own signature.
     *
     * @param Tokens $tokens   the Tokens instance
     * @param string $method   the wrapped method name
     * @param int    $startIdx function/method start index
     * @param int    $bodyIdx  function/method body index
     *
     * @return array an array containing the sequence and case sensitiveness [ 0 => $seq, 1 => $case ]
     */
    private function getWrapperMethodSequence(Tokens $tokens, $method, $startIdx, $bodyIdx)
    {
        // initialise sequence as { $this->{$method}(
        $seq = array(
            '{',
            array(T_VARIABLE, '$this'),
            array(T_OBJECT_OPERATOR),
            array(T_STRING, $method),
            '(',
        );
        $case = array(true, true, true, false, true);

        // parse method parameters, if any
        $index = $startIdx;
        while (true) {
            // find the next variable name
            $index = $tokens->getNextTokenOfKind($index, array(array(T_VARIABLE)));

            if (null === $index || $index >= $bodyIdx) {
                // we've reached the body already
                break;
            }

            // append a comma if it's not the first variable
            if (count($seq) > 5) {
                $seq[] = ',';
                $case[] = true;
            }

            // append variable name to the sequence
            $seq[] = array(T_VARIABLE, $tokens[$index]->getContent());
            $case[] = true;
        }

        // almost done, close the sequence with ); }
        $seq[] = ')';
        $case[] = true;
        $seq[] = ';';
        $case[] = true;
        $seq[] = '}';
        $case[] = true;

        return array($seq, $case);
    }

    /**
     * Find a function or method matching a given name within certain bounds.
     *
     * @param Tokens $tokens   the Tokens instance
     * @param string $name     the function/Method name
     * @param int    $startIdx the function/method start index
     * @param int    $bodyIdx  the function/method body index
     *
     * @return array|null {
     *                    The function/method data, if a match is found.
     *
     *      @var int    $nameIdx  the index of the function/method name
     *      @var int    $startIdx the index of the function/method start
     *      @var int    $endInx   the index of the function/method end
     *      @var string $bodyIdx  the index of the function/method body
     * }
     */
    private function findFunction(Tokens $tokens, $name, $startIdx, $endIdx)
    {
        $function = $tokens->findSequence(array(
            array(T_FUNCTION),
            array(T_STRING, $name),
        ), $startIdx, $endIdx, false);

        if (null !== $function) {
            // keep only the indexes
            $function = array_keys($function);

            // find previous block, ignoring method modifiers
            $prevBlock = $tokens->getPrevMeaningfulToken($function[0]);
            while ($tokens[$prevBlock]->isGivenKind(array(T_PUBLIC, T_PRIVATE, T_PROTECTED, T_ABSTRACT, T_STATIC))) {
                if ($tokens[$prevBlock]->equals(array(T_ABSTRACT))) {
                    // skip abstract methods
                    return;
                }
                $prevBlock = $tokens->getPrevMeaningfulToken($prevBlock);
            }

            // find method body start
            $bodyStart = $tokens->getNextTokenOfKind($function[1], array('{'));
            if (null === $bodyStart) {
                // no body
                return;
            }

            // and body end
            $funcEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $bodyStart);

            return array(
                'nameIdx' => $function[1],
                'startIdx' => $prevBlock + 1,
                'endIdx' => $funcEnd,
                'bodyIdx' => $bodyStart,
            );
        }
    }
}
