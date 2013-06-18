<?php
/**
 * Required rule
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
 * Required rule
 *
 * Means that the field is required
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @subpackage Dgilan\JsonDocValidator\Rules
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Required extends RuleAbstract
{
    /**
     * @inheritDoc
     */
    public static function getName()
    {
        return 'required';
    }

    /**
     * @inheritDoc
     */
    public function validate(Node $node)
    {
        if (!$target = $this->getValidationTarget()) {
            throw new Exception('The "'.$this->getName().'" rule require validation target!', 500);
        }

        $validatedTarget = $node->getNode($target);

        if (empty($validatedTarget)) {
            $node->setError(sprintf('The field "%s" is required', $target));
        }
    }

    /**
     * @inheritDoc
     */
    public static function isDefault()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public static function isAppliedToParent()
    {
        return true;
    }
}
