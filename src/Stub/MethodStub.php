<?php
namespace Classgen\Stub;

class MethodStub extends DocumentableStub
{
    protected $code;

    protected $codeStub;

    protected $name;

    protected $returnType;

    protected $accessibility = 'public';

    protected $isStatic = false;

    protected $parameters = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setCode(\Closure $code)
    {
        $this->code = $code;

        $this->codeStub = CodeStub::createFromClosure($code);

        $this->setParametersFromClosure($code);

        return $this;
    }

    public function setParametersFromClosure(\Closure $code)
    {
        $params = array();

        $function = new \ReflectionFunction($code);

        foreach($function->getParameters() as $param)
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

        $this->parameters = $params;
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
        return $this->parameters;
    }

    public function getCodeStub()
    {
        return $this->codeStub;
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

        foreach($this->getCodeStub()->toLines() as $line)
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