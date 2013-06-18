<?php
/**
 * Type rule
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
 * Type rule
 *
 * Checks field's type
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Type extends RuleAbstract
{
    /**
     * Returns rule name
     *
     * @return string
     */
    public static function getName()
    {
        return 'type';
    }

    /**
     * @inheritDoc
     */
    public function validate(Node $node)
    {
        if (!$type = $this->getRuleValue()) {
            throw new Exception('The type must be set in the "'.$this->getName().'" rule!', 500);
        }

        if ($type !== $node->getType()) {
            $node->setError('The field must have a type "'.$type.'"');
        }
    }
}
