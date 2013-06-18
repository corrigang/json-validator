<?php
namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Result as ValidationResult;
use Dgilan\JsonDocValidator\Manager;

class ResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provider
     */
    public function testValidationChecking($errors, $expected)
    {
        $node = $this->getMock(
            'Dgilan\JsonDocValidator\Node',
            array('validate', 'getErrors'),
            array(array())
        );
        $node->expects($this->once())->method('getErrors')->will($this->returnValue($errors));
        $node->expects($this->any())->method('getNodes');

        $node->validate();
        $result = new ValidationResult($node);
        $this->assertEquals($expected, $result->isValid());
    }

    public function provider()
    {
        return array(
            array(array(), true),
            array(array('0'), false)
        );
    }

    /**
     * @group cur
     */
    public function testGettingErrorsTree()
    {
        $node1 = $this->getMock(
            'Dgilan\JsonDocValidator\Node',
            array('getErrors', 'getNodes', 'validate'),
            array(array())
        );

        $node2 = $this->getMock(
            'Dgilan\JsonDocValidator\Node',
            array('getErrors', 'validate', 'getNodes'),
            array(array())
        );

        $node3 = $this->getMock(
            'Dgilan\JsonDocValidator\Node',
            array('getErrors', 'validate', 'getNodes'),
            array(array())
        );

        $node4 = $this->getMock(
            'Dgilan\JsonDocValidator\Node',
            array('getErrors', 'validate', 'getNodes'),
            array(array())
        );

        $node1->expects($this->any())->method('getErrors')->will($this->returnValue(array('Node1 error1')));
        $node1->expects($this->any())->method('getNodes')->will(
            $this->returnValue(
                array(
                     'node2' => $node2,
                     'node3' => $node3
                )
            )
        );

        $node2->expects($this->any())->method('getErrors')->will($this->returnValue(array('Node2 error2')));
        $node2->expects($this->any())->method('getNodes')->will($this->returnValue(array()));

        $node3->expects($this->any())->method('getErrors')->will($this->returnValue(array()));
        $node3->expects($this->any())->method('getNodes')->will($this->returnValue(array('node4' => $node4)));

        $node4->expects($this->any())->method('getErrors')->will($this->returnValue(array()));
        $node4->expects($this->any())->method('getNodes')->will($this->returnValue(array()));

        $result = new ValidationResult($node1);

        $expected = array('Node1 error1', 'node2' => array('Node2 error2'));
        $this->assertEquals($expected, $result->getErrors());
    }
}
