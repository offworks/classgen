# classgen
A simple, dynamic and programmatically fluent PHP class generator.

## Installation
Through composer
```
composer require offworks/classgen
```

## Usage
### Class creation manipulation
#### Create a class
```
$class = new \Classgen\Stub\ClassStub('Acme\Models\Blog');

$class->inherits('BaseModel');
```
#### Add property
```
$property = $class->addProperty('isPublished', 'bool');

$property = $class->addStaticProperty('timestamp', 'bool');
```
#### Add method
```
$method = $class->addMethod('doNothing');
```

And it'll print the class on casting the object to string.
```
echo $class;
```
```
<?php
namespace Acme\Model;

class Blog extends BaseModel
{
    /**
     * @var bool
     */
    protected $isPublished;
    
    /**
     * @var bool
     */
    protected static $timestamp;
    
    /**
     * @return void
     */
    public function doNothing()
    {
    
    }
}
```
### Class's method manipulation
#### Generate a stub from \Closure
Through the initialize($mixed) method, the stub will automatically generate the method's params, if \Closure is passed.
```
// sample
$method = $class->addMethod('setAtPublished');

$method->comment('Publish the article')
    ->returnAs('self')
    ->initialize(function($published = true)
    {
        // mark this article as published
        $this->setPublished($published ? 1 : 0);
        
        return $this;
    });
```
This code might generate a method stub something like this :
```
/**
 * Publish the article
 *
 * @param $published
 * @return self
 */
public function setAsPublished($published = true)
{
    // mark this article as published
    $this->setPublished($published ? 1 : 0);
    
    return $this;
}
```
#### Generate the stub directly from string
```
$method = $class->addMethod('isPublished');

$method->comment('Check whether article is published')
   ->returnAs('bool')
   ->initialize('return $this->isPublished == true;');
```
Generate a method stub something like this :
```
/**
 * Check whether article is published
 *
 * @return bool
public function isPublished()
{
    return $this->isPublished == true;
}
```

#### More nested code block
```
$method = $class->addMethod('isPopular')->returnAs('bool');

// code() method let you code within a safe handler. (enough with pollution)
$method->code(function($code)
{
    $code->addBlock('if($this->likes > 1000)', function($code)
    {
         $code->addBlock('if($this->comments->count > 100)', function($code)
         {
             $code->write('return true;');
         });
    });
    
    // continued block will skip the line break
    $code->addContinuedBlock('else if($this->isDeleted())', function($code)
    {
        $code->write(function()
        {
            throw new \Exception('The article has been deleted. Throw! throw!');
        });
    });
    
    $code->write('return false;');
});
```
Generate a method stub something like this :
```
/**
 * @return bool
 */
public function isPopular()
{
    if($this->likes > 1000)
    {
        if($this->comments->count > 100)
        {
            return true;
        }
    }
    else if($this->isDeleted())
    {
        throw new \Exception('The article has been deleted. Throw! throw!');
    }
    
    return false;
}
```

## Saving the class to the desired path
### file_put_contents
Most straight forward way
```
file_put_contents(__DIR_.'/app/MyClass.php', $class->toString());
```
### With generator
#### PSR-4 based generator
```
$generator = new \Classgen\Generator\Psr4('Acme\\', __DIR__.'/src');
```
The generator has it's own sort of Collection of classes. Adding a class is simply by passing class name (it'll create a new), or add existing instance.
```
$generator->addClass($class); //existing

$class = $generator->addClass('Acme/Routes/AdminRoute');
```
And generate once you're done.
```
$generator->generate();
```
It will generator a structure and file (since we have only one class) similar to this :
```
/src
  /Model
    Article.php
  /Routes
    AdminRoute.php
```

#### Flat based generator
Generate classes in a single directory
```
$generator = new \Classgen\Generator\Flat(__DIR__.'/src/Payments');

$generator->addClass('App\Payment\Paypal');
$generator->addClass('App\Payment\Cash');
$generator->addClass('App\Payment\Cc');
```
And generate
```
$generator->generate();
```

## API
### \Classgen\Stub\ClassStub
- inherits(string $parentClass) : self
- implementInterfaces(array $interfaces) : self
- addMethod(string $name, mixed $code) : \Classgen\Stub\MethodStub'
- getName() : string
- getNamespace() : string
- getShortClassname() : string
- addProperty(string $name, string $type = null) : \Classgen\Stub\PropertyStub
- addStaticProperty(string $name, string $type = null) : Classgen\Stub\PropertyStub
- addMethod(string $name, mixed $code = null) : \Classgen\Stub\MethodStub
- addStaticMethod(string $name, mixed $code = null) : \Classgen\Stub\MethodStub

### \Classgen\Stub\MethodStub
- initialize(mixed $code) : self
- returnAs(string $type) : self
- setAccessibility(string $accessibility) : self
- code(\Closure $handler) : self
- getCode() : \Classgen\Stub\CodeStub

### \Classgen\Stub\CodeStub
- replace(string $find, string $replace) : self
- filter(\Closure $handler) : self
- write(string|\Closure $code) : self
- each(\Closure $handler) : self
- prepend(string|\Closure $code) : self
- addBlock($header, \Closure $handler = null) : \Classgen\Stub\BlockStub
- addContinuedBlock($header, \Closure $handler = null) : \Classgen\Stub\Blockstub

## License
See [MIT License](LICENSE.md)

## Thank you!
Hope you like it!