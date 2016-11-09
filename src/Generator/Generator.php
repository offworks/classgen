<?php
namespace Classgen\Generator;

use Classgen\Collection;
use Classgen\Stub\ClassStub;

/**
 * A psr-0 generator
 *
 * Class Generator
 * @package Classgen\Generator
 */
abstract class Generator
{
    /**
     * @var Collection|ClassStub[]
     */
    protected $classes;

    public function __construct()
    {
        $this->classes = new Collection;
    }

    /**
     * @return Collection|\Classgen\Stub\ClassStub[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    public function addClass($name)
    {
        return $this->classes->add($name);
    }

    public function setClasses(Collection $classes)
    {
        $this->classes = $classes;
    }

    abstract public function generate();
}