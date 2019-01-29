<?php
namespace Backend\BaseBundle\Propel\Behavior;


class AddRandomPkBehavior extends \Behavior
{
    public function preInsert(\PHP5ObjectBuilder $builder)
    {
        $script = "\$this->prepareId();";
        return $script;
    }

    public function objectMethods(\PHP5ObjectBuilder $builder)
    {
        return "
protected function prepareId()
{
    if (\$this->getId() === null) {
        \$id = sprintf('%d%03d', floor(microtime(true) * 10000), rand(0, 999));
        \$this->setId(\$id);
    }
}
        ";
    }

}