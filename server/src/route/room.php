<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/12
 * Time: 16:41
 */

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$app->group('/room', function (App $app) {
    $app->get('', function (Request $request, Response $response) {
        $result = ['status' => 'success', 'info' => 'Hello, room!'];
        return $response->withJson($result);
    });

    $app->get('/{semester:20[0-9]{2}-20[0-9]{2}-[1|2]}/{identifier:[0-9a-zA-Z]+}',
        function (Request $request, Response $response, $args) {
            // 获取请求数据
            $semester = $args['semester'];
            $identifier = $args['identifier'];

            // 验证请求学期数据
            $db = new MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
            $collection = $db->selectCollection('info');
            $semester_list = $collection->findOne(
                ['key' => 'semester_list']
            );
            $semester_list = (array)$semester_list->getArrayCopy();
            $semester_list = (array)$semester_list['value'];
            if (!in_array($semester, $semester_list)) {
                goto Bad_request;
            }

            // 在数据库中查询数据
            $mysqli = $this->get('mysql_client');
            mysqli_select_db($mysqli, $this->get('MySQL')['occam']);
            $sql = str_replace('template', $semester, $this->get('SQL')['room']);
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, "s", $identifier);
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($room_rid, $room_name, $room_building,
                $room_campus, $course_name, $course_code, $course_room, $room_code, $course_week,
                $course_lesson, $teacher_code, $teacher_name, $teacher_title);

            $result = [];
            while ($stmt->fetch()) {
                // 对每个数据进行数据转换
                $course_week = json_decode($course_week, true);

                // 完成数据的映射处理
                $result['code'] = $room_rid;
                $result['name'] = $room_name;
                $result['building'] = $room_building;
                $result['campus'] = $room_campus;

                $result['course'] [] = $course_code;
                $result[$course_code]['name'] = $course_name;
                $result[$course_code]['course_code'] = $course_code;
                $result[$course_code]['room'] = $course_room;
                $result[$course_code]['room_code'] = $room_code;
                $result[$course_code]['week'] = $course_week;
                $result[$course_code]['lesson'] = $course_lesson;

                $result[$course_code]['teacher'] [] = [
                    'code' => $teacher_code,
                    'name' => $teacher_name,
                    'title' => $teacher_title
                ];
            }
            if (count($result) < 1) {
                goto Not_found;
            } else {
                $result['course'] = array_values(array_unique($result['course']));
            }


            // 将字典数据写入请求响应
            return $response->withJson($result);
            // 异常访问出口
            Bad_request:
            return WolfBolin\Slim\HTTP\Bad_request($response);
            Not_found:
            return WolfBolin\Slim\HTTP\Not_found($response);
        });

});

