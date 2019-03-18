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

function Unauthorized(Response $response, $info = "需要验证身份")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(401)->withJson($error_info);
}

function Unauthorized3(Response $response, $info = "身份验证失败")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(401)->withJson($error_info);
}

function Bad_request(Response $response, $info = "访问参数异常")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(403)->withJson($error_info);
}

function Not_found(Response $response, $info = "无法找到页面文件")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(404)->withJson($error_info);
}

function Not_allowed(Response $response, $info = "此请求方法被拒绝")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(405)->withJson($error_info);
}

function Not_acceptable(Response $response, $info = "请求参数无法被响应")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(406)->withJson($error_info);
}

function Server_error(Response $response, $info = "服务器内部错误")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(500)->withJson($error_info);
}

function Service_unavailable(Response $response, $info = "服务暂时不可用")
{
    $error_info = ["status" => "error", "info" => $info];
    return $response->withStatus(503)->withJson($error_info);
}
