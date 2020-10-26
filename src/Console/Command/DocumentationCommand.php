<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Console\Command;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Documentation\DocumentationGenerator;
use PhpCsFixer\FixerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class DocumentationCommand extends Command
{
    protected static $defaultName = 'documentation';

    /**
     * @var DocumentationGenerator
     */
    private $generator;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->generator = new DocumentationGenerator();
    }

    protected function configure()
    {
        $this
            ->setAliases(['doc'])
            ->setDescription('Dumps the documentation of the project into its /doc directory.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        /** @var AbstractFixer[] $fixers */
        $fixers = $fixerFactory->getFixers();

        $paths = [
            '_index' => $this->generator->getFixersDocumentationIndexFilePath(),
        ];

        $filesystem = new Filesystem();

        foreach ($fixers as $fixer) {
            $class = \get_class($fixer);
            $paths[$class] = $path = $this->generator->getFixerDocumentationFilePath($fixer);

            $filesystem->dumpFile($path, $this->generator->generateFixerDocumentation($fixer));
        }

        /** @var SplFileInfo $file */
        foreach ((new Finder())->files()->in($this->generator->getFixersDocumentationDirectoryPath()) as $file) {
            $path = $file->getPathname();

            if (!\in_array($path, $paths, true)) {
                $filesystem->remove($path);
            }
        }

        if (false === @file_put_contents($paths['_index'], $this->generator->generateFixersDocumentationIndex($fixers))) {
            throw new \RuntimeException("Failed updating file {$paths['_index']}.");
        }

        return 0;
    }
}
