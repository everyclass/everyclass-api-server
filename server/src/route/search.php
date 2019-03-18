<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/18
 * Time: 23:39
 */

use \Slim\App;
use \Slim\Http\Request;
use \Slim\Http\Response;

use \WolfBolin\Everyclass\Tools as Tools;

$app->group('/search', function (App $app) {
    $app->get('', function (Request $request, Response $response) {
        $result = ['status' => 'success', 'info' => 'Hello, search engine!'];
        return $response->withJson($result);
    });

    $app->get('/query', function (Request $request, Response $response) {
        $search_key = $request->getQueryParam("key", "");
        $page_num = intval($request->getQueryParam("page_num", 0));
        $page_size = intval($request->getQueryParam("page_size", 20));
        if (empty($search_key) || !is_string($search_key) || strlen($search_key) < 2 || $page_size < 2 || $page_size > 100) {
            goto Bad_request; // 错误的请求类型
        }

        // 在搜索库中完成关键词搜索
        $db = new MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['occam']);
        $collection = $db->selectCollection('search');
        $search_result = $collection->find(
            ['key' => $search_key],
            [
                'projection' => [
                    '_id' => 0
                ],
                'limit' => $page_size,
                'skip' => $page_num * $page_size
            ]
        );
        $search_result = (array)$search_result->toArray();
        $result = [];
        foreach ($search_result as $item) {
            $obj = [
                'code' => $item['code'],
                'name' => $item['name'],
                'type' => $item['type'],
                'semester' => (array)$item['semester']
            ];
            foreach ($item['data'] as $key => $value) {
                $obj[$key] = $value;
            }
            $result [] = $obj;
        }
        if (count($result) < 1) {
            goto Not_found;
        } else {
            $result = [
                'data' => $result,
                'info' => [
                    'page_num' => $page_num,
                    'page_size' => $page_size,
                    'count' => count($result)
                ]
            ];
        }

        // 将字典数据写入请求响应
        $result = array_merge($result, ['status' => 'success']);
        return $response->withJson($result);
        // 异常访问出口
        Bad_request:
        return \WolfBolin\Slim\HTTP\Bad_request($response);
        Not_found:
        return \WolfBolin\Slim\HTTP\Not_found($response);
    });

})->add(\WolfBolin\Slim\Middleware\x_auth_token())
    ->add(\WolfBolin\Slim\Middleware\maintenance_mode())
    ->add(\WolfBolin\Slim\Middleware\access_record());
