<?php
/**
 * Json Doc Validator
 *
 * PHP version 5.3
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */

namespace Dgilan\JsonDocValidator;

use Symfony\Component\Yaml;

/**
 * Json Doc Validator
 *
 * <b>Using:</b>
 *
 * 1. Loading rules
 * <code>
 *     $validator = new Validator;
 *     $validator->parseRulesFromJson($json);
 *     //$validator->parseRulesFromFile($filepath);
 *     //$validator->parseRulesFromArray($array);
 * </code>
 * 2. Validation
 * <code>
 *     $resultObject = $validator->validate($jsonObject);
 * </code>
 * 3. Getting result
 * <code>
 *    $resultObject->isValid();
 * </code>
 * 4. Getting errors if exist
 * <code>
 *    if (!$resultObject->isValid()){
 *        $errors = $resultObject->getErrors();
 *    }
 * </code>
 *
 * @category   library
 * @package    Dgilan\JsonDocValidator
 * @author     Mikhail Lantukh <lantukhmikhail@gmail.com>
 * @link       https://github.com/dgilan/json-validator
 */
class Validator
{
    protected $manager;

    /**
     * The rule prefix in the config
     *
     * @var string
     */
    protected $rulePrefix = '_';

    /**
     * Sets rule prefix
     *
     * @param string $prefix
     */
    public function setRulePrefix($prefix)
    {
        $this->rulePrefix = $prefix;
    }

    /**
     * Parses validation rules from yaml file
     *
     * @param string $filePath
     *
     * @throws Exception
     */
    public function parseRulesFromFile($filePath)
    {
        if (!$file = stream_resolve_include_path($filePath)) {
            throw new Exception(sprintf('File %s does not exist', $filePath));
        }

        $config        = Yaml\Yaml::parse(file_get_contents($file));
        $this->manager = new Manager($config, $this->rulePrefix);
    }

    /**
     * Parses rules from the array
     *
     * @param $array
     */
    public function parseRulesFromArray($array)
    {
        $this->manager = new Manager($array, $this->rulePrefix);
    }

    /**
     * Parses rules from the json string
     *
     * @param string $json
     *
     * @throws Exception
     */
    public function parseRulesFromJson($json)
    {
        if (!$config = json_decode($json)) {
            throw new Exception('The rulse could not be converted from json', 500);
        }
        $this->manager = new Manager($config, $this->rulePrefix);
    }

    /**
     * Validates the json document according to previously loaded rules
     *
     * @param string $json
     *
     * @return Result
     * @throws Exception
     */
    public function validate($json)
    {
        if (is_null($this->manager)) {
            throw new Exception('You have to set validation rules first!', 500);
        }
        return $this->manager->validate($json);
    }
}
