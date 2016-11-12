<?php
namespace Classgen\Stub;

abstract class MemberStub extends DocumentableStub
{
    /**
     * @var string
     */
    protected $visibility;

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
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param $visibility
     * @return $this
     * @throws \Exception
     */
    public function setVisibility($visibility)
    {
        if( ! in_array($visibility, array('protected', 'private', 'public')))
            throw new \Exception('Accessibility may only be protected, private, or public');

        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Alias to setAccessibility()
     * @param $visibility
     * @return PropertyStub
     */
    public function visibility($visibility)
    {
        return $this->setVisibility($visibility);
    }
}