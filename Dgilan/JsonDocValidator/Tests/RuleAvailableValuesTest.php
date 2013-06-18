<?php
namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Rules\AvailableValues;

class RuleAvailableValuesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidation($target, $list, $isValidate)
    {
        $inputString = <<<EOD
{"response":"hi","rows":"abra", "code":200}
EOD;
        $node        = new Node(json_decode($inputString));
        $rule        = new AvailableValues();
        $rule->setRuleValue($list);

        $targetNode = $node->getNode($target);
        $targetNode->addRule($rule);
        $targetNode->validate();
        $this->assertEquals($isValidate, count($targetNode->getErrors()) === 0, 'The list value is wrong');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array('response', 'hi,by', true),
            array('rows', 'hi,by', false),
            array('code', '200,201,203', true)
        );
    }
}
