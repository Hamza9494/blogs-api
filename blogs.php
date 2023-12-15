 <?php

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header('Content-Type: application/json');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == "OPTIONS") {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
        header("HTTP/1.1 200 OK");
        die();
    }



    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;


    require __DIR__ . "/vendor/autoload.php";

    spl_autoload_register(function ($class) {
        require __DIR__ . "/src/$class.php";
    });
    $parts = explode('/', $_SERVER['REQUEST_URI']);

    if ($parts[3] !== 'blogs.php') {
        die('nothing for u here my dude');
    }

    $user_credentials = json_decode(file_get_contents("php://input"), true);

    $headers = getallheaders();


    $user_token = explode("Bearer ", $headers['Authorization']);

    $jwt = $user_token[1];


    $key = "mykey2010";

    try {
        $decoded = JWT::decode($jwt, new Key($key, "HS256"));
        $decoded = (array) $decoded;

        //decoded user id from fron end
        $user_id = $decoded["id"];
        $sql = "SELECT * FROM user WHERE id = ?";
        $database = new Database('localhost', 'root', '', 'blogs');

        $conn =  $database->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(pdo::FETCH_ASSOC);
        if ($user["id"] == $user_id) {
            $id = $parts[4] ?? null;

            set_exception_handler('HandleErrors::handleExceptions');

            $database = new Database('localhost', 'root', '', 'blogs');

            $gateway = new BlogGateway($database);

            $controller = new ProcessBlogsRequests($gateway, $user_id);

            $controller->processRequests($_SERVER['REQUEST_METHOD'], $id);
        } else if ($user["id"] !== $user_id) {
            echo json_encode(["error_message" => "no such user exist in our database"]);
            die();
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
