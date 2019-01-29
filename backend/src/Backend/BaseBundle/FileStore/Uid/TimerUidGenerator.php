<?php
namespace Backend\BaseBundle\FileStore\Uid;

class TimerUidGenerator implements UidGeneratorInterface
{
    public function generate()
    {
        list($usec, $sec) = explode(' ', microtime());
        return sprintf('%d%03d-%05d%05d%05d%05d', $sec, floor($usec*1000), rand(0, 99999), rand(0, 99999), rand(0, 99999), rand(0, 99999));
    }
}