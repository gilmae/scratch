<?php

namespace Scratch;

foreach (glob(dirname(__FILE__) . "/app/*.php") as $filename) {
    include $filename;
}

function run($app)
{

    $env = get_environment();
    $result = $app->call($env);

    if (!is_array($result)) {
        throw new Exception("Invalid App Results, expecting array: [Status, Headers, Body]");
    }

    if (count($result) != 3) {
        throw new Exception("Invalid App Results, expecting array: [Status, Headers, Body]");
    }

    $status = $result[0];
    $headers = $result[1];
    $body = $result[2];

    http_response_code($status);

    if (is_array($headers)) {
        foreach ($headers as $key => $value) {
            header($key . ": " . $value);
        }
    }

    if (!is_array($body)) {
        $body = array($body);
    }

    foreach ($body as $body_item) {
        echo ($body_item);
    }
}

function get_environment()
{
    $env = array_copy($_SERVER);
    $env["POST"] = array_copy($_POST);
    $env["GET"] = array_copy($_GET);
    $env["FILES"] = array_copy($_FILES);
    return $env;
}

function array_copy(array $array)
{
    $result = array();
    foreach ($array as $key => $val) {
        if (is_array($val)) {
            $result[$key] = array_copy($val);
        } elseif (is_object($val)) {
            $result[$key] = clone $val;
        } else {
            $result[$key] = $val;
        }
    }
    return $result;
}
?>
