<?php
namespace Classgen\Stub;

class CodeStub extends Stub
{
    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public static function createFromClosure(\Closure $closure)
    {
        $code = $closure;

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

        $stub = new static(implode("\n", $lines));

        return $stub;
    }

    public function toLines()
    {
        return explode("\n", $this->content);
    }
}