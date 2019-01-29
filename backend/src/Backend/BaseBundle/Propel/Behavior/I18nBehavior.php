<?php
namespace Backend\BaseBundle\Propel\Behavior;

class I18nBehavior extends \I18nBehavior
{
    protected function getDirname()
    {
        if (null === $this->dirname) {
            $r = new \ReflectionClass(parent::class);
            $this->dirname = dirname($r->getFileName());
        }
        return $this->dirname;
    }

    public function getObjectBuilderModifier()
    {
        if (is_null($this->objectBuilderModifier)) {
            $this->objectBuilderModifier = new I18nBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    public function getQueryBuilderModifier()
    {
        if (is_null($this->queryBuilderModifier)) {
            $this->queryBuilderModifier = new I18nBehaviorQueryBuilderModifier($this);
        }

        return $this->queryBuilderModifier;
    }
}