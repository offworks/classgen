<?php
namespace Classgen\Stub;

abstract class MemberStub extends DocumentableStub
{
    /**
     * @var string
     */
    protected $accessibility;

    /**
     * @var bool
     */
    protected $isStatic = false;

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
}