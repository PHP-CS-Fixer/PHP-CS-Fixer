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

namespace PhpCsFixer\Tests\Fixtures\Test\FileReaderTest;

/**
 * @author ntzm
 *
 * @internal
 *
 * @see \PhpCsFixer\Tests\FileReaderTest::testReadStdin
 * @see https://secure.php.net/manual/en/class.streamwrapper.php
 *
 * A stream wrapper class that pretends to be php://stdin, and returns
 * `<?php echo "foo";` on first read, then an empty string every subsequent read
 */
final class StdinFakeStream
{
    /**
     * @var resource
     */
    public $context;

    private static bool $hasReadContent = false;

    private string $content = '<?php echo "foo";';

    private bool $hasReadCurrentString = false;

    public function stream_open($path)
    {
        return 'php://stdin' === $path;
    }

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

    public function stream_eof()
    {
        return $this->hasReadCurrentString;
    }

    public function stream_stat()
    {
        return array();
    }
}
