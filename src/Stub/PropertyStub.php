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
     * If type is give through setValue
     * @var string
     */
    protected $valueType = null;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $isStatic = false;

    /** @var string */
    protected $value;

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

    /**
     * Set property value
     * @param $var
     * @return $this
     */
    public function setValue($value, $asString = false)
    {
        if($asString)
            return $this->setValueByString($value);

        $this->valueType = gettype($value);

        if(is_array($value))
            $value = static::normalizeExportedArray(var_export($value, true));
        else
            $value = var_export($value, true);

        return $this->setValueByString($value);
    }

    /**
     * Alias to setValue
     * @param $value
     * @return self
     */
    public function value($value, $asString = false)
    {
        return $this->setValue($value, $asString);
    }

    /**
     * @param $string
     * @return self
     */
    public function setValueByString($string)
    {
        $this->value = $string;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getType()
    {
        if($this->type)
            return $this->type;

        if($this->valueType === null)
            return null;

        return $this->valueType;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
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

        $doc->addVariable($this->name, $this->getType());

        return $doc;
    }

    /**
     * @param $accessibility
     * @return $this
     * @throws \Exception
     */
    public function setAccessibility($accessibility)
    {
        if( ! in_array($accessibility, array('protected', 'private', 'public')))
            throw new \Exception('Accessibility may only be protected, private, or public');

        $this->accessibility = $accessibility;

        return $this;
    }

    /**
     * Alias to setAccessibility()
     * @param $accessibility
     * @return PropertyStub
     */
    public function accessibility($accessibility)
    {
        return $this->setAccessibility($accessibility);
    }

    public function toLines()
    {
        $line = $this->accessibility . ' ' . ($this->isStatic ? 'static ' : '') . '$' . $this->name;

        if($this->value)
            $line = $line . ' = ' . $this->value.';';

        return array_merge($this->getPhpDocStub()->toLines(), array($line));
    }
}