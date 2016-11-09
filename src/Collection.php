<?php
namespace Classgen;

use Classgen\Stub\ClassStub;

/**
 * Collection of classes
 *
 * Class Collection
 * @package Classgen
 */
class Collection extends \ArrayIterator
{
    public function each(\Closure $handler)
    {
        foreach($this as $name => $class)
            $handler($name, $class);
    }

    public function add($name)
    {
        if(is_object($name) && $name instanceof ClassStub)
            $this[$name->getName()] = $class = $name;
        else
            $this[$name] = $class = new ClassStub($name);

        return $class;
    }
}