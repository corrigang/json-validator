<?php
/**
 * Validation rules manager
 *
 * PHP version 5.3
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */

namespace Dgilan\JsonDocValidator;

/**
 * Validation rules manager
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Manager
{
    /**
     * The list of found in the system rules
     *
     * @var array
     */
    protected static $rulesMap;

    /**
     * Parsed rules
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Constructor
     *
     * @param array  $config
     * @param string $prefix
     */
    public function __construct($config, $prefix)
    {
        $tmp                 = !isset($config['_fields'])?array('_fields' => $config):$config;
        $this->rules['root'] = $this->parseRuleConfig($tmp, $prefix);
    }

    /**
     * Parses rules config and prepare Manager to the validation of json objects
     *
     * @param array|string $config Rules config. Could be either array either string. The second option means
     *                             that the field must contain only default rules
     * @param string       $prefix Rule prefix
     * @param array|null   $defaultRules
     *
     * @return array
     */
    protected function parseRuleConfig($config, $prefix, $defaultRules = null)
    {
        $result = array('rules' => array(), 'fields' => array());
        if (is_null($defaultRules)) {
            $defaultRules = $this->getDefaultRules();
        }

        foreach ($defaultRules as $rule) {
            $result['rules'][$rule] = true;
        }

        if (is_array($config)) {
            if (isset($config[$prefix.'fields'])) {
                $fields = $config[$prefix.'fields'];
                unset($config[$prefix.'fields']);
            } else {
                $fields = array();
            }

            foreach ($config as $key => $value) {
                if (strpos($key, $prefix) === 0) {
                    $result['rules'][$this->getRuleName(substr($key, strlen($prefix)))] = $value;
                }
            }

            foreach ($fields as $name => $field) {
                $result['fields'][$name] = $this->parseRuleConfig($field, $prefix, $defaultRules);
            }
        }

        return $result;
    }

    /**
     * Converts rule name like rule_name to ruleName
     *
     * @param string $rule
     *
     * @return string
     */
    private function getRuleName($rule)
    {
        $parts = explode('_', $rule);
        $first = array_shift($parts);
        foreach ($parts as &$part) {
            $part = ucfirst($part);
        }
        return $first.implode($parts);
    }

    /**
     * Returns the map of rules
     *
     * @return array
     */
    public function getRulesMap()
    {
        if (is_null(self::$rulesMap)) {
            self::$rulesMap = array();

            $d = opendir(__DIR__.'/Rules');
            while (($file = readdir($d)) !== false) {
                if (!is_dir($file)) {
                    $class = 'Dgilan\\JsonDocValidator\\Rules\\'.str_replace('.php', '', $file);
                    if (class_exists($class)) {
                        $reflection = new \ReflectionClass($class);
                        $isRule     = $reflection->implementsInterface(
                            'Dgilan\\JsonDocValidator\\Rules\\RuleInterface'
                        );
                        if (!$reflection->isAbstract() && $isRule) {
                            self::$rulesMap[$class::getName()] = $class;
                        }
                    }
                }
            }
            closedir($d);
        }
        return self::$rulesMap;
    }

    /**
     * Returns the list of default rules
     *
     * @return array
     */
    public function getDefaultRules()
    {
        $result = array();
        foreach ($this->getRulesMap() as $name => $class) {
            if ($class::isDefault()) {
                array_push($result, $name);
            }
        }
        return $result;
    }

    /**
     * Returns the rules list
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Adds the rule class to the rules map
     *
     * @param string|object $class Rule class or instance of the rule
     *
     * @throws Exception
     */
    public function addRule($class)
    {
        if (!is_object($class) && !class_exists($class)) {
            throw new Exception(sprintf('Class "%s" was not found', $class), 500);
        }
        $reflection = new \ReflectionClass(is_object($class)?get_class($class):$class);
        if (!$reflection->implementsInterface(
            'Dgilan\\JsonDocValidator\\Rules\\RuleInterface'
        )
        ) {
            throw new Exception("The rule must implement RuleInterface");
        }

        $rules                    = $this->getRulesMap();
        $rules[$class::getName()] = $class;
        self::$rulesMap           = $rules;
    }

    /**
     * Validates the json document and returns the Result object
     *
     * @param string $json
     *
     * @return Result
     * @throws Exception
     */
    public function validate($json)
    {
        if (!$input = json_decode($json)) {
            throw new Exception('The object could not be converted from json', 500);
        }
        $node  = new Node($input);

        $rules = $this->getRules();
        foreach ($rules['root']['fields'] as $fieldName => $fieldRules) {
            $this->applyRulesToNode($fieldRules, $node, $fieldName);
        }

        $node->validate();
        return new Result($node);
    }

    /**
     * Applies rules to the node
     *
     * @param array      $rules        List of rules
     * @param Node       $parentNode   Parent node
     * @param string     $nodeName     Node name to be adapt to the rules
     * @param null|array $defaultRules List of default rules
     */
    protected function applyRulesToNode($rules, Node $parentNode, $nodeName, $defaultRules = null)
    {
        if (is_null($defaultRules)) {
            $defaultRules = $this->getDefaultRules();
        }

        $map = $this->getRulesMap();
        if ('array' === $parentNode->getType()) {
            foreach ($parentNode->getNodes() as $node) {
                $this->applyRulesToNode($rules, $node, $nodeName, $defaultRules);
            }
            return;
        }
        $node = $parentNode->getNode($nodeName);

        foreach ($rules['rules'] as $ruleName => $ruleValue) {
            if (isset($map[$ruleName])) {
                $ruleClass = $map[$ruleName];
                $rule      = new $ruleClass;

                if (!is_null($ruleValue)) {
                    $rule->setRuleValue($ruleValue);
                }
                if ($ruleClass::isAppliedToParent()) {
                    $rule->setValidationTarget($nodeName);
                    $parentNode->addRule($rule);
                } elseif (!is_null($node)) {
                    $node->addRule($rule);
                }
            }
        }

        if (!is_null($node)) {
            foreach ($rules['fields'] as $fieldName => $fieldRules) {
                $this->applyRulesToNode($fieldRules, $node, $fieldName, $defaultRules);
            }
        }
    }
}
