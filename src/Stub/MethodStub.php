<?php
namespace Classgen\Stub;

class MethodStub extends DocumentableStub
{
    protected $code;

    /**
     * @var null|CodeStub
     */
    protected $codeStub;

    protected $name;

    protected $returnType;

    protected $accessibility = 'public';

    protected $isStatic = false;

    protected $parameters = array();

    public function __construct($name)
    {
        $this->name = $name;

        $this->codeStub = new CodeStub;
    }

    public function setCode($arg)
    {
        $this->codeStub->append($arg);

        if (is_object($arg) && $arg instanceof \Closure)
            $this->setParametersFromClosure($arg);

        return $this;
    }

    public function addParameter($name, $type = null, $default = 'lsLSMK02L', $defaultType = null)
    {
        $param = array(
            'name' => $name,
            'type' => $type
        );

        if($default !== 'lsLSMK02L')
        {
            $param['default'] = array(
                'type' => $defaultType ? : gettype($default),
                'value' => $default
            );
        }

        $this->parameters[] = $param;

        return $this;
    }

    public function setParametersFromClosure(\Closure $code)
    {
        $params = array();

        $function = new \ReflectionFunction($code);

        foreach ($function->getParameters() as $param) {
            $string = (string)$param;
            $matches = array();

            preg_match('/\[(.*?)\]/', $string, $matches);

            $match = trim(strip_tags($matches[1]));

            $p = array();

            if(strpos($match, '$') === 0)
            {
                $name = substr($match, 1);
            }
            else
            {
                list($type, $name) = explode(' ', $match, 2);

                if (strpos($name, '$') === 0)
                    $name = substr($name, 1);

                $p['type'] = $type;
            }

            @list($name) = explode(' = ', $name, 2);

            $p['name'] = $name;

            if($param->isOptional())
            {
                if($param->isDefaultValueConstant())
                {
                    $p['default'] = array(
                        'type' => 'constant',
                        'value' => $param->getDefaultValueConstantName());
                }
                else
                {
                    if(is_array($param->getDefaultValue()))
                    {
                        $p['default'] = array(
                            'type' => 'array',
                            'value' => $param->getDefaultValue()
                        );
                    }
                    else if(is_null($param->getDefaultValue()))
                    {
                        $p['default'] = array(
                            'type' => 'NULL',
                            'value' => null
                        );
                    }
                    else
                    {
                        $p['default'] = array(
                            'type' => 'string',
                            'value' => $param->getDefaultValue()
                        );
                    }
                }
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
        return $this->returnType ?: 'void';
    }

    public function getPhpDocStub()
    {
        $doc = parent::getPhpDocStub();

        foreach ($this->getParameters() as $param)
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

    /**
     * @return null|CodeStub
     */
    public function getCodeStub()
    {
        return $this->codeStub;
    }

    protected static function normalizeExportedArray($string)
    {
        return implode('', explode("\n", $string));
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

            $name = '$' . $name;

            if(isset($param['default']))
            {
                switch($param['default']['type'])
                {
                    case 'constant':
                        $name = $name . ' = ' . $param['default']['value'];
                    break;
                    case 'array':
                        $name = $name . ' = ' . static::normalizeExportedArray(var_export($param['default']['value'], true));
                    break;
                    case 'string':
                        $name = $name . ' = ' . var_export($param['default']['value'], true);
                    break;
                    case 'integer':
                        $name = $name . ' = ' . $param['default']['value'];
                    break;
                    case 'NULL':
                        $name = $name . ' = null';
                    break;
                }
            }

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
        $doc = $this->getPhpDocStub();

        if($doc->isValid())
            return array_merge($this->getPhpDocStub()->toLines(), $this->getMethodLines());

        return $this->getMethodLines();
    }
}