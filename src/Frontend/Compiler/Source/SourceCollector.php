<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Frontend\Compiler\Source;

/**
 * @internal
 */
class SourceCollector
{
    /**
     * @var SourceInterface[]
     */
    protected $sources = [];

    /**
     * @param string $file
     * @return $this
     */
    public function addFile(string $file, string $extensionId = null)
    {
        $this->sources[] = new FileSource($file, $extensionId);

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function addString(callable $callback)
    {
        $this->sources[] = new StringSource($callback);

        return $this;
    }

    /**
     * @return SourceInterface[]
     */
    public function getSources()
    {
        return $this->sources;
    }
}
