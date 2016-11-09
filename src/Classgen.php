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
     * Classes on the given path
     *
     * @param string $path
     */
    public function generatePsr4($baseNamespace, $path)
    {
        foreach($this->classes as $stub)
        {
            $filename = $stub->getShortClassname() . '.php';

            $segments = explode('\\', $stub->getNamespace());

            /*if(count($segments) > 1)
                array_shift($segments);*/

            $namespace = substr($stub->getNamespace(), strlen(trim($baseNamespace, '\\')));

            $dir = $path . '/' . str_replace('\\', '/', $namespace);

            if( ! file_exists($dir))
                mkdir($dir, 0777, true);

            $filepath = $dir . '/' . $filename;

            file_put_contents($filepath, $stub->toString());
        }
    }
}