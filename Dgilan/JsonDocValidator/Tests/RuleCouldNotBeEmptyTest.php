<?php
namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Rules\CouldNotBeEmpty;

class RuleCouldNotBeEmptyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidation($target, $ruleValue, $isValidate)
    {
        $inputString = <<<EOD
{"response":{},"rows":[], "code":[]}
EOD;
        $node        = new Node(json_decode($inputString));
        $rule        = new CouldNotBeEmpty();
        $rule->setRuleValue($ruleValue);

        $targetNode = $node->getNode($target);
        $targetNode->addRule($rule);
        $targetNode->validate();
        $this->assertEquals($isValidate, count($targetNode->getErrors()) === 0);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array('response', false, true),
            array('rows', true, false),
            array('code', false, true)
        );
    }
}
