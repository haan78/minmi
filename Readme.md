# Whait is Minmi?
Minmi is a minimalist Web Framework which is written in PHP.

## Examples
#### Creating Default Router
```php
require_once "./lib/Minmi.php";

use \Minmi\DefaultJsonRouter;
use \Minmi\Request;
$router = new DefaultJsonRouter();
```

#### Executing The Router
Execute command should run at the end of the code.
```php
...
$router->execute();
```

#### Simple Request Handling
```php
$router->add("/",function(Request $req){
    return "This is main";
});
$router->add("/login",function(Request $req){
    return "This is login";
});
```

#### Handling HTTP Methods
    $router.add(string [uri],callable [response function], array [methods])
methods array can be countains GET, POST, HEAD, PUT values. Empty means response all.
```php
//The same request with difrent methods.
$router->add("/method",function(Request $req){
    return "This is response via GET method.";
},["GET"]); //GET, POST, HEAD, PUT

$router->add("/method",function(Request $req){
    return "This is response via POST method.";
},["POST"]);
```
Leave the methods an empty array to response all methods with the same function.

#### Handling Request Data
```php
$router->add("/data",function(Request $req){
    return [
        "json_post_data" => $req->json(), //Json Post request payload as an object
        "query_paramters" => $req->query(), //Handle $_GET properties with htmlspecialchars()
    ];
});
```

#### Handling Pathinfo Params
String or Integer parameters are supported. String parameters must start with @ and Integer parameters smust start with #
```php
$router->add("/params/@str/#int",function(Request $req){
    return [
        "str" => $req->params()["str"],
        "int" => $req->params()["int"]
    ];
});
```

#### Getting client agent and remote address
```php
$router->add("/remote",function(Request $req) {
    return [
        "agent" => $req->agent(),
        "remote" => $req->remote()
    ];
});
```

#### Error Hangling
Normal erros rais with the chanching to status 500. Alternatifly custom erros can be raised with diffrent status code.
```php
$router->add("/error_server",function() {
    throw new Exception("This is server error");
});

$router->add("/error_custom",function(Request $req) {
    $req->raise("Unauthorized",401);
});
```

#### Routing Prefix
    Router(string [prefix] = "")
Router class can get URI prefix.  Defautl value is empty string.
```php
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
```

#### Router Base Authentication

```php
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
```


#### Custom Routing
Router is an abstract class. So it can be extened to manipulate the output.
Output method decleration reciaves Response object as a parameter.
    
    abstract protected function output(Response [response]) : void;

In Example HtmlRouter is made from Router abstract class. Additinaly it shows if there is a console output fro debuging.
```php
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
```
## Request Class
#### Methods:

1. **Request->getUriPattern():string** Returns last matched uri pettern.

1. **Request->params():array** Returns uri parametersi whic are indicated in    uri pattern. 
    ```
    Exammle: /@a/#b
    Request->params() = [
        "a" : [a string value]
        "b" : [an integer value]
    ]
    ```

1. **Request->setStatus(int [http status code]): void** Sets http status code.

1. **Request->getStatus():int** Returns current http status (default is 200).

1. **Request->agent():string** Returns client's user agent. see also $_SERVER ["HTTP_USER_AGENT"]

1. **Request->remoute():string** Returns client's remote address by looking $_SERVER["HTTP_X_FORWARDED_FOR","HTTP_X_REAL_IP","REMOTE_ADDR"] variables.

1. **Request->json(): anythink** Returns Json request which is sent in post data.

1. **Request->path(): array of string** Returns pathinfo as array of string
    ```
    patinfo = /a/b/c
    Request->path() = [ "a","b","c" ]

1. **Request->query(): array of string** Returns url query parameters via sterilize html special characters. Sea also htmlspecialchars()

1. **Request->raise(string [message],int status = 500)** Raise a http error exception with http status.

## Response Class
#### Proberties
1. **Response->status:int** Http status code.
1. **Response->error:?Exception** If there is an error this value is an Exception object otherwise it is null.
1. **Response->debug:string** If there is some debuging commands such as echo, print_r or var_dump into the response function. All outputs store in this property.
    For example:
    ```php
    $router->add("/debug",function(){
        echo "This is a console output \n";
        echo "It can be seen by Rersponse->debug property";
    });
    //So then Response->debug will be
    Response->debug = "This is a console output \n It can be seen by Rersponse->debug property";
    ```
    There will be no output in stdin.

1. **Response->result: [anythink]** This is the returned value from response function.
    For example:
    ```php
    $router->add("/return",function(){
        return "This is the return value";
    });
    //So then Response->result will be
    Response->result = "This is the return value";
    ```