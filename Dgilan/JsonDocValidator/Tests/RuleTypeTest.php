<?php
namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Rules\Type;

class RuleTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidation($target, $type, $isValidate)
    {
        $inputString = <<<EOD
{"response":{},"rows":[], "code":200}
EOD;
        $node        = new Node(json_decode($inputString));
        $rule        = new Type();
        $rule->setRuleValue($type);

        $targetNode = $node->getNode($target);
        $targetNode->addRule($rule);
        $targetNode->validate();
        $this->assertEquals($isValidate, count($targetNode->getErrors()) === 0, 'Field type is wrong');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array('response', 'object', true),
            array('rows', 'array', true),
            array('code', 'array', false)
        );
    }
}
