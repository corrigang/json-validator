<?php
/**
 * Rule interface
 *
 * PHP version 5.3
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
namespace Dgilan\JsonDocValidator\Rules;

use Dgilan\JsonDocValidator\Node;

/**
 * Rule interface
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
interface RuleInterface
{
    /**
     * Returns rule name
     *
     * @return string
     */
    public static function getName();

    /**
     * Validates the node
     *
     * @param \Dgilan\JsonDocValidator\Node $node  Node to be validated
     */
    public function validate(Node $node);

    /**
     * The name of validation target
     * It could be child node. By default it is the node itself
     *
     * @param string $target
     *
     * @return Dgilan\JsonDocValidator\Rules\RuleInterface
     */
    public function setValidationTarget($target);

    /**
     * Returns the name of validation target.
     *
     * @return string
     */
    public function getValidationTarget();

    /**
     * Sets the rule attribute value
     *
     * @param mixed $value
     *
     * @return Dgilan\JsonDocValidator\Rules\RuleInterface
     */
    public function setRuleValue($value);

    /**
     * Returns the rule value
     *
     * @return mixed
     */
    public function getRuleValue();

    /**
     * Checks is the rule required for any described field by default
     *
     * @return bool
     */
    public static function isDefault();

    /**
     * Checks is the rule must be applied to parent node with target validation = current node
     *
     * @return bool
     */
    public static function isAppliedToParent();
}
