<?php

foreach (glob(dirname(__FILE__) . "/app/*.php") as $filename)
{
    include $filename;
}

class Scratch
{
    public static function run($app)
    {
        $path = $_SERVER['REQUEST_URI'];
        $result = $app->call($path);

        if (!is_array($result))
        {
            throw new Exception("Invalid App Results, expecting array: [Status, Headers, Body]");
        }

        if (count($result) != 3)
        {
            throw new Exception("Invalid App Results, expecting array: [Status, Headers, Body]");          
        }

        $status = $result[0];
        $headers = $result[1];
        $body = $result[2];

        http_response_code($status);

        if (is_array($headers))
        {
            foreach ($headers as $key => $value)
            {
                header($key . ": " . $value);
            }
        }

        if (!is_array($body)) 
        {
            $body = array($body);
        }

        foreach ($body as $body_item)
        {
            echo($body_item);
        }
    }
}
?>