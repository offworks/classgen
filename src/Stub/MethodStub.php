<?php
namespace Classgen\Stub;

class MethodStub extends Stub
{
    protected $code;

    protected $name;

    protected $returnType;

    protected $accessibility = 'public';

    protected $isStatic = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setCode(\Closure $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setReturnType($type)
    {
        $this->returnType = $type;

        return $this;
    }

    public function setAccessibility($accessibility)
    {
        $this->accessibility = $accessibility;
    }

    public function setAsStatic($static = true)
    {
        $this->isStatic = $static;

        return $this;
    }

    public function getReturnType()
    {
        return $this->returnType ? : 'void';
    }

    public function getPhpDocStub()
    {
        $doc = new PhpDocStub;

        $doc->setDescription($this->description);

        foreach($this->getParameters() as $param)
        {
            list($name) = explode(' ', $param['name']);

            $type = isset($param['type']) ? $param['type'] : null;

            $doc->addParameter($name, $type);
        }

        if($this->isMagicMethod())
            $doc->removeReturnType();
        else
            $doc->addReturnType($this->getReturnType());

        return $doc;
    }

    public function isMagicMethod()
    {
        return strpos($this->name, '__') === 0;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $params = array();
        $code = $this->code ? : function(){};

        $func = new \ReflectionFunction($code);

        foreach($func->getParameters() as $param)
        {
            $string = (string) $param;
            $matches = array();

            preg_match('/\[(.*?)\]/', $string, $matches);

            $match = trim(strip_tags($matches[1]));

            $p = array();

            if(strpos($match, '$') === 0)
            {
                $p['name'] = $match;
            }
            else
            {
                list($type, $name) = explode(' ', $match, 2);

                $p['type'] = $type;

                $p['name'] = $name;
            }

            $params[] = $p;
        }

        return $params;
    }

    /**
     * @return array
     */
    public function getCodeLines()
    {
        $code = $this->code ? : function(){};

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

    /**
     * @return string
     */
    public function getNameStub()
    {
        $methodName = $this->name;

        $params = array();

        foreach($this->getParameters() as $param)
        {
            $name = $param['name'];

            $name = str_replace('Array', 'array()', $name);

            if(isset($param['type']))
                $params[] = '\\'.$param['type'] . ' ' . $name;
            else
                $params[] = $name;
        }

        return $methodName . '(' . implode(', ', $params) . ')';
    }

    /**
     * @return array
     */
    public function getMethodLines()
    {
        $stub = array();

        $stub[] = $this->accessibility.' '.($this->isStatic ? 'static ' : '') . 'function '.$this->getNameStub();

        $stub[] = '{';

        foreach($this->getCodeLines() as $line)
            $stub[] = '    '.$line;

        $stub[] = '}';

        return $stub;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return implode("\n", $this->toLines());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return array
     */
    public function toLines()
    {
        return array_merge($this->getPhpDocStub()->toLines(), $this->getMethodLines());
    }
}