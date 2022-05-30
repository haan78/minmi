<?php

require_once "./lib/Minmi.php";

use \Minmi\DefaultJsonRouter;
use \Minmi\Request;

$router = new DefaultJsonRouter("/api");

// pathinfo shuld be /api/userdetail/[user_id]
$router->add("/userdetail/#user_id",function(Request $req){
    return [
        "user_id" => $req->params()["user_id"],
        "name" => "User Name",
        "role" => "admin",
        "imgae" => "[image url]"
    ];
});

$router->execute();
