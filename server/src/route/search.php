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
        $search_type = $request->getQueryParam("type", array("student", "teacher"));
        $page_size = intval($request->getQueryParam("page_size", 20));
        $page_index = intval($request->getQueryParam("page_index", 1));
        $sort_key = $request->getQueryParam("sort_key", null);
        $sort_order = $request->getQueryParam("sort_order", "AES");
        if ($page_size < 2 || $page_size > 100 || $page_index < 1) {
            goto Bad_request; // 错误的请求类型
        }
        if (empty($search_key) || !is_string($search_key) || strlen($search_key) < 2) {
            goto Bad_request;
        }
        if (empty($search_type) || !is_array($search_type) || count($search_type) < 1) {
            goto Bad_request;
        }
        if (!empty($sort_key)) {
            if (!in_array($sort_key, ["code", "name", "type"]) || !in_array($sort_order, ["AES", "DESC"])) {
                goto Bad_request;
            }
            if($sort_order == "AES"){
                $sort_order = 1;
            }else{
                $sort_order = -1;
            }
        }

        // 在搜索库中完成关键词搜索
        $db = new MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['occam']);
        $collection = $db->selectCollection('search');
        $search_result = $collection->find(
            [
                'key' => $search_key,
                'type' => ['$in' => $search_type]
            ],
            [
                'projection' => [
                    '_id' => 0,
                    'key' => 0
                ],
                'limit' => $page_size,
                'skip' => ($page_index - 1) * $page_size,
                'sort' => empty($sort_key) ? null : [$sort_key => $sort_order]
            ]
        );
        $search_count = $collection->find(
            [
                'key' => $search_key,
                'type' => ['$in' => $search_type]
            ],
            [
                'projection' => [
                    '_id' => 1
                ]
            ]
        );
        $search_result = (array)$search_result->toArray();
        $search_count = (array)$search_count->toArray();
        $result = [];
        foreach ($search_result as $item) {
            $item[$item['type'] . '_code'] = $item['code'];
            $item['semester_list'] = (array)$item['semester'];
            $item = array_merge((array)$item, (array)$item['data']);
            if($item['type'] == 'student'){
                $item['class'] = $item['klass'];
                unset($item['klass']);
            }
            unset($item['data']);
            unset($item['code']);
            unset($item['semester']);
            $result [] = $item;
        }
        if (count($result) < 1) {
            goto Not_found;
        } else {
            $result = [
                'data' => $result,
                'info' => [
                    'page_index' => $page_index,
                    'page_size' => $page_size,
                    'page_num' => ceil(count($search_count) / $page_size),
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
