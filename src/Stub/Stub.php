<?php
namespace Classgen\Stub;

class Stub
{
    /**
     * @var PhpDocStub
     */
    protected $doc;

    /**
     * @var null|string
     */
    protected $description;

    /**
     * @var array
     */
    protected $tags = array();

    /**
     * @var array
     */
    protected $comments = array();

    public function toLines()
    {
        return array();
    }

    public function tag($name, $description = null)
    {
        $this->tags[] = array(
            'name' => $name,
            'description' => $description
        );

        return $this;
    }

    public function getPhpDocStub()
    {
        $stub = new PhpDocStub;

        $stub->setDescription($this->description);

        foreach($this->comments as $comment)
            $stub->addComment($comment);

        foreach($this->tags as $tag)
            $stub->addTag($tag['name'], $tag['description']);

        return $stub;
    }

    public function addComment($comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param int $level
     * @return array
     */
    public function getIndentedLines($level = 1)
    {
        $newLines = array();

        foreach($this->toLines() as $line)
            $newLines[] = str_repeat('    ', $level) . $line;

        return $newLines;
    }
}