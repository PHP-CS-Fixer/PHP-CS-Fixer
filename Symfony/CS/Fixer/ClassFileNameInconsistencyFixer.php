<?php

namespace Symfony\CS\Fixer;

use Symfony\CS\FixerInterface;

/**
 * This "fixer" only notifies if there is a suspicious inconsistency between the name of the file and
 * the name of the class. It doesn't try to automagically fix it.
 *
 *
 * @author Mark van der Velden <mark@dynom.nl>
 */
class ClassFileNameInconsistencyFixer implements FixerInterface
{
    /**
     * Fixes a file, or in this instance it only verifies if the class name matches the case of the file name
     *
     * @param \SplFileInfo $file    A \SplFileInfo instance
     * @param string       $content The file content
     *
     * @return string The fixed file content
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $matches = array();
        if (preg_match('@(?:class|interface|trait)(?:\s+)(\w+)@', $content, $matches) !== 1) {
            return $content;
        }


        if ( ! empty($matches[1])) {
            $className = $matches[1];
            $fileNameWithoutExtension = $file->getBasename('.'. $file->getExtension());

            // Does it match ?
            if ($className !== $fileNameWithoutExtension) {

                // It's generally the class-name that is correct, considering it leading.
                $expectedFileName = $className .'.'. $file->getExtension();
                $actualFileName = $file->getBasename();

                echo '! WARNING the class name "'. $className .'" doesn\'t match the file name "'. $actualFileName .'"'.
                    ', expecting the file to be named "'. $expectedFileName .'"';
            }
        }

        // Nothing to fix, returning the content
        return $content;
    }

    /**
     * Returns the level of CS standard.
     *
     * Can be one of self::PSR1_LEVEL, self::PSR2_LEVEL, or self::ALL_LEVEL
     */
    public function getLevel()
    {
        return self::ALL_LEVEL;
    }

    /**
     * Returns the priority of the fixer.
     *
     * The default priority is 0 and higher priorities are executed first.
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * Returns true if the file is supported by this fixer.
     *
     * @return Boolean true if the file is supported by this fixer, false otherwise
     */
    public function supports(\SplFileInfo $file)
    {
        return 'php' == pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    }

    /**
     * Returns the name of the fixer.
     *
     * The name must be all lowercase and without any spaces.
     *
     * @return string The name of the fixer
     */
    public function getName()
    {
        return 'class_file_name_inconsistency';
    }

    /**
     * Returns the description of the fixer.
     *
     * A short one-line description of what the fixer does.
     *
     * @return string The description of the fixer
     */
    public function getDescription()
    {
        return 'A simple check if the file name casing, match that of the class name. '.
            'A mistake developers on case-insensitive file systems might miss.';
    }
}
