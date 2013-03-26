<?php
namespace Hat\Environment;

use Hat\Environment\State\ProfileState;

class Profile
{

    protected $path;

    /**
     * @var \Hat\Environment\State\ProfileState
     */
    protected $state;

    /**
     * @var Profile[]
     */
    protected $parents = array();

    /**
     * @var Definition[]|Holder
     */
    protected $definitions;

    /**
     * @var Definition[]|Holder
     */
    protected $system_definitions;

    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * @var \Hat\Environment\State\DefinitionState
     */
    public function getState()
    {
        if (!$this->state) {
            $this->state = new ProfileState();
        }
        return $this->state;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }


    public function addParent(Profile $parent)
    {
        $this->parents[] = $parent;
    }

    /**
     * @return Profile
     */
    public function getParents()
    {
        return $this->parents;
    }

    public function hasParents()
    {
        return count($this->parents) ? true : false;
    }

    /**
     * @param Definition $definition
     */
    public function addDefinition(Definition $definition)
    {
        $this->getDefinitions()->set($definition->getName(), $definition);
    }

    /**
     * @return Definition[]|Holder
     */
    public function getDefinitions()
    {

        if (!$this->definitions) {
            $this->definitions = new Holder();
        }

        return $this->definitions;


    }

    /**
     * @return Definition[]|Holder
     */
    public function getSystemDefinitions()
    {

        if (!$this->system_definitions) {
            $this->system_definitions = new Holder();
        }

        return $this->system_definitions;
    }

    /**
     * @param Definition $definition
     */
    public function addSystemDefinition(Definition $definition)
    {
        $this->getSystemDefinitions()->set($definition->getName(), $definition);
    }

    protected function getOwnFile($path)
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . $path;
    }

    protected function hasOwnFile($path)
    {
        return file_exists($this->getOwnFile($path));
    }

    public function apply(Profile $profile)
    {

        foreach ($profile->getDefinitions() as $definition) {

            if ($this->getDefinitions()->has($definition->getName())) {
                $this->getDefinitions()->get($definition->getName())->apply($definition);
            } else {
                $this->addDefinition($definition);
            }

        }

        foreach ($profile->getSystemDefinitions() as $definition) {

            if ($this->getSystemDefinitions()->has($definition->getName())) {
                $this->getSystemDefinitions()->get($definition->getName())->apply($definition);
            } else {
                $this->addSystemDefinition($definition);
            }

        }

    }

    public function extend(Profile $parent)
    {

        $copy = clone $parent;

        $copy->apply($this);

        $this->apply($copy);

        $this->addParent($parent);

    }


}
