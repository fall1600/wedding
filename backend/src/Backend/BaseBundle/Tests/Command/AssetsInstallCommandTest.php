<?php
namespace Backend\BaseBundle\Tests\Command;

use Backend\BaseBundle\Command\AssetsInstallCommand;
use Backend\BaseBundle\Tests\Fixture\BaseTestCase;

class AssetsInstallCommandTest extends BaseTestCase
{
    public function test_numericArrayMerge()
    {
        //arrange
        $a = array(
            "10" => array(
                "a" => 'a',
                'b' => 'b',
            ),
            "20" => array(
                'a' => 'a',
                'b' => 'b',
            ),
        );

        $b = array(
            "10" => array(
                "c" => 'a',
                'd' => 'b',
            ),
            "20" => array(
                'e' => 'a',
                'f' => 'b',
            ),
        );

        $output = array(
            "10" => array(
                "a" => 'a',
                'b' => 'b',
                "c" => 'a',
                'd' => 'b',
            ),
            "20" => array(
                'a' => 'a',
                'b' => 'b',
                'e' => 'a',
                'f' => 'b',
            ),
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'numericArrayMerge', $a, $b);

        //assert
        $this->assertEquals($output, $result);
    }

    public function test_numericArrayMerge_origin_index_not_exists()
    {
        //arrange
        $a = array(
            "20" => array(
                'a' => 'a',
                'b' => 'b',
            ),
        );

        $b = array(
            "10" => array(
                "c" => 'a',
                'd' => 'b',
            ),
            "20" => array(
                'e' => 'a',
                'f' => 'b',
            ),
        );

        $output = array(
            "10" => array(
                "c" => 'a',
                'd' => 'b',
            ),
            "20" => array(
                'a' => 'a',
                'b' => 'b',
                'e' => 'a',
                'f' => 'b',
            ),
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'numericArrayMerge', $a, $b);

        //assert
        $this->assertEquals($output, $result);
    }

    public function test_numericArrayMerge_origin_has_other_item()
    {
        //arrange
        $a = array(
            "20" => array(
                'a' => 'a',
                'b' => 'b',
            ),
            "30" => array(
                'a' => 'a',
                'b' => 'b',
            ),
        );

        $b = array(
            "10" => array(
                "c" => 'a',
                'd' => 'b',
            ),
            "20" => array(
                'e' => 'a',
                'f' => 'b',
            ),
        );

        $output = array(
            "10" => array(
                "c" => 'a',
                'd' => 'b',
            ),
            "20" => array(
                'a' => 'a',
                'b' => 'b',
                'e' => 'a',
                'f' => 'b',
            ),
            "30" => array(
                'a' => 'a',
                'b' => 'b',
            ),
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'numericArrayMerge', $a, $b);

        //assert
        $this->assertEquals($output, $result);
    }


    public function test_numericArrayMerge_deep_merge()
    {
        //arrange
        $a = array(
            "10" => array(
                'a' => array(
                    'b' => array(
                        'c' => 'c',
                        'd' => 'd',
                    ),
                    'c' => 'test',
                ),
                'b' => 'test',
                'c' => 'test'
            ),
        );

        $b = array(
            "10" => array(
                'a' => array(
                    'b' => array(
                        'e' => 'e',
                        'f' => 'f',
                    ),
                    'c' => 'test2',
                ),
                'b' => 'test2',
                'c' => 'test2'
            ),
        );

        $output = array(
            "10" => array(
                'a' => array(
                    'b' => array(
                        'c' => 'c',
                        'd' => 'd',
                        'e' => 'e',
                        'f' => 'f',
                    ),
                    'c' => array(
                        'test',
                        'test2',
                    )
                ),
                'b' => array(
                    'test',
                    'test2',
                ),
                'c' => array(
                    'test',
                    'test2',
                ),
            ),
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'numericArrayMerge', $a, $b);

        //assert
        $this->assertEquals($output, $result);
    }

    public function test_deepArrayKeySort()
    {
        //arrange
        $a = array(
            "100" => array(
                'a',
                "5" => array(5),
                "10" => array(10),
                "4" => array(4),
            ),
            "10" => array(
                'b',
                "4" => array(4),
                "6" => array(6),
                "5" => array(5),
            ),
            "20" => array(
                'c',
                "5" => array(5),
                "1" => array(1),
                "4" => array(4),
            ),
            "1" => array(
                'd',
                "5" => array(5),
                "1" => array(1),
                "40" => array(40),
            ),
        );

        $output = array(
            array(
                array(10),
                array(5),
                array(4),
                'a',
            ),
            array(
                array(5),
                array(4),
                array(1),
                'c',
            ),
            array(
                array(6),
                array(5),
                array(4),
                'b',
            ),
            array(
                array(40),
                array(5),
                array(1),
                'd',
            ),
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'deepArrayKeySort', $a);

        //assert
        $this->assertEquals($output, $result);
    }

    public function test_deepArrayKeySort_preserve_associative_key()
    {
        //arrange
        $a = array(
            "100" => array(
                "a" => "a",
                'a',
                "5" => array(5),
                "10" => array(10),
                "4" => array(4),
            ),
            "10" => array(
                "b" => "b",
                'b',
                "4" => array(4),
                "6" => array(6),
                "5" => array(5),
            ),
            "20" => array(
                "c" => "c",
                'c',
                "5" => array(5),
                "1" => array(1),
                "4" => array(4),
            ),
            "1" => array(
                "d" => "d",
                'd',
                "5" => array(5),
                "1" => array(1),
                "40" => array(40),
            ),
            "e" => "e",
        );

        $output = array(
            array(
                array(10),
                array(5),
                array(4),
                'a',
                "a" => "a",
            ),
            array(
                array(5),
                array(4),
                array(1),
                'c',
                "c" => "c",
            ),
            array(
                array(6),
                array(5),
                array(4),
                'b',
                "b" => "b",
            ),
            array(
                array(40),
                array(5),
                array(1),
                'd',
                "d" => "d",
            ),
            "e" => "e",
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'deepArrayKeySort', $a);

        //assert
        $this->assertEquals($output, $result);
    }

    public function test_deepArrayKeySort_preserve_associative_key_with_associative_key_sort()
    {
        //arrange
        $a = array(
            "100" => array(
                "z" => "z",
                "a" => "a",
                'a',
                "5" => array(5),
                "10" => array(10),
                "4" => array(4),
            ),
            "10" => array(
                "b" => "b",
                "z" => "z",
                'b',
                "4" => array(4),
                "6" => array(6),
                "5" => array(5),
            ),
            "20" => array(
                "z" => "z",
                "c" => "c",
                'c',
                "5" => array(5),
                "1" => array(1),
                "4" => array(4),
            ),
            "1" => array(
                "d" => "d",
                "z" => "z",
                'd',
                "5" => array(5),
                "1" => array(1),
                "40" => array(40),
            ),
            "e" => "e",
            "z" => "z",
        );

        $output = array(
            array(
                array(10),
                array(5),
                array(4),
                'a',
                "z" => "z",
                "a" => "a",
            ),
            array(
                array(5),
                array(4),
                array(1),
                'c',
                "z" => "z",
                "c" => "c",
            ),
            array(
                array(6),
                array(5),
                array(4),
                'b',
                "z" => "z",
                "b" => "b",
            ),
            array(
                array(40),
                array(5),
                array(1),
                'd',
                "z" => "z",
                "d" => "d",
            ),
            "z" => "z",
            "e" => "e",
        );

        $command = $this->getMockBuilder(AssetsInstallCommand::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        //act
        $result = $this->callObjectMethod($command, 'deepArrayKeySort', $a);

        //assert
        $this->assertEquals($output, $result);
    }
}