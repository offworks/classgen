<?php
namespace Classgen\Stub;

class BlockStub extends CodeStub
{
    protected $headerLine;

    protected $lines = array();

    protected $isContinued = false;

    public function __construct($header, $content = '')
    {
        parent::__construct($content);

        $this->headerLine = $header;
    }

    /**
     * Block header. will be less 1 indention.
     * @return string
     */
    public function getHeaderLine()
    {
        return $this->headerLine;
    }

    /**
     * Block body
     * @return array
     */
    public function getBodyLines()
    {
        return array_merge(array('{'), $this->lines, array('}'));
    }

    public function toIndentedLines($level = 1)
    {
        $lines = $this->toLines();

        $newLines = array();

        foreach($lines as $index => $line)
        {
            $lvl = $level;

            if($index <= 1 || ($index == count($lines) - 1))
            {
                $lvl = $level - 1;
            }

            if(is_object($line) && $line instanceof BlockStub)
            {
                $newLines[] = '';

                foreach($line->toIndentedLines($lvl + 1) as $line)
                    $newLines[] = $line;
            }
            else
            {
                $newLines[] = str_repeat('    ', $lvl) . $line;
            }
        }

        return $newLines;
    }

    /**
     * @return array
     */
    public function toLines()
    {
        return array_merge(array($this->getHeaderLine()), $this->getBodyLines());
    }

    /**
     * Set this block as a continuation
     * @return $this
     */
    public function setAsContinued()
    {
        $this->isContinued = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isContinued()
    {
        return $this->isContinued;
    }
}