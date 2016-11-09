<?php
namespace Classgen\Stub;

class Stub
{
    public function toLines()
    {
        return array();
    }

    /**
     * @param int $level
     * @return array
     */
    public function toIndentedLines($level = 1)
    {
        $newLines = array();

        foreach($this->toLines() as $line)
            $newLines[] = str_repeat('    ', $level) . $line;

        return $newLines;
    }

    public function toString()
    {
        return implode("\n", $this->toLines());
    }

    public function __toString()
    {
        return $this->toString();
    }
}