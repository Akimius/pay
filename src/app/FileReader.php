<?php

declare(strict_types=1);

namespace App\app;

use Countable;
use LimitIterator;
use RuntimeException;
use SplFileObject;

final class FileReader extends LimitIterator implements Countable
{
    /**
     * The number of items to iterate. Must be -1 or greater. -1, the default, means no limit.
     */
    private const NO_LIMIT = -1;

    /**
     * Txt file
     *
     * @var SplFileObject
     */
    private SplFileObject $file;

    /**
     * @param string $filePath
     * @param int $offset The offset to start at. Must be zero or greater. "0" starts from the very first line, "1" skips the first line ...
     * @param int $limit The number of items to iterate. Must be -1 or greater. -1, the default, means no limit.
     */
    public function __construct(string $filePath, int $offset = 0, int $limit = self::NO_LIMIT)
    {
        $this->file = $this->getFile($filePath);
        $this->file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);

        parent::__construct($this->file, $offset, $limit);
    }

    /**
     * Checks the TXT file.
     *
     * @param string $filePath The path of the file.
     *
     * @return SplFileObject The SplFileObject instance.
     */
    private function getFile(string $filePath): SplFileObject
    {
        $path = realpath($filePath);

        if (false === $path || !is_readable($path)) {
            throw new RuntimeException(sprintf('The file [%s] does not exist or is not readable', $filePath));
        }

        return new SplFileObject($path);
    }

    /**
     * Return the amount of lines in the TXT file.
     *
     * @return int The amount of the lines.
     */
    public function count(): int
    {
        $currentPosition = $this->file->key();

        $totalLinesCount = iterator_count($this->file);
        $this->file->seek($currentPosition);

        return $totalLinesCount;
    }

    /**
     * Get line number of the iterated file
     *
     * @link https://php.net/manual/en/splfileobject.key.php
     * @return int the current line number.
     */
    public function getLineNumber(): int
    {
        return $this->file->key() + 1;
    }
}
