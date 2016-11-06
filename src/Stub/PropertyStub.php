<?php
namespace Classgen\Stub;

class PropertyStub extends DocumentableStub
{
    /**
     * @var string
     */
    protected $accessibility = 'protected';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var null|string
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $isStatic = false;

    /**
     * PropertyStub constructor.
     * @param $name
     */
    public function __construct($name, $type = null, $description = null)
    {
        $this->name = $name;

        $this->type = $type;

        $this->description = $description;
    }

    public function setDefault($var)
    {
        $this->default = var_export($var, true);
    }

    public function setDefaultByString($string)
    {
        $this->default = $string;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param bool $static
     * @return $this
     */
    public function setAsStatic($static = true)
    {
        $this->isStatic = $static;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessibility()
    {
        return $this->accessibility;
    }

    /**
     * @return PhpDocStub
     */
    public function getPhpDocStub()
    {
        $doc = parent::getPhpDocStub();

        $doc->removeReturnType();

        $doc->addVariable($this->name, $this->type);

        return $doc;
    }

    /**
     * @param string $accessibility
     * @return $this
     */
    public function setAccessibility($accessibility)
    {
        $this->accessibility = $accessibility;

        return $this;
    }

    public function toLines()
    {
        $lines = array();

        $lines[] = $this->accessibility . ' ' . ($this->isStatic ? 'static ' : '') . '$' . $this->name;

        return array_merge($this->getPhpDocStub()->toLines(), $lines);
    }
}