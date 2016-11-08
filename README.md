# classgen
A simple PHP class generator that helps developer with more dynamic PHP classes generation.

## Usage
### Instantiate the generator service
```
$generator = new \Classgen\Classgen;
```
### Class creation manipulation
#### Create a class
```
$class = $generator->addClass('Acme\Models\Blog');
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

The codes above might generate a stub like this :
```
<?php
namespace Acme\Model;

class Blog
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
#### Generate a code block stub from \Closure
The stub will automatically generate the method's parameter, by the \Closure parameters.
```
// sample
$class->addMethod('setAtPublished')
    ->addComment('Publish the article')
    ->setReturnType('self')
    ->setCode(function($published = true) {
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
    $this->setPublished($published ? 1 : 0);
    
    return $this;
}
```
#### Generate a code block stub from string
```
$class->addMethod('isPublished')
    ->addComment('Check whether article is published')
    ->setReturnType('bool')
    ->setCode('return $this->isPublished == true;');
```
Generate a method stub something like this
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
$method = $class->addMethod('isPopular')->setReturnType('bool');

$method->getCodeStub()->addBlock('if($this->likes > 1000)', function($block)
{
    $block->addBlock('if($this->comments->count > 100)', function($block)
    {
        $block->write('return true;');
    });
})->write('return false;');
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
    
    return false;
}
```
## License
See [MIT License](LICENSE.md)
