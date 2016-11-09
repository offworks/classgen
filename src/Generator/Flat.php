<?php
namespace Classgen\Generator;

use Classgen\Stub\ClassStub;

class Flat extends Generator
{
    protected $directory;

    public function __construct($directory)
    {
        parent::__construct();

        $this->directory = $directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Generate the classes on the given directory
     */
    public function generate()
    {
        $dir = $this->directory;

        $this->classes->each(function($name, ClassStub $class) use($dir)
        {
            if( ! file_exists($dir))
                mkdir($dir, 0777, true);

            $filepath = $dir . '/' . $class->getShortClassname() . '.php';

            file_put_contents($filepath, $class->toString());
        });
    }
}