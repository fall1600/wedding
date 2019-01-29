<?php
namespace Backend\BaseBundle\Tests\Form\Type;

use Backend\BaseBundle\Tests\Fixture\BaseTestCase;
use Backend\BaseBundle\Form\Type\BaseFormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @group unit
 */
class BaseFormTypeTest extends BaseTestCase
{
    public function test___construct()
    {
        //arrange
        $callback = function(){};
        $name = 'some_name';

        //act
        $formType = new BaseFormType($callback, $name);

        //assert
        $this->assertEquals($name, $this->getObjectAttribute($formType, 'name'));
        $this->assertEquals($callback, $this->getObjectAttribute($formType, 'builderCallback'));
    }

    public function test_buildForm()
    {
        //arrange
        $result = array();
        $mockBuilder = $this->getMockBuilder(FormBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $testOption = array(1, 2, 3);
        $mockFormType = $this->getMockBuilder(BaseFormType::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $callback = function(FormBuilderInterface $builder, array $option) use(&$result){
            $result = array($builder, $option);
        };
        $this->setObjectAttribute($mockFormType, 'builderCallback', $callback);

        //act
        $mockFormType->buildForm($mockBuilder, $testOption);

        //assert
        $this->assertEquals($mockBuilder, $result[0]);
        $this->assertEquals($testOption, $result[1]);
    }

    public function test_getName()
    {
        //arrange
        $name = '12345';
        $mockFormType = $this->getMockBuilder(BaseFormType::class)
            ->disableOriginalConstructor()
            ->setMethods()
            ->getMock();
        $this->setObjectAttribute($mockFormType, 'name', $name);

        //act
        $result = $mockFormType->getName();

        //assert
        $this->assertEquals($name, $result);
    }
}