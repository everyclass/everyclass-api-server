<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/11
 * Time: 14:44
 */

use Slim\Http\Request;
use Slim\Http\Response;


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
