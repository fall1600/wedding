<?php
namespace Backend\BaseBundle\Token\Service;

class Token
{
    protected $clams;

    public function __construct($clams)
    {
        $this->clams = $clams;
    }

    public function getId()
    {
        return $this->clams['id']??null;
    }

    public function getType()
    {
        return $this->clams['type']??null;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->clams['data']??array();
    }

    /**
     * 取得全部欄位
     * @return array
     */
    public function getClams()
    {
        return $this->clams;
    }

    /**
     * @param $type
     * @return boolean
     */
    public function isType($type)
    {
        return $type === $this->getType();
    }
}