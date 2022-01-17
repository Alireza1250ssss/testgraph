<?php

function check_repeat($title)
{
    $connection = new \PDO('mysql:host=localhost;dbname=codeignter_test','root','');
    $sql = "SELECT * FROM news WHERE title=".'"'.$title.'"';
    $result = $connection->query($sql);
    $result = $result->fetchAll(PDO::FETCH_ASSOC);
    return $result!=null;
}