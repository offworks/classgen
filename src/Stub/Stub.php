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

    protected static function normalizeExportedArray($string)
    {
        $array = implode('', explode("\n", $string));

        return str_replace(array('array (  ', ',)'), array('array(', ')'), $array);
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