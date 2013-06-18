<?php
/**
 * Available Values rule
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
 * Available Values rule
 *
 * Checks the set of available values
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class AvailableValues extends RuleAbstract
{
    /**
     * Returns rule name
     *
     * @return string
     */
    public static function getName()
    {
        return 'availableValues';
    }

    /**
     * @inheritDoc
     */
    public function validate(Node $node)
    {
        if (!$set = $this->getRuleValue()) {
            throw new Exception('There is no list of values in the "'.$this->getName().'" rule!', 500);
        }

        $set          = explode(',', $set);
        $checkedValue = $node->getValue();

        if (is_null($checkedValue)) {
            $node->setError('The field is null, so it does not support "'.$this->getName().'" rule');
        } elseif (!in_array($checkedValue, $set)) {
            $node->setError(
                sprintf(
                    'The field "%s" is not available',
                    $checkedValue
                )
            );
        }
    }
}
