<?php

namespace Dgilan\JsonDocValidator\Tests;

use \Dgilan\JsonDocValidator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testValidator($json, $expected)
    {
        $validator = new  Validator;
        $validator->parseRulesFromFile(__DIR__.'/docs/example1.yml');

        $resultObject = $validator->validate($json);
        $this->assertEquals($expected, $resultObject->isValid(), 'Validation is invalid');
    }

    public function provider()
    {
        return array(
            array('{"response":[]}', false),
            array('{"response":{"status_code":500}}', false),
            array('{"response":{"status_code":200,"record_count":4,"cdoc_count":"ggg"},"rows":[]}', false),
            array('{"response":{"status_code":200,"record_count":4,"cdoc_count":6},"rows":[]}', true),
            array('{"response":{"status_code":200,"record_count":4,"cdoc_count":6},"rows":[{}]}', false),
            array(
                '{"response":{"status_code":200,"record_count":4,"cdoc_count":6},
                "rows":[{"name":"test","org_contacts":[],"cinx_id":{"type":"","domain":"","id":""}}]}',
                false
            ),
            array(
                '{"response":{"status_code":200,"record_count":4,"cdoc_count":6},
                "rows":[{"name":"test", "project":{"name":"","number":"","cinx_id":""},
                "vendors":[],"cinx_id":{"type":"","domain":"","id":""}}]}',
                true
            ),
            array(
                '{"response":{"status_code":200,"record_count":4,"cdoc_count":6},
                "rows":[{"name":"test", "project":{"name":"","number":"","cinx_id":""},
                "vendors":{},"cinx_id":{"type":"","domain":"","id":""}}]}',
                false
            ),
        );
    }
}
