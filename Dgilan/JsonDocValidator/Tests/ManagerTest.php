<?php
namespace Dgilan\JsonDocValidator\Tests;

use Dgilan\JsonDocValidator\Node;
use Dgilan\JsonDocValidator\Manager;

/**
 * Class ManagerTest
 *
 * @package Dgilan\JsonDocValidator\Tests
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $manager;

    public function setUp()
    {
        $config        = array(
            '_fields' => array(
                'response' => array(
                    '_fields' => array(
                        'status_code'  => array(
                            '_pattern'          => '\d+',
                            '_available_values' => 200
                        ),
                        'record_count' => array(
                            '_pattern' => '\d+'
                        ),
                        'cdoc_count'   => array(
                            '_pattern' => '\d+'
                        )
                    )
                ),
                'rows'     => array(
                    '_required'           => false,
                    '_type'               => 'array',
                    '_could_not_be_empty' => true,
                    '_fields'             => array(
                        'name' => ''
                    )
                )
            )
        );
        $this->manager = new Manager($config, '_');
    }

    public function testParsingRules()
    {
        $parcedRules = array(
            'root' => array(
                'rules'  => array(
                    'required' => true
                ),
                'fields' => array(
                    'response' => array(
                        'rules'  => array(
                            'required' => true
                        ),
                        'fields' => array(
                            'status_code'  => array(
                                'rules'  => array(
                                    'required'        => true,
                                    'pattern'         => '\d+',
                                    'availableValues' => 200
                                ),
                                'fields' => array()
                            ),
                            'record_count' => array(
                                'rules'  => array(
                                    'required' => true,
                                    'pattern'  => '\d+',
                                ),
                                'fields' => array()
                            ),
                            'cdoc_count'   => array(
                                'rules'  => array(
                                    'required' => true,
                                    'pattern'  => '\d+',
                                ),
                                'fields' => array()
                            )
                        )
                    ),
                    'rows'     => array(
                        'rules'  => array(
                            'required'        => false,
                            'type'            => 'array',
                            'couldNotBeEmpty' => true
                        ),
                        'fields' => array(
                            'name' => array(
                                'rules'  => array('required' => true),
                                'fields' => array()
                            )
                        )
                    )
                )
            )
        );
        $this->assertSame($parcedRules, $this->manager->getRules(), 'Parsing of rules config is incorrect');
    }

    public function testSearchingRules()
    {
        $rulesCount = $this->getFilesCount(realpath(__DIR__.'/../Rules/')) - 2;
        $foundRules = count($this->manager->getRulesMap());
        $this->assertEquals($rulesCount, $foundRules, 'The count of found rules is not actual');
    }

    public function testAddingRule()
    {
        $rule = $this->getMock('Dgilan\JsonDocValidator\Rules\RuleInterface');
        $rule::staticExpects($this->once())->method('getName')->will($this->returnValue('test'));
        $rule::staticExpects($this->once())->method('isDefault')->will($this->returnValue(true));
        $this->manager->addRule($rule);
        $this->assertTrue(in_array('test', $this->manager->getDefaultRules()));
    }

    /**
     * @dataProvider provider
     */
    public function testApplyingRules($json, $nodeName, $appliedRules)
    {
        $result = $this->manager->validate($json);
        $node   = $this->getNodeByName($result->getValidatedNode(), $nodeName);
        $rules  = $node->getRules();
        $tmp    = array();
        foreach ($rules as $ruleName => $fields) {
            $tmp[$ruleName] = array_keys($fields);
        }
        $this->assertEquals($appliedRules, $tmp, 'Wrong applied rules for the node');
    }

    public function provider()
    {
        return array(
            array('{}', 'root', array('required' => array('response', 'rows'))),
            array(
                '{"response":{},"rows":[{"name":""}]}',
                'response',
                array('required' => array('status_code', 'record_count', 'cdoc_count'))
            ),
            array(
                '{"response":{},"rows":[{"name":""}]}',
                'rows',
                array('type' => array(0), 'couldNotBeEmpty' => array(0))
            ),
            array(
                '{"response":{"status_code":500},"rows":[{"name":""}]}',
                'status_code',
                array('pattern' => array(0), 'availableValues' => array(0))
            ),
            array(
                '{"response":{"status_code":500},"rows":[{"name":""}]}',
                'name',
                array()
            )
        );
    }

    /**
     * Returns files count in the directory
     *
     * @param string $dir Directory path
     *
     * @return int
     */
    private function getFilesCount($dir)
    {
        $count = 0;
        $d     = opendir($dir);
        while (($file = readdir($d)) !== false) {
            if (!is_dir($file)) {
                $count++;
            }
        }
        closedir($d);
        return $count;
    }

    /**
     * Returns node by node name
     *
     * @param Node   $node Input node for searching
     * @param string $name Name
     *
     * @return null|Node
     */
    private function getNodeByName($node, $name)
    {
        if ($name == 'root') {
            return $node->getRoot();
        }
        $result = null;
        foreach ($node->getNodes() as $nodeName => $child) {
            if ($nodeName === $name) {
                $result = $child;
                break;
            } elseif ($result = $this->getNodeByName($child, $name)) {
                break;
            }
        }
        return $result;
    }
}
