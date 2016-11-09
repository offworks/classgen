<?php
namespace Classgen\Generator;

use Classgen\Collection;
use Classgen\Stub\ClassStub;

class Psr4 extends Generator
{
    protected $baseNamespace;

    protected $src;

    public function __construct($baseNamespace, $src)
    {
        parent::__construct();

        $this->src = $src;

        $this->baseNamespace = $baseNamespace;
    }

    public function generate()
    {
        $baseNamespace = $this->baseNamespace;

        $src = $this->src;

        $this->classes->each(function($name, ClassStub $class) use($src, $baseNamespace)
        {
            $filename = $class->getShortClassname() . '.php';

            $namespace = substr($class->getNamespace(), strlen(trim($baseNamespace, '\\')));

            $dir = $src . '/' . str_replace('\\', '/', $namespace);

            if( ! file_exists($dir))
                mkdir($dir, 0777, true);

            $filepath = $dir . '/' . $filename;

            file_put_contents($filepath, $class->toString());
        });
    }
}