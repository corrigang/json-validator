Json Document Validation Component
==============

1) Loading rules
-----------------
    $validator = new Validator;
    $validator->parseRulesFromJson($json);
    //$validator->parseRulesFromFile($filepath);
    //$validator->parseRulesFromArray($array);

2) Validation
-----------------
    $resultObject = $validator->validate($jsonDocument);

3) Getting result
-----------------
    $resultObject->isValid();

4. Getting errors if exist
-----------------
    if (!$resultObject->isValid()){
       $errors = $resultObject->getErrors();
    }
