<?php

require_once "../vendor/autoload.php";

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

$connection = new PDO('mysql:host=localhost;dbname=codeignter_test','root','');
$sql = "SELECT * FROM news WHERE slug='title45-slug'";
$statement = $connection->query($sql);
$result = $statement->fetch(PDO::FETCH_ASSOC);
//var_dump($result); die;


$news_item_type = new ObjectType([
    'name'=>'news_item',
    'fields'=>[
        'title'=>[
            'type'=>Type::string(),
            'resolve'=>function($news_item){
                return $news_item['title'];
            }
        ],
        'text'=>[
            'type'=>Type::string(),
            'resolve'=>function($news_item){
                return $news_item['text'];
            }
        ]
    ]
]);

$queryType = new ObjectType([
   'name'=>'query',
   'fields'=>[
       'news_items'=>[
           'type'=>$news_item_type,
           'args'=>[
               'slug'=>Type::nonNull(Type::string())
           ],
           'resolve'=>function($rootValue,$args){
               $connection = new PDO('mysql:host=localhost;dbname=codeignter_test','root','');
               $sql = 'SELECT * FROM news WHERE slug='.'"'.$args['slug'].'"';
               $statement = $connection->query($sql);
               $result = $statement->fetch(PDO::FETCH_ASSOC);
               return $result;
           }
       ]
   ]
]);



$schema = new \GraphQL\Type\Schema([
    'query'=>$queryType,
    'News_item'=>$news_item_type
]);

$entityBody = file_get_contents("php://input");
$inputs = json_decode($entityBody,true);
$output = \GraphQL\GraphQL::executeQuery($schema,$inputs['query']);
$output=$output->toArray();
echo  json_encode($output);
