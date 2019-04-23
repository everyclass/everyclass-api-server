<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/12
 * Time: 16:41
 */

use \Slim\App;
use \Slim\Http\Request;
use \Slim\Http\Response;

use \WolfBolin\Everyclass\Tools as Tools;

$app->group('/course/{identifier:[0-9a-zA-Z]+}', function (App $app) {
    $app->get('', function (Request $request,Response $response) {
        $result = ['status' => 'success', 'info' => 'Hello, course!'];
        return $response->withJson($result);
    });

    $app->get('/timetable/{semester:20[0-9]{2}-20[0-9]{2}-[1|2]}',
        function (Request $request,Response $response, $args) {
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
            mysqli_select_db($mysqli, $this->get('MySQL')['entity']);
            $stmt = mysqli_prepare($mysqli, $this->get('SQL')['course']);
            mysqli_stmt_bind_param($stmt, "ss", $semester, $identifier);
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($course_name, $klass_code, $course_room, $room_code, $course_week,
                $course_lesson, $course_klass, $course_pick, $course_hour, $course_type, $student_name, $student_code,
                $student_klass, $student_deputy, $teacher_name, $teacher_code, $teacher_title, $teacher_unit);

            $result = [];
            $student_list = [];
            $teacher_list = [];
            while ($stmt->fetch()) {
                // 对每个数据进行数据转换
                $course_week = json_decode($course_week, true);

                // 完成数据的映射处理
                $result['name'] = $course_name;
                $result['course_code'] = $klass_code;
                $result['room'] = $course_room;
                $result['room_code'] = $room_code;
                $result['week_list'] = $course_week;
                $result['lesson'] = $course_lesson;
                $result['union_name'] = $course_klass;
                $result['picked'] = $course_pick;
                $result['hour'] = $course_hour;
                $result['type'] = $course_type;

                $student_list[$student_code]['name'] = $student_name;
                $student_list[$student_code]['student_code'] = $student_code;
                $student_list[$student_code]['class'] = $student_klass;
                $student_list[$student_code]['deputy'] = $student_deputy;

                $teacher_list[$teacher_code]['name'] = $teacher_name;
                $teacher_list[$teacher_code]['teacher_code'] = $teacher_code;
                $teacher_list[$teacher_code]['title'] = $teacher_title;
                $teacher_list[$teacher_code]['unit'] = $teacher_unit;
            }
            if (count($result) < 1) {
                goto Not_found;
            } else {
                // 最后的处理
                $result['semester'] = $semester;
                $result['week_string'] = Tools\week_encode($result['week_list']);
                $result['student_list'] = array_values($student_list);
                $result['teacher_list'] = array_values($teacher_list);
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

