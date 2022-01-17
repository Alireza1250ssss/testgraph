<?php

require_once "../vendor/autoload.php";
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


\App\Route::get('/welcome',[\App\Welcome::class,'index']);
\App\Route::post('/api/get_all_items',[\App\NewsApi::class,'get_all_item']);
\App\Route::post('/api/get_news_item',[\App\NewsApi::class,'get_news_item']);
\App\Route::post('/api/save_news',[\App\NewsApi::class,'save_news']);
\App\Route::post('/api/delete_news',[\App\NewsApi::class,'delete_news']);

\App\Route::resolve($_SERVER['REQUEST_URI'],$_SERVER['REQUEST_METHOD']);