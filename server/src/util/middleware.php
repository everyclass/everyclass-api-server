<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/11
 * Time: 14:44
 */

namespace WolfBolin\Slim\Middleware;

use \Slim\Http\Request;
use \Slim\Http\Response;

function x_auth_token() {
    $result = function (Request $request, Response $response, $next) {
        $auth_token = $request->getHeader('X-Auth-Token');
        if ($auth_token) {
            if (count($auth_token) == 1 && $auth_token[0] == $this->get('Auth_Token')) {
                return $next($request, $response);
            } else {
                $error_info = ["status" => "error", "info" => "身份验证失败"];
                return $response->withStatus(401)->withJson($error_info);
            }
        } else {
            $error_info = ["status" => "error", "info" => "需要进行身份验证"];
            return $response->withStatus(401)->withJson($error_info);
        }
    };
    return $result;
}


function access_record() {
    $result = function (Request $request, Response $response, $next) {
        $http_result = $next($request, $response);
        $db = new \MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
        $collection = $db->selectCollection('record');
        $collection->insertOne([
            'code' => $response->getStatusCode(),
            'method' => $request->getMethod(),
            'scheme' => $request->getUri()->getScheme(),
            'host' => $request->getUri()->getHost(),
            'port' => $request->getUri()->getPort(),
            'path' => $request->getUri()->getPath(),
            'query' => $request->getUri()->getQuery(),
            'fragment' => $request->getUri()->getFragment(),
            'user_info' => $request->getUri()->getUserInfo(),
            'authority' => $request->getUri()->getAuthority(),
            'header' => $request->getHeaders(),
            'time' => time(),
            'date' => date("Y-m-d H:i:s")
        ]);
        return $http_result;
    };
    return $result;
}
