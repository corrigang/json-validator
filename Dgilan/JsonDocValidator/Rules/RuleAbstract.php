<?php
/**
 * Rule Abstract class
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
 * Rule Abstract class
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 * @abstract
 */
abstract class RuleAbstract implements RuleInterface
{
    /**
     * Validation target
     *
     * @var string
     */
    protected $target;

    /**
     * Rule value
     *
     * @var mixed
     */
    protected $value;

    /**
     * The name of validation target
     * It could be child node. By default it is the node itself
     *
     * @param string $target
     *
     * @return Dgilan\JsonDocValidator\Rules\RuleInterface
     */
    public function setValidationTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Returns the name of validation target.
     *
     * @return string
     */
    public function getValidationTarget()
    {
        return $this->target;
    }

    /**
     * Checks is the rule required for any described field by default
     *
     * @return bool
     */
    public static function isDefault()
    {
        return false;
    }

    /**
     * Checks is the rule must be applied to parent node with target validation = current node
     *
     * @return bool
     */
    public static function isAppliedToParent()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setRuleValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRuleValue()
    {
        return $this->value;
    }
}
