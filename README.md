# classgen
A simple PHP class generator that helps developer with more dynamic PHP classes generation.

## Usage
```
$generator = new \Classgen\Classgen;

$class = $generator->addClass('App\Http\PostController');

$class->inherits('App\Http\BaseController');

$class->addProperty('logged_in', 'bool');

$class->addMethod('execute')
    ->addComment('Execute this controller')
    ->setReturnType('void')
    ->setCode(function()
    {
        parent::execute();
    });

```

## License
See [MIT License](LICENSE.md)
