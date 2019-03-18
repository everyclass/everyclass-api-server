<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/15
 * Time: 1:33
 */

use \Slim\App;
use \Slim\Http\Request;
use \Slim\Http\Response;

use \WolfBolin\Everyclass\Tools as Tools;

$app->group('/teacher', function (App $app) {
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

            // 查询数据库的学生信息
            $db = new MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['occam']);
            $collection = $db->selectCollection('teacher');
            $select_result = $collection->findOne(
                ['code' => $identifier],
                ['projection' => ['_id' => 0]]
            );
            if ($select_result) {
                // 此人信息存在
                $result = (array)$select_result->getArrayCopy();
                $result['available_semester'] = (array)$result['semester'];
                $result['semester'] = $semester;
            } else {
                // 未找到此人信息
                goto Not_found;
            }

            // 在数据库中查询数据
            $mysqli = $this->get('mysql_client');
            mysqli_select_db($mysqli, $this->get('MySQL')['occam']);
            $sql = str_replace('template', $semester, $this->get('SQL')['teacher']);
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, "s", $identifier);
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($course_name, $course_code, $course_room, $room_code,
                $course_week, $course_lesson, $teacher_name, $teacher_code, $teacher_title, $teacher_unit);

            $course_list = [];
            while ($stmt->fetch()) {
                // 对每个数据进行数据转换
                $course_week = json_decode($course_week, true);

                // 完成数据的映射处理
                $course_list[$course_code]['name'] = $course_name;
                $course_list[$course_code]['course_code'] = $course_code;
                $course_list[$course_code]['room'] = $course_room;
                $course_list[$course_code]['room_code'] = $room_code;
                $course_list[$course_code]['week'] = $course_week;
                $course_list[$course_code]['week_str'] = Tools\week_encode($course_list[$course_code]['week']);
                $course_list[$course_code]['lesson'] = $course_lesson;

                $course_list[$course_code]['teacher'] [] = [
                    'code' => $teacher_code,
                    'name' => $teacher_name,
                    'title' => $teacher_title
                ];
            }
            if (count($course_list) < 1) {
                goto Not_found;
            } else {
                $result['course'] = array_values($course_list);
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
