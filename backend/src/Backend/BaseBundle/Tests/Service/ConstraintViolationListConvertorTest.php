<?php
namespace Backend\BaseBundle\Tests\Service;


use Backend\BaseBundle\Service\ConstraintViolationListConvertor;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationListConvertorTest extends BaseTestCase
{
    public function test_toArray_simple()
    {
        //arrange
        $violation0 = new ConstraintViolation('err0', 'err0', array(), null, '[field0]', null);
        $violation1 = new ConstraintViolation('err1', 'err1', array(), null, '[field1]', null);
        $violation2 = new ConstraintViolation('err2', 'err2', array(), null, '[field2]', null);
        $list = new ConstraintViolationList(array($violation0, $violation1, $violation2));
        $constraintViolationListConvertor = new ConstraintViolationListConvertor();

        //act
        $result = $constraintViolationListConvertor->toArray($list);

        //assert
        $this->assertEquals(array(
            'field0' => 'err0',
            'field1' => 'err1',
            'field2' => 'err2',
        ), $result);
    }

    public function test_toArray_complex()
    {
        //arrange
        $violation0 = new ConstraintViolation('err0', 'err0', array(), null, '[list0][field0]', null);
        $violation1 = new ConstraintViolation('err1', 'err1', array(), null, '[list1][field1]', null);
        $violation2 = new ConstraintViolation('err2', 'err2', array(), null, '[list1][field2]', null);
        $list = new ConstraintViolationList(array($violation0, $violation1, $violation2));
        $constraintViolationListConvertor = new ConstraintViolationListConvertor();

        //act
        $result = $constraintViolationListConvertor->toArray($list);

        //assert
        $this->assertEquals(array(
            'list0' => array(
                'field0' => 'err0',
            ),
            'list1' => array(
                'field1' => 'err1',
                'field2' => 'err2',
            ),
        ), $result);
    }

}