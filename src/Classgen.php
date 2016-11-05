<?php
namespace Classgen;

use Classgen\Stub\ClassStub;

class Classgen
{
    /**
     * @var ClassStub[] $classes
     */
    protected $classes = array();

    /**
     * @var string|null
     */
    protected $baseNamespace;

    /**
     * Add class
     *
     * @param $name
     * @return ClassStub
     */
    public function addClass($name)
    {
        return $this->classes[] = new ClassStub($name);
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function setBaseNamespace($namespace)
    {
        $this->baseNamespace = $namespace;

        return $this;
    }

    /**
     * Classes on the given path
     *
     * @param string $path
     */
    public function generate($path)
    {
        foreach($this->classes as $stub)
        {
            $filename = $stub->getShortClassname() . '.php';

            $namespace = $stub->getNamespace();

            if($this->baseNamespace)
                $namespace = trim(substr($namespace, strlen($this->baseNamespace)), '\\');

            $dir = $path . '/' . str_replace('\\', '/', $namespace);

            if( ! file_exists($dir))
                mkdir($dir, 0777, true);

            $filepath = $dir . '/' . $filename;

            file_put_contents($filepath, $stub->toString());
        }
    }
}