<?php

namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider nodeProvider
     */
    public function testNodeCreation($inputString)
    {
        $node = new Node(json_decode($inputString));
        $this->assertEquals(
            array('response', 'rows'),
            array_keys($node->getNodes()),
            'Node parsing error: wrong nodes'
        );
        $response = $node->getNode('response');
        $this->assertSame($node, $response->getParent(), 'Something wrong with parent search');

        $rows = $node->getNode('rows');
        $this->assertEquals('array', $rows->getType(), 'Wrong Node Type');
    }

    /**
     * @dataProvider nodeProvider
     */
    public function testGettingRoot($inputString)
    {
        $node  = new Node(json_decode($inputString));
        $child = $node->getNode('rows')->first()->getNode('tx_dates')->first();
        $this->assertSame($node, $child->getRoot(), 'Wrong root node found');
    }

    /**
     * @dataProvider nodeProvider
     */
    public function testConvertingToObject($inputString)
    {
        $node = new Node(json_decode($inputString));
        $this->assertEquals(json_decode($inputString), $node->toObject(), 'Convert to object works incorrectly');
    }

    public function testValidation()
    {
        $node = $this->getMock('Dgilan\JsonDocValidator\Node', array('getRules', 'getNodes'), array(null));

        $rule = $this->getMock('Dgilan\JsonDocValidator\Rules\RuleInterface');
        $rule->expects($this->once())->method('validate')->with($this->equalTo($node));

        $node->expects($this->once())->method('getRules')->will($this->returnValue(array('test' => array($rule))));
        $node2 = $this->getMock('Dgilan\JsonDocValidator\Node', array('validate'), array(null));
        $node->expects($this->once())->method('getNodes')->will($this->returnValue(array('node2' => $node2)));

        $node2->expects($this->once())->method('validate');

        $node->validate();
    }


    /**
     * Node Provider
     *
     * @return array
     */
    public function nodeProvider()
    {
        $node = <<<EOD
{"response":{"status_code":200,"message":"OK","record_count":10,"cdoc_count":10},
"rows":[{"name":"test34","description":null, "project":{"name":null,"number":null},
"tx_dates":[{"type":"SUBMITTED","value":"2013-06-17"}]}]}
EOD;

        return array(
            array('node' => $node)
        );
    }
}
