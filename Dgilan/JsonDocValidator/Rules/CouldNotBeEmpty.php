<?php
/**
 * Could be empty rule
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
 * Could be empty rule
 *
 * Means that the array could be empty
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class CouldNotBeEmpty extends RuleAbstract
{
    /**
     * Returns rule name
     *
     * @return string
     */
    public static function getName()
    {
        return 'couldNotBeEmpty';
    }

    /**
     * Validates the node
     *
     * @param \Dgilan\JsonDocValidator\Node $node  Node to be validated
     */
    public function validate(Node $node)
    {
        if (($node->getType() === 'array') && ($this->getRuleValue())) {
            if (!count($node->getNodes())) {
                $node->setError('The field could not be empty');
            }
        }
    }
}
