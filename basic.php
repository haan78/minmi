<?php
date_default_timezone_set('Europe/Istanbul');
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once "./lib/Minmi.php";

use \Minmi\DefaultJsonRouter;
use \Minmi\Request;

$router = new DefaultJsonRouter();
/**************** Simple Request Handling ***************/
$router->add("/",function(Request $req){
    return "This is main";
});
$router->add("/login",function(Request $req){
    return "This is login";
});

/*************** Handling HTTP Methods ******************/

//The same request with difrent methods.
$router->add("/method",function(Request $req){
    return "This is response via GET method.";
},["GET"]); //GET, POST, HEAD, PUT

$router->add("/method",function(Request $req){
    return "This is response via POST method.";
},["POST"]);

//Leave the method array empty to response all.


/*************** Handling Request Data ******************/
$router->add("/data",function(Request $req){
    return [
        "json_post_data" => $req->json(),
        "query_paramters" => $req->query(), //Handle $_GET properties with htmlspecialchars()
    ];
});

/*************** Handling Patinfo Params *****************/
$router->add("/params/@str/#int",function(Request $req){
    return [
        "str" => $req->params()["str"],
        "int" => $req->params()["int"]
    ];
});

/*************** Getting agent and remote address **********/
$router->add("/remote",function(Request $req) {
    return [
        "agent" => $req->agent(),
        "remote" => $req->remote()
    ];
});

/*************** Error Hangling ***************************/
$router->add("/error_server",function() {
    throw new Exception("This is server error");
});

$router->add("/error_custom",function(Request $req) {
    $req->raise("Unauthorized",401);
});

$router->execute();
