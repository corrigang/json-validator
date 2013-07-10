<?php
/**
 * Validation result
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

/**
 * Validation result
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Result
{
    /**
     * Validated node
     *
     * @var Node
     */
    protected $validatedNode;

    /**
     * Constructor
     *
     * @param Node $validatedNode
     */
    public function __construct(Node $validatedNode)
    {
        $this->validatedNode = $validatedNode;
    }

    /**
     * Returns validated node
     *
     * @return Node
     */
    public function getValidatedNode()
    {
        return $this->validatedNode;
    }

    /**
     * Checks is the validation has been done successfully
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isNodeValid($this->validatedNode);
    }

    /**
     * Checks is the Node Valid
     *
     * @param Node $node
     *
     * @return bool
     */
    protected function isNodeValid(Node $node)
    {
        $isValid = !count($node->getErrors());
        if ($isValid) {
            foreach ($node->getNodes() as $node) {
                if (!$isValid = $this->isNodeValid($node)) {
                    break;
                }
            }
        }
        return $isValid;
    }

    /**
     * Returns the list of all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->getNodeErrors($this->validatedNode);
    }

    /**
     * Returns node errors
     *
     * @param Node $node
     *
     * @return array
     */
    protected function getNodeErrors($node)
    {
        $result = array();
        foreach ($node->getErrors() as $error) {
            $result[] = $error;
        }

        foreach ($node->getNodes() as $name => $value) {
            $errors = $this->getNodeErrors($value);
            if (count($errors)) {
                $result[$name] = $this->getNodeErrors($value);
                if ('array' === $node->getType()) {
                    break;
                }
            }
        }
        return $result;
    }
}
