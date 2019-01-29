<?php
namespace Backend\BaseBundle\Tests\Service;


use Backend\BaseBundle\Service\CaseConvertService;
use Backend\BaseBundle\Tests\Fixture\BaseKernelTestCase;

class CaseConvertServiceTest extends BaseKernelTestCase
{
    public function test_convert()
    {
        //arrange
        $column = 'order_status';
        //act
        $caseConvert = new CaseConvertService();
        $result = $caseConvert->convert($column);
        //assert
        $this->assertEquals('OrderStatus', $result);
    }

    public function test_convert_not_change()
    {
        //arrange
        $column = 'OrderStatus';
        //act
        $caseConvert = new CaseConvertService();
        $result = $caseConvert->convert($column);
        //assert
        $this->assertEquals('OrderStatus', $result);
    }

    public function test_convert_bad_format()
    {
        //arrange
        $column = 'Order__Status';
        //act
        $caseConvert = new CaseConvertService();
        $result = $caseConvert->convert($column);
        //assert
        $this->assertEquals('OrderStatus', $result);
    }

    public function test_convert_other_format()
    {
        //arrange
        $column = 'dfgdfjkgjdfklg';
        //act
        $caseConvert = new CaseConvertService();
        $result = $caseConvert->convert($column);
        //assert
        $this->assertEquals('Dfgdfjkgjdfklg', $result);
    }
}