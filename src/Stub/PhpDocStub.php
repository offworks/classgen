<?php
namespace Classgen\Stub;

class PhpDocStub extends DocumentableStub
{
    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $comments = array();

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var array
     */
    protected $variables = array();

    /**
     * @var string
     */
    protected $returnType = 'void';

    /**
     * @var array
     */
    protected $tags = array();

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * @param $name
     * @param null|string $description
     * @return $this
     */
    public function addTag($name, $description = null)
    {
        $this->tags[] = array(
            'name' => $name,
            'description' => $description
        );

        return $this;
    }

    /**
     * @param $name
     * @param null $type
     * @return $this
     */
    public function addParameter($name, $type = null)
    {
        $param = array();

        $param['name'] = ltrim($name, '$');

        if($type)
            $param['type'] = '\\' . ltrim($type, '\\');

        $this->parameters[] = $param;

        return $this;
    }

    /**
     * @param $name
     * @param null|string $type
     * @retur $this
     */
    public function addVariable($name, $type = null)
    {
        $var = array();

        $var['name'] = ltrim($name, '$');

        if($type)
            $var['type'] = $type;

        $this->variables[] = $var;

        return $this;
    }

    /**
     * @return array
     */
    public function toLines()
    {
        $doc = array();

        $doc[] = '/**';

        if($this->description)
        {
            foreach(explode("\n", $this->description) as $descLn)
                $this->comments[] = trim($descLn);
        }

        if(count($this->comments) > 0)
        {
            foreach($this->comments as $comment)
                $doc[] = ' * ' . $comment;

            $doc[] = ' *';
        }

        foreach($this->tags as $tag)
        {
            $name = $tag['name'];

            $doc[] = ' * @'.$name . (isset($tag['description']) ? ' ' . $tag['description'] : '');
        }

        foreach($this->parameters as $param)
        {
            $name = $param['name'];

            $doc[] = ' * @param ' . (isset($param['type']) ? $param['type'] . ' ' : '') . '$' . $name;
        }

        foreach($this->variables as $var)
        {
            $name = $var['name'];

            $doc[] = ' * @var ' . (isset($var['type']) ? $var['type'] . ' ' : '') . '$' . $name;
        }

        if($this->returnType !== false)
            $doc[] = ' * @return ' . $this->returnType;

        $doc[] = ' */';

        return $doc;
    }

    /**
     * @param string $returnType
     * @return $this
     */
    public function addReturnType($returnType)
    {
        $this->returnType = $returnType;

        return $this;
    }

    /**
     * @return $this
     */
    public function removeReturnType()
    {
        $this->returnType = false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->description
        || $this->comments
        || $this->tags
        || $this->parameters
        || $this->variables
        || $this->returnType !== false;
    }
}