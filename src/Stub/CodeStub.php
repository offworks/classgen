<?php
namespace Classgen\Stub;

class CodeStub extends Stub
{
    /** @var array|BlockStub[]  */
    protected $lines = array();

    public function __construct($content = '')
    {
        $this->lines = $content === '' ? array() : explode("\n", $content);
    }

    public static function createFromArgument($arg)
    {
        return new static(implode("\n", static::createLinesFromArgument($arg)));
    }

    public static function createFromClosure(\Closure $code)
    {
        return new static(implode("\n", static::createLinesFromClosure($code)));
    }

    public function replace($find, $replace)
    {
        $this->lines = explode("\n", str_replace($find, $replace, implode("\n", $this->lines)));

        return $this;
    }

    public function filter(\Closure $closure)
    {
        $this->lines = explode("\n", $closure(implode("\n", $this->lines)));

        return $this;
    }

    /**
     * Loop every line
     * @param \Closure $closure
     */
    public function each(\Closure $handler)
    {
        $lines = array();

        foreach($this->lines as $line)
            $lines[] = $handler($line);

        $this->lines = $lines;
    }

    /**
     * @param $arg
     * @return CodeStub
     */
    public function prepend($arg)
    {
        $lines = static::createLinesFromArgument($arg);

        $this->lines = array_merge($lines, array(''), $this->lines);

        return $this;
    }

    /**
     * @param $arg
     * @return self
     */
    public function append($arg)
    {
        $lines = static::createLinesFromArgument($arg);

        if(count($this->lines) == 0 && count($lines) == 1 && $lines[0] === '')
            return $this;

        $this->lines = $this->lines ? array_merge($this->lines, array(''), $lines) : $lines;

        return $this;
    }

    /**
     * Alias to append($arg)
     * @param mixed $arg
     * @return CodeStub
     */
    public function write($arg)
    {
        return $this->append($arg);
    }

    public function addBlock($header, \Closure $handler = null)
    {
        $block = new BlockStub($header);

        $this->lines[] = $block;

        if($handler)
            $handler($block);

        return $this;
    }

    public function addContinuedBlock($header, \Closure $handler = null)
    {
        $block = new BlockStub($header);

        $this->lines[] = $block;

        if($handler)
            $handler($block);

        $block->setAsContinued();

        return $this;
    }

    /**
     * @param mixed $arg
     * @return array
     */
    protected static function createLinesFromArgument($arg)
    {
        if($arg instanceof \Closure)
            return static::createLinesFromClosure($arg);

        if(is_string($arg))
            return static::createLinesFromString($arg);
    }

    /**
     * @param string $arg
     * @return array
     */
    protected static function createLinesFromString($arg)
    {
        return explode("\n", trim($arg));
    }

    /**
     * @param \Closure $code
     * @return array
     */
    protected static function createLinesFromClosure(\Closure $code)
    {
        $func = new \ReflectionFunction($code);

        // credit : http://stackoverflow.com/questions/7026690/reconstruct-get-code-of-php-function
        $filename = $func->getFileName();
        $start_line = $func->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
        $end_line = $func->getEndLine();
        $length = $end_line - $start_line;

        $source = file($filename);
        $body = implode("", array_slice($source, $start_line, $length));

        // credit : http://stackoverflow.com/questions/2034687/regex-get-string-value-between-two-characters
        $matches = array();
        $t = preg_match('/{(.*?)\}/s', $body, $matches);

        $lines = explode("\t", trim($matches[1]));

        $lines = array_map(function($value)
        {
            return trim($value);
        }, $lines);

        return $lines;
    }

    public function toLines()
    {
        if(count($this->lines) == 0)
            return array('');

        // block stub checking.
        $lines = array();

        foreach($this->lines as $index => $line)
        {
            if(is_object($line) && $line instanceof BlockStub)
            {
                if(!$line->isContinued() && $index !== 0)
                    $lines[] = '';

                foreach($line->toIndentedLines(1) as $line)
                    $lines[] = $line;
            }
            else
            {
                $lines[] = $line;
            }
        }

        return $lines;
    }
}