<?php

require_once "../vendor/autoload.php";

use GraphQL\GraphQL;
use GraphQL\Type\Schema;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$queryType = new ObjectType([
    'name' => 'Query',
    'fields' => [
        'returned_value' => [
            'type' => Type::string(),
            'args' => [
                'message' => Type::nonNull(Type::string()),
            ],
            'resolve' => function ($rootValue, $args) {
                return $rootValue['prefix'] . $args['message'];
            }
        ],
    ],
]);


$schema = new Schema([
    'query' => $queryType
]);

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);
$query = $input['query'];
$variableValues = isset($input['variables']) ? $input['variables'] : null;
//var_dump($variableValues,$query); die;
try {
    $rootValue = ['prefix' => 'You said: '];
    $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
    $output = $result->toArray();
} catch (\Exception $e) {
    $output = [
        'errors' => [
            [
                'message' => $e->getMessage()
            ]
        ]
    ];
}
header('Content-Type: application/json');
echo json_encode($output);
