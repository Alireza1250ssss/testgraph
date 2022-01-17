<?php
require_once "../vendor/autoload.php";

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$queryType = new ObjectType([
    'name'=>'Query',
    'fields'=>[
        'name'=>[
            'type'=>Type::string(),
            'resolve'=> function(){
                return "my test graph";
            }
        ]
    ]
]);

$schema = new \GraphQL\Type\Schema([
   'query'=>$queryType
]);
$entityBody = file_get_contents("php://input");
$inputs = json_decode($entityBody,true);
//var_dump($inputs['query']); die;
$result = \GraphQL\GraphQL::executeQuery($schema,$inputs['query']);
$output = $result->toArray();
echo  json_encode($output);