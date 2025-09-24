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

namespace PhpCsFixer\Tests;

use org\bovigo\vfs\vfsStream;
use PhpCsFixer\FileReader;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @covers \PhpCsFixer\FileReader
 *
 * @author ntzm
 */
final class FileReaderTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        // testReadStdinCaches registers a stream wrapper for PHP so we can mock
        // php://stdin. Restore the original stream wrapper after this class so
        // we don't affect other tests running after it
        stream_wrapper_restore('php');
    }

    public function testCreateSingleton(): void
    {
        $instance = FileReader::createSingleton();

        self::assertSame($instance, FileReader::createSingleton());
    }

    public function testRead(): void
    {
        $fs = vfsStream::setup('root', null, [
            'foo.php' => '<?php echo "hi";',
        ]);

        $reader = new FileReader();

        self::assertSame('<?php echo "hi";', $reader->read($fs->url().'/foo.php'));
    }

    public function testReadStdinCaches(): void
    {
        $reader = new FileReader();

        $stdinStream = $this->createStdinStreamDouble();

        stream_wrapper_unregister('php');
        stream_wrapper_register('php', \get_class($stdinStream));

        self::assertSame('<?php echo "foo";', $reader->read('php://stdin'));
        self::assertSame('<?php echo "foo";', $reader->read('php://stdin'));
    }

    public function testThrowsExceptionOnFail(): void
    {
        $fs = vfsStream::setup();
        $nonExistentFilePath = $fs->url().'/non-existent.php';

        $reader = new FileReader();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('#^Failed to read content from "'.preg_quote($nonExistentFilePath, '#').'.*$#');

        $reader->read($nonExistentFilePath);
    }

    private function createStdinStreamDouble(): object
    {
        return new class {
            /**
             * @var resource
             */
            public $context;

            private static bool $hasReadContent = false;

            private string $content = '<?php echo "foo";';

            private bool $hasReadCurrentString = false;

            public function stream_open(string $path): bool
            {
                return 'php://stdin' === $path;
            }

            /**
             * @return false|string
             */
            public function stream_read()
            {
                if ($this->stream_eof()) {
                    return false;
                }

                $this->hasReadCurrentString = true;

                if (self::$hasReadContent) {
                    return '';
                }

                self::$hasReadContent = true;

                return $this->content;
            }

            public function stream_eof(): bool
            {
                return $this->hasReadCurrentString;
            }

            /**
             * @return array{}
             */
            public function stream_stat(): array
            {
                return [];
            }
        };
    }
}
