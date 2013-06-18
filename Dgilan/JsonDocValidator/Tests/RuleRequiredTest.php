<?php

namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Rules\Required;

class RuleRequiredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidation($target, $isValidate)
    {
        $inputString = <<<EOD
{"response":{},"rows":[], "code":200}
EOD;
        $node        = new Node(json_decode($inputString));
        $rule        = new Required();

        $node->addRule($rule);
        $rule->setValidationTarget($target);
        $node->validate();
        $this->assertEquals($isValidate, count($node->getErrors()) === 0, 'Validation is not correct');
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array('response', true),
            array('missed', false),
            array('rows', true),
            array('code', true)
        );
    }
}
