<?php
/**
 * Validation node
 *
 * PHP version 5.3
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */

namespace Dgilan\JsonDocValidator;

use Dgilan\JsonDocValidator\Rules\RuleInterface;

/**
 * Validation node
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Node
{
    /**
     * The list of child nodes
     *
     * @var array
     */
    protected $nodes = array();

    /**
     * The type of node
     *
     * @var string
     */
    protected $type;

    /**
     * Parent of the node
     *
     * @var null|Node
     */
    protected $parent = null;

    /**
     * The list of validation rules
     *
     * @var array
     */
    protected $rules = array();

    /**
     * The list of validation errors
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Node value. Uses if the type = 'simple'
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor. Creates Node From the json decoded object
     *
     * @param mixed $input
     *
     * @throws Exception
     */
    public function __construct($input)
    {
        $this->type = gettype($input);
        $types      = array('object', 'array');

        if (!in_array($this->type, $types)) {
            $this->value = $input;
        } else {
            foreach ($input as $key => $value) {
                $this->nodes[$key] = new self($value);
                $this->nodes[$key]->setParent($this);
            }
        }
    }

    /**
     * Returns type of the node
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns Node value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets parent of the node
     *
     * @param Node $parent
     */
    public function setParent(Node $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns parent of the node
     *
     * @return \Dgilan\JsonDocValidator\Node|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns root node
     *
     * @return Node
     */
    public function getRoot()
    {
        $current = $this;
        while ($parent = $current->getParent()) {
            $current = $parent;
        }
        return $current;
    }

    /**
     * Returns the list of child nodes
     *
     * @return array
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Returns child node by name
     *
     * @param string $name
     *
     * @return Node|null
     */
    public function getNode($name)
    {
        return isset($this->nodes[$name])?$this->nodes[$name]:null;
    }

    /**
     * Returns
     *
     * @return array|\stdClass
     */
    public function toObject()
    {
        if (!in_array($this->getType(), array('array', 'object'))) {
            $result = $this->getValue();
        } else {
            $isObject = $this->getType() === 'object';
            $result   = $isObject?new \stdClass:array();
            foreach ($this->nodes as $key => $node) {
                $value = $this->nodes[$key]->toObject();
                if ($isObject) {
                    $result->{$key} = $value;
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Returns the first child node
     *
     * @return null|Node
     */
    public function first()
    {
        return count($this->nodes)?$this->nodes[0]:null;
    }

    /**
     * Adds to the node validation rule
     *
     * @param Rules\RuleInterface $rule
     */
    public function addRule(RuleInterface $rule)
    {
        $ruleClass = get_class($rule);
        $name      = $ruleClass::getName();

        if ($target = $rule->getValidationTarget()) {
            $this->rules[$name][$target] = $rule;
        } else {
            $this->rules[$name][] = $rule;
        }
    }

    /**
     * Returns rule by name
     *
     * @param string $name
     *
     * @return null|RuleInterface
     */
    public function getRule($name)
    {
        return isset($this->rules[$name])?$this->rules[$name]:null;
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
     * Validates the node
     */
    public function validate()
    {
        foreach ($this->getRules() as $sortedList) {
            foreach ($sortedList as $rule) {
                $rule->validate($this);
            }
        }
        foreach ($this->getNodes() as $node) {
            $node->validate();
        }
    }

    /**
     * Adds the validation error
     *
     * @param string $msg
     */
    public function setError($msg)
    {
        array_push($this->errors, $msg);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
