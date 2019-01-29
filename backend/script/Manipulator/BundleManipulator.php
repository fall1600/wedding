<?php
namespace Dgfactor\Script\Manipulator;


use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;

class BundleManipulator extends Manipulator
{
    protected $namespace = '';
    protected $class = '';

    public function __construct(\SplFileInfo $bundleFile)
    {
        $this->setCode(token_get_all(file_get_contents($bundleFile->getPathname())));
    }

    /**
     * @return $this
     */
    public function parse()
    {
        while ($token = $this->next()) {
            if(is_array($token) && $token[0] == T_NAMESPACE){
                $this->parseNamespace();
            }

            if(is_array($token) && $token[0] == T_CLASS){
                $this->parseClass();
                break;
            }
        }
        return $this;
    }

    protected function parseNamespace()
    {
        while($token = $this->next()){
            if((is_array($token) && $token[0] == T_CURLY_OPEN) || $token == ';'){
                break;
            }
            if(is_array($token) && $token[0] == T_WHITESPACE){
                continue;
            }
            if(is_array($token) && in_array($token[0], array(T_STRING, T_NS_SEPARATOR))){
                $this->namespace.=$token[1];
            }
        }
    }

    protected function parseClass()
    {
        while($token = $this->next()){
            if((is_array($token) && in_array($token[0], array(T_CURLY_OPEN, T_EXTENDS, T_IMPLEMENTS))) || $token == ';'){
                break;
            }
            if(is_array($token) && $token[0] == T_WHITESPACE){
                continue;
            }
            if(is_array($token) && in_array($token[0], array(T_STRING, T_NS_SEPARATOR))){
                $this->class.=$token[1];
            }
        }
    }

    public function getFullClass()
    {
        $class = $this->namespace.'\\'.$this->class;
        $class = str_replace('////', '', $class);
        return $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

}