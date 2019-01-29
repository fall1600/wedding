<?php
namespace Backend\BaseBundle\Propel\Behavior;


use Backend\BaseBundle\Propel\I18n;

class I18nBehaviorQueryBuilderModifier extends \I18nBehaviorObjectBuilderModifier
{
    public function queryFilter(&$script, $builder)
    {
        $builder->declareClass(I18n::class);
        $script = preg_replace('/abstract class \w+Query extends \w+/', '${0} implements I18n', $script);
    }
}