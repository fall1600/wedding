<?php
namespace Widget\PhotoBundle\Propel\Behavior;


class PhotoSaveBehavior extends \Behavior
{

    // default parameters value
    protected $parameters = array(
        'column'           => null,
    );

    public function getObjectBuilderModifier()
    {
        if (is_null($this->objectBuilderModifier)) {
            $this->objectBuilderModifier=new ObjectBuilderModifier($this);
        }
        return $this->objectBuilderModifier;
    }
}