<?php
namespace Hat\Environment\Handler\Definition;

use Hat\Environment\Definition;
use Hat\Environment\Handler\Handler;

use Hat\Environment\State\State;
use Hat\Environment\Kit\Kit;

class DocHandler extends Handler
{

    /**
     * @var Kit
     */
    protected $kit;

    public function __construct(Kit $kit)
    {
        $this->kit = $kit;
    }

    public function supports($definition)
    {
        return $definition instanceof Definition
            && $definition->getOptions()->has('doc')
            && $definition->getState()->isFail();
    }

    protected function doHandle($definition)
    {
        return $this->handleDefinition($definition);
    }

    protected function handleDefinition(Definition $definition)
    {
        $path = $definition->getOptions()->get('doc');

        $profile = $this->kit->get('profile.register')->getProfile();

        $doc = $this->kit->get('profile.loader')->loadDocForProfile($profile, $path);

        $this->kit->get('output')->writeln();
        $this->kit->get('output')->writeln($doc);
        $this->kit->get('output')->writeln();

    }

}
