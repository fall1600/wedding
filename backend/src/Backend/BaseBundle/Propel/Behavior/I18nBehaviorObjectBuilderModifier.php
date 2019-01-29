<?php
namespace Backend\BaseBundle\Propel\Behavior;


use Backend\BaseBundle\Propel\I18n;

class I18nBehaviorObjectBuilderModifier extends \I18nBehaviorObjectBuilderModifier
{
    public function objectFilter(&$script, $builder)
    {
        $builder->declareClass(I18n::class);
        $script = preg_replace('/abstract class \w+ extends \w+ implements Persistent/', '${0}, I18n', $script);
        parent::objectFilter($script, $builder);
    }
}