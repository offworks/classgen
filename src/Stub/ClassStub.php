<?php
namespace Classgen\Stub;

class ClassStub extends DocumentableStub
{
    /** @var MethodStub[] $methods */
    protected $methods = array();

    /**
     * @var string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $parent;

    /** @var PropertyStub[] */
    protected $properties = array();

    /**
     * @var array
     */
    protected $interfaces = array();

    /**
     * @var bool
     */
    protected $isFinal = false;

    /**
     * @var bool
     */
    protected $isAbstract = false;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get fully qualified class name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        $names = explode('\\', $this->name);

        if(count($names) == 1)
            return null;

        array_pop($names);

        return implode('\\', $names);
    }

    public function getShortClassname()
    {
        $names = explode('\\', $this->name);

        return array_pop($names);
    }

    public function extendsFrom($name)
    {
        $this->parent = $name;

        return $this;
    }

    /**
     * Alias to extendsFrom
     * @param $name
     * @return ClassStub
     */
    public function inherits($name)
    {
        return $this->extendsFrom($name);
    }

    public function setAsAbstract($abstract = true)
    {
        $this->isAbstract = true;

        return $this;
    }

    public function implementInterfaces(array $interfaces)
    {
        $this->interfaces = $interfaces;

        return $this;
    }

    public function interfaces(array $interfaces)
    {
        return $this->implementInterfaces($interfaces);
    }

    public function addProperty($name, $type = null)
    {
        $this->properties[] = $stub = new PropertyStub($name, $type);

        return $stub;
    }

    public function addStaticProperty($name, $type = null)
    {
        $stub = $this->addProperty($name, $type);

        $stub->setAsStatic();

        return $stub;
    }

    /**
     * Add method
     * @param string $name
     * @param null|mixed $code
     * @return MethodStub
     */
    public function addMethod($name, $code = null)
    {
        $this->methods[$name] = $stub = new MethodStub($name, $code);

        return $stub;
    }

    public function getMethod($name)
    {
        return $this->methods[$name];
    }

    /**
     * Add static method
     * @param $name
     * $param null|mixed $code
     * @return MethodStub
     */
    public function addStaticMethod($name, $code = null)
    {
        $method = $this->addMethod($name, $code);

        $method->setAsStatic(true);

        return $method;
    }

    /**
     * @param bool $final
     * @return $this;
     */
    public function setAsFinal($final = true)
    {
        $this->isFinal = $final;

        return $this;
    }

    public function getNameStub()
    {
        $name = ($this->isFinal ? 'final ' : '') . 'class '.$this->getShortClassname();

        if($this->parent)
            $name .= ' extends '.$this->parent;

        if($this->interfaces)
            $name .= ' implements ' . implode(', ', $this->interfaces);

        return $name;
    }

    public function getPhpDocStub()
    {
        $stub = parent::getPhpDocStub();

        $stub->removeReturnType();

        return $stub;
    }

    /**
     * @return null|string
     */
    public function getNamespaceStub()
    {
        $namespace = $this->getNamespace();

        if( ! $namespace )
            return null;

        return 'namespace ' . $namespace.';';
    }

    /**
     * @return array
     */
    public function toLines()
    {
        $lines = array();

        $lines[] = '<?php';

        if($stub = $this->getNamespaceStub())
            $lines[] = $stub;

        $lines[] = '';

        $doc = $this->getPhpDocStub();

        if($doc->isValid())
        {
            foreach($doc->toLines() as $line)
            {
                $lines[] = $line;
            }
        }

        $lines[] = $this->getNameStub();

        $lines[] = '{';

        // properties
        foreach($this->properties as $index => $property)
        {
            $propertyLines = $property->toIndentedLines();

            foreach($propertyLines as $line)
                $lines[] = $line;

            $lines[] = '';
        }

        // methods
        foreach($this->methods as $index => $method)
        {
            $methodLines = $method->toIndentedLines();

            foreach($methodLines as $line)
                $lines[] = $line;

            if($index != (count($this->methods) - 1))
                $lines[] = '';
        }

        $lines[] = '}';

        return $lines;
    }
}

