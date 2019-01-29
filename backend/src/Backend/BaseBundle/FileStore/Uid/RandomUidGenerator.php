<?php
namespace Backend\BaseBundle\FileStore\Uid;

class RandomUidGenerator implements UidGeneratorInterface
{
    protected $seed;

    public function __construct($seed = null)
    {
        $this->seed = $seed;
    }

    public function generate()
    {
        $rawMD5 = md5(microtime().'-'.rand().'-'.rand()."-{$this->seed}", true);
        return str_replace(array('+', '/', '='), array('_', ',', ''), base64_encode($rawMD5));
    }
}