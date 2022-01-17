<?php

namespace App;

use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

class NewsApi
{
    public $connection;
    public $news_item_type;


    public function __construct()
    {
        $this->connection = new \PDO('mysql:host=localhost;dbname=codeignter_test','root','');
        $this->news_item_type=new ObjectType([
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


    }

    public function get_all_item()
    {


        $queryType = new ObjectType([
            'name'=>'query',
            'fields'=>[
                'news_items'=>[
                    'type'=>Type::listOf($this->news_item_type),
                    'resolve'=>function($rootValue,$args){
                        $sql = 'SELECT * FROM news';
                        $statement = $this->connection->query($sql);
                        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
//                        var_dump($result); die;
                        return $result;
                    }
                ]
            ]
        ]);

        $schema = new \GraphQL\Type\Schema([
            'query'=>$queryType
        ]);
        $entityBody = file_get_contents("php://input");
        $inputs = json_decode($entityBody,true);
        $output = \GraphQL\GraphQL::executeQuery($schema,$inputs['query']);
        $output=$output->toArray();
        echo  json_encode($output);
    }

    public function get_news_item()
    {

        $queryType = new ObjectType([
            'name'=>'query',
            'fields'=>[
                'news_items'=>[
                    'type'=>$this->news_item_type,
                    'args'=>[
                        'slug'=>Type::nonNull(Type::string())
                    ],
                    'resolve'=>function($rootValue,$args){
                        $sql = 'SELECT * FROM news WHERE slug='.'"'.$args['slug'].'"';
                        $statement = $this->connection->query($sql);
                        $result = $statement->fetch(\PDO::FETCH_ASSOC);
                        return $result;
                    }
                ]
            ]
        ]);

        $schema = new \GraphQL\Type\Schema([
            'query'=>$queryType
        ]);

        $entityBody = file_get_contents("php://input");
        $inputs = json_decode($entityBody,true);
        $output = \GraphQL\GraphQL::executeQuery($schema,$inputs['query']);
        $output=$output->toArray();
        echo  json_encode($output);
    }

    public function save_news()
    {
//        die('salam');
        $mutationType = new ObjectType([
           'name'=>'mutation',
           'fields'=>[
               'news_item'=>[
                   'type'=>Type::string(),
                   'args'=>[
                       'title'=>Type::nonNull(Type::string()),
                       'text'=>Type::nonNull(Type::string())
                   ],
                   'resolve'=>function($rootValue,$args){
                        if(check_repeat($args['title']))
                            return "the entry was repeated";
                        $sql = 'INSERT INTO news(title,slug,text,created_at,updated_at) VALUES(:title,:slug,:text,:created_at,:updated_at)';
                        $statement = $this->connection->prepare($sql);
                        $statement->bindParam(":title",$args['title']);
                        $statement->bindParam(":text",$args['text']);
                        $slug = $args['title'].'-slug';
                        $statement->bindParam(":slug",$slug);
                        $time  =time();
                        $statement->bindParam(":created_at",$time);
                        $statement->bindParam(":updated_at",$time);
                        $statement->execute();
                        return $this->connection->lastInsertId();
                   }
               ]
           ]
        ]);
        $schema = new \GraphQL\Type\Schema([
            'mutation'=>$mutationType
        ]);

        $entityBody = file_get_contents("php://input");
        $inputs = json_decode($entityBody,true);
        $output = \GraphQL\GraphQL::executeQuery($schema,$inputs['mutation']);
        $output=$output->toArray();

        echo  json_encode($output);
    }

    public function delete_news()
    {
//        die('salameykm');
        $mutationType = new ObjectType([
           'name'=>'mutation',
           'fields'=>[
               'news_item'=>[
                   'type'=>Type::string(),
                   'args'=>[
                       'slug'=>Type::nonNull(Type::string())
                   ],
                   'resolve'=>function($rootValue,$args){
                        $sql = "DELETE FROM news WHERE slug="."'".$args['slug']."'";
                        $stmt = $this->connection->query($sql);
                        return $rootValue['prefix'].$args['slug'];
                   }

               ]
           ]
        ]);
        $schema = new Schema([
           'mutation'=>$mutationType
        ]);
        $entityBody = file_get_contents("php://input");
        $inputs = json_decode($entityBody,true);
        $rootValue['prefix']='successfully deleted news with slug : ';
        $output = \GraphQL\GraphQL::executeQuery($schema,$inputs['mutation'],$rootValue);
        $output=$output->toArray();

        echo  json_encode($output);
    }

}