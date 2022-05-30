<?php
require_once "./lib/Minmi.php";

use \Minmi\Router;
use \Minmi\Request;
use \Minmi\Response;

class HtmlRouter extends Router {
    protected function output(Response $response) : void {
        echo "<h1>Status : $response->status</h1>";
        if ( !is_null($response->error) ) { //This means there is an error
            echo "<h2>There is an error</h2>";
            echo $response->error->getMessage();
        } else {
            echo "<h2>Result is</h2>";
            echo "<pre>\n";
            echo json_encode($response->result,JSON_PRETTY_PRINT);
            echo "\n<pre>";
        }
        
        /* If there are some debugin outputs. */
        if (!empty($response->debug)) {              
            echo "<h2>Debug Outputs</h2>";
            echo "<pre>\n";
            echo $response->debug;
            echo "\n<pre>";
        }
    }
}

$htmlrouter = new HtmlRouter();

$htmlrouter->add("/server",function(Request $req) {
    return [
        "IP"=>$req->remote(),
        "AGENT" => $req->agent()
    ];
});

$htmlrouter->add("/error",function(Request $req) {
    throw new Exception("This is an Error");
});

$htmlrouter->add("/debug",function(Request $req) {
    echo "This is degub output 1\n";
    echo "This is degub output 2\n";
    echo "This is degub output 3\n";
    return true;
});


$htmlrouter->execute();
