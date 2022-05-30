<?php

require_once "./lib/Minmi.php";

use \Minmi\DefaultJsonRouter;
use \Minmi\Request;

$router = new DefaultJsonRouter();

$router->auth(function(Request $req) {
    if ($req->getUriPattern() == "/product/delete/#product_id") {
        $req->raise("This method is not allowed",403);
    }
});

$router->add("/product/info/#product_id",function(Request $req){
    return [
        "info"=>"This product is very good!",
        "id" => $req->params()["product_id"]
    ];
});

$router->add("/product/delete/#product_id",function(Request $req){
    return "You can't see this";
});

$router->execute();
