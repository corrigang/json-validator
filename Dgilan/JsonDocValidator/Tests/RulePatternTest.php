<?php
namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Rules\Pattern;

class RulePatternTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidation($target, $pattern, $isValidate)
    {
        $inputString = <<<EOD
{"response":"abracadabra","rows":"abra 4444J", "code":200, "cdoc":"ggg", "newOne":0}
EOD;
        $node        = new Node(json_decode($inputString));
        $rule        = new Pattern();
        $rule->setRuleValue($pattern);

        $targetNode = $node->getNode($target);
        $targetNode->addRule($rule);
        $targetNode->validate();
        $this->assertEquals($isValidate, count($targetNode->getErrors()) === 0, 'Pattern does not match');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array('response', '\w+', true),
            array('rows', '\w+', false),
            array('code', '\d+', true),
            array('cdoc', '\d+', false),
            array('newOne', '\d+', true),
        );
    }
}
