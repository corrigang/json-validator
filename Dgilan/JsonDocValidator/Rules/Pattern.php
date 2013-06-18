<?php
/**
 * Pattern rule
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
use Dgilan\JsonDocValidator\Exception;

/**
 * Pattern rule
 *
 * Checks that field is satisfied regexp pattern
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Pattern extends RuleAbstract
{
    /**
     * @inheritDoc
     */
    public static function getName()
    {
        return 'pattern';
    }

    /**
     * @inheritDoc
     */
    public function validate(Node $node)
    {
        if (!$pattern = $this->getRuleValue()) {
            throw new Exception('There is no pattern in the "'.$this->getName().'" rule!', 500);
        }

        $checkedValue = $node->getValue();

        if (is_null($checkedValue)) {
            $node->setError('The field does not support "'.$this->getName().'" rule');
        } elseif (!preg_match('/^'.$pattern.'$/', $checkedValue, $match)) {
            $node->setError(
                sprintf(
                    'The field does not match "%s", current value is "%s"',
                    $pattern,
                    $checkedValue
                )
            );
        }
    }
}
