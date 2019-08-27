<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/6
 * Time: 19:36
 * @param $response
 * @return mixed
 */

namespace WolfBolin\Slim\HTTP;

use Slim\Http\Response;

function Not_modified(Response $response)
{
    return $response->withStatus(304);
}

function Unauthorized(Response $response, $info = "Require authentication")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(401)->withJson($error_info);
}

function Unauthorized3(Response $response, $info = "Authentication failure")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(401)->withJson($error_info);
}

function Bad_request(Response $response, $info = "Access parameter exception")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(403)->withJson($error_info);
}

function Not_found(Response $response, $info = "Page file could not be found")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(404)->withJson($error_info);
}

function Not_allowed(Response $response, $info = "This request method was rejected")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(405)->withJson($error_info);
}

function Not_acceptable(Response $response, $info = "Request cannot be responsed")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(406)->withJson($error_info);
}

function Server_error(Response $response, $info = "Server internal error")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(500)->withJson($error_info);
}

function Service_unavailable(Response $response, $info = "Service temporarily unavailable")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(503)->withJson($error_info);
}
