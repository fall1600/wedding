<?php
namespace Widget\PhotoBundle\Propel\Behavior;


use Widget\PhotoBundle\File\PhotoUploadFile;
use Widget\PhotoBundle\Image\PhotoList;

class ObjectBuilderModifier
{
    /** @var \Behavior  */
    protected $behavior;

    /** @var \Table  */
    protected $table;

    protected $objectBuilder;

    public function __construct(\Behavior $behavior)
    {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
    }

    public function objectMethods(\PHP5ObjectBuilder $builder)
    {
        $this->objectBuilder = $builder;
        $column = $this->behavior->getColumnForParameter('column');
        return <<<EOT
{$this->objectPhotoMasterIndexGetterMethod($builder, $column)}

{$this->objectPhotoMasterIndexSetterMethod($builder, $column)}
EOT;
    }

    protected function objectPhotoMasterIndexGetterMethod(\PHP5ObjectBuilder $builder, \Column $column)
    {
        $photoMasterIndexAttributeName = $this->getPhotoMasterIndexAttributeName();
        $columnPhpName = $column->getPhpName();
        return <<<EOT
public function get{$columnPhpName}Master()
{
    if(\$this->{$photoMasterIndexAttributeName} === false) {
        if (!(\$photos = \$this->get{$columnPhpName}())) {
            return false;
        }
        \$this->{$photoMasterIndexAttributeName} = array_search(\$photos->getMasterPhoto(), (array) \$photos);
    }
    return \$this->{$photoMasterIndexAttributeName};
}
EOT;
    }

    protected function objectPhotoMasterIndexSetterMethod(\PHP5ObjectBuilder $builder, \Column $column)
    {
        $photoMasterIndexAttributeName = $this->getPhotoMasterIndexAttributeName();
        $columnPhpName = $column->getPhpName();
        return <<<EOT
public function {$columnPhpName}Master(\$index)
{
    \$this->{$photoMasterIndexAttributeName} = \$index;
    return \$this;
}
EOT;
    }

    protected function getPhotoAttributeName()
    {
        $column = $this->behavior->getColumnForParameter('column');
        return "{$column->getName()}Photos";
    }

    protected function getPhotoMasterIndexAttributeName()
    {
        $column = $this->behavior->getColumnForParameter('column');
        return "{$column->getName()}MasterIndex";
    }

    public function objectAttributes(\PHP5ObjectBuilder $builder)
    {
        $column = $this->behavior->getColumnForParameter('column');
        return "
protected \${$this->getPhotoAttributeName()};

private \${$this->getPhotoMasterIndexAttributeName()} = false;
        ";
    }

    protected function getSetterScript($columnPhpName)
    {
        $photoAttributeName = $this->getPhotoAttributeName();
        $photoListClass = '\\'.PhotoList::class;
        $photoUploadClass = '\\'.PhotoUploadFile::class;
        $photoMasterIndexAttributeName = $this->getPhotoMasterIndexAttributeName();
        return <<<EOT

   /**
     * Set the value of [photos] column.
     *
     * @param   \$photos new value
     * @return {$this->table->getPhpName()} The current object (for fluent API support)
     */
    public function set{$columnPhpName}(\$photos)
    {
        \$resultArray = array();
        \$originPhotos = \$this->get{$columnPhpName}();

        foreach (\$photos as \$index => \$photo){
            if(\$photo){
                if(\$photo instanceof $photoUploadClass){
                    \$resultArray[\$index] = \$photo->makePhoto();
                }
                else{
                    \$resultArray[\$index] = \$photo;
                }
                continue;
            }

            if(isset(\$originPhotos[\$index])){
                if(\$photo instanceof $photoUploadClass){
                    \$resultArray[\$index] = \$originPhotos[\$index]->makePhoto();
                }
                else{
                    \$resultArray[\$index] = \$originPhotos[\$index];
                }
                continue;
            }
        }
        if(!\$resultArray){
            \$this->{$photoAttributeName} = \$resultArray;
            return \$this->rebuild_set{$columnPhpName}(null);
        }
        
        \$photo = isset(\$resultArray[\$this->{$photoMasterIndexAttributeName}])?\$resultArray[\$this->{$photoMasterIndexAttributeName}]:null;
        \$resultArray = array_values(\$resultArray);
        \$this->{$photoAttributeName} = \$resultArray;
        \$this->{$photoMasterIndexAttributeName} = array_search(\$photo, \$resultArray);
        \$photoList = new $photoListClass(\$resultArray);
        \$photoList->setMasterPhotoIndex(\$this->{$photoMasterIndexAttributeName});
        return \$this->rebuild_set{$columnPhpName}(\$photoList);      
    }
EOT;
    }

    public function preSave($builder)
    {
        $column = $this->behavior->getColumnForParameter('column');
        $columnPhpName = $column->getPhpName();
        return <<<EOT
\$a{$columnPhpName} = \$this->get{$columnPhpName}();
if(\$a{$columnPhpName} && \$a{$columnPhpName}->isModified()){
    \$this->set{$columnPhpName}(\$a{$columnPhpName});
}
EOT;
    }

    protected function generateSetterMethod(&$script)
    {
        $columnPhpName = $this->behavior->getColumnForParameter('column')->getPhpName();
        $setterMethodName = "set$columnPhpName";
        $parser = new \PropelPHPParser($script, true);
        $oldMethod = $parser->findMethod($setterMethodName);
        $oldMethod = $this->replaceMethodName($oldMethod, $setterMethodName);
        $parser->replaceMethod($setterMethodName, $oldMethod.$this->getSetterScript($columnPhpName));
        $script = $parser->getCode();
    }

    public function objectFilter(&$script)
    {
        $this->generateSetterMethod($script);
    }

    protected function replaceMethodName($script, $methodName)
    {
        $pattern = '/public\s+function\s+(' . $methodName . ')/i';
        return preg_replace($pattern, "private function rebuild_$1", $script);
    }

    public function objectClearReferences($builder)
    {
        $name = $this->getPhotoAttributeName();
        return "
\$this->{$name} = null;
        ";
    }

}