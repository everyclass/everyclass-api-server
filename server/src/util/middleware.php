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

        if (empty($auth_token) || count($auth_token) > 1 || !is_string($auth_token[0])) {
            return \WolfBolin\Slim\HTTP\Unauthorized($response);
        }

        // 在数据库中查询Token
        $db = new \MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
        $collection = $db->selectCollection('token');
        $token_status = $collection->findOne([
            'token' => $auth_token[0]
        ]);

        // 处理Token信息
        if ($token_status) {
            $response = $response->withHeader('X-Auth-User', $token_status['role']);
            return $next($request, $response);
        } else {
            return \WolfBolin\Slim\HTTP\Unauthorized3($response);
        }
    };
    return $result;
}


function access_record() {
    $result = function (Request $request, Response $response, callable $next) {
        $http_result = $next($request, $response);
        $db = new \MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
        $collection = $db->selectCollection('record');
        $collection->insertOne([
            'role' => $http_result->hasHeader('X-Auth-User') ? $http_result->getHeader('X-Auth-User')[0] : "guest",
            'addr' => $_SERVER['REMOTE_ADDR'],
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


function maintenance_mode() {
    $result = function (Request $request, Response $response, $next) {
        $db = new \MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
        $collection = $db->selectCollection('info');
        $service_state = $collection->findOne([
            'key' => 'service_state'
        ]);
        $service_notice = $collection->findOne([
            'key' => 'service_notice'
        ]);
        $service_state = (array)$service_state->getArrayCopy();
        $service_notice = (array)$service_notice->getArrayCopy();

        if ($service_state['value'] != 'running') {
            $service_info = [
                'service_state' => $service_state['value'],
                'service_notice' => $service_notice['value']
            ];
            // 服务停止，发布公告
            return \WolfBolin\Slim\HTTP\Service_unavailable($response, $service_info);
        }

        // 服务正在运行
        return $next($request, $response);
    };
    return $result;
}
