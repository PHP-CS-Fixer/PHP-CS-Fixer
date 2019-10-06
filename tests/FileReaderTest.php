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

namespace PhpCsFixer\Tests;

use org\bovigo\vfs\vfsStream;
use PhpCsFixer\FileReader;
use PhpCsFixer\Tests\Fixtures\Test\FileReaderTest\StdinFakeStream;

/**
 * @author ntzm
 *
 * @internal
 *
 * @covers \PhpCsFixer\FileReader
 */
final class FileReaderTest extends TestCase
{
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        // testReadStdinCaches registers a stream wrapper for php so we can mock
        // php://stdin. Restore the original stream wrapper after this class so
        // we don't affect other tests running after it
        stream_wrapper_restore('php');
    }

    public function testCreateSingleton()
    {
        $instance = FileReader::createSingleton();

        static::assertInstanceOf(\PhpCsFixer\FileReader::class, $instance);
        static::assertSame($instance, FileReader::createSingleton());
    }

    public function testRead()
    {
        $fs = vfsStream::setup('root', null, [
            'foo.php' => '<?php echo "hi";',
        ]);

        $reader = new FileReader();

        static::assertSame('<?php echo "hi";', $reader->read($fs->url().'/foo.php'));
    }

    public function testReadStdinCaches()
    {
        $reader = new FileReader();

        stream_wrapper_unregister('php');
        stream_wrapper_register('php', StdinFakeStream::class);

        static::assertSame('<?php echo "foo";', $reader->read('php://stdin'));
        static::assertSame('<?php echo "foo";', $reader->read('php://stdin'));
    }

    public function testThrowsExceptionOnFail()
    {
        $fs = vfsStream::setup();
        $nonExistentFilePath = $fs->url().'/non-existent.php';

        $reader = new FileReader();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Failed to read content from "'.$nonExistentFilePath.'". file_get_contents('.$nonExistentFilePath.'): failed to open stream: "org\bovigo\vfs\vfsStreamWrapper::stream_open" call failed');

        $reader->read($nonExistentFilePath);
    }
}
