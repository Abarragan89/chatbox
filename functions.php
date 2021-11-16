<?php 
    //PDO object needs attr, user, pass, opts
    //PDO is what your query from. get back items in array or object
    //$attr needs host, data, chrs
    $host = 'localhost';
    $data = 'chatbox';
    $user = 'reesie';
    $pass = 'password';
    $chrs = 'utf8mb4';
    $attr = "mysql:host=$host;dbname=$data;charset=$chrs";
    $opts = 
    [
        //the way we handle errors
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        //the way we get our row items back from fectch
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
        //whether it forces emulation of prepared statements
        PDO::ATTR_EMULATE_PREPARES => false, 
    ];

    try
    {
        $pdo = new PDO($attr, $user, $pass, $opts);
    }
    catch (\PDOException $e)
    {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    // -------------------------THE FIVE FUNCTIONS-----------------------------
    //Creates a table using custom function
    function createTable($name, $query) 
    {
        queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
        echo"Table '$name' created or already exists.<br>";
    }
    //Make a query
    function queryMysql($query)
    {
        global $pdo;
        return $pdo->query($query);
    }
    //End session
    function destroySession()
    {
        $_SESSION=array();

        if (session_id() != "" || isset($_COOKIE[session_name()]))
            setcookie(session_name(), '', time()-25292000, '/');
    }

    function sanitizeString($var)
    {
        global $pdo;
        $var = strip_tags($var);
        $var = htmlentities($var);
        $result = $pdo->quote($var);
        return str_replace("'", "", $result);
    }

    function showProfile($user)
    {
        if (file_exists("$user.jpg"))
            echo "<img src='$user.jpg' style='float:left;'>";

        $result = $pdo->query("SELECT * FROM profiles WHERE user='$user'");

        while ($row = $result->fetch())
        {
            die(stripslashes($row['text']) . "<br style='clear:left;'><br>");
        }
        echo "<p>Nothing to see here, yet</p><br>";
    }
?>