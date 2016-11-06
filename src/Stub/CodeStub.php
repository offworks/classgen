<?php
namespace Classgen\Stub;

class CodeStub extends Stub
{
    protected $content;

    public function __construct($content = '')
    {
        $this->content = $content;
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
        $this->content = str_replace($find, $replace, $this->content);

        return $this;
    }

    public function filter(\Closure $closure)
    {
        $this->content = $closure($this->content);

        return $this;
    }

    public function prepend($arg)
    {
        $this->content = implode("\n", static::createLinesFromArgument($arg)) . "\n\n" . $this->content;

        return $this;
    }

    public function append($arg)
    {
        $content = implode("\n", static::createLinesFromArgument($arg));

        $this->content = ($this->content) ? $this->content . "\n\n" . $content : $content;

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
        return explode("\n", $this->content);
    }
}