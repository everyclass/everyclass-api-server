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

$app->group('/card/{identifier:[0-9a-zA-Z]+}', function (App $app) {
    $app->get('', function (Request $request,Response $response) {
        $result = ['status' => 'success', 'info' => 'Hello, card!'];
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
            $stmt = mysqli_prepare($mysqli, $this->get('SQL')['card']);
            mysqli_stmt_bind_param($stmt, "ss", $semester, $identifier);
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($card_name, $card_code, $card_room, $card_pick, $card_hour,
                $card_type, $card_week, $card_lesson, $tea_class, $room_code, $course_code,
                $student_name, $student_code, $student_klass, $student_deputy,
                $teacher_name, $teacher_code, $teacher_title, $teacher_unit);

            $result = [];
            $student_list = [];
            $teacher_list = [];
            while ($stmt->fetch()) {
                // 对每个数据进行数据转换
                $card_week = json_decode($card_week, true);

                // 完成数据的映射处理
                $result['name'] = $card_name;
                $result['room'] = $card_room;
                $result['hour'] = $card_hour;
                $result['type'] = $card_type;
                $result['picked'] = $card_pick;
                $result['lesson'] = $card_lesson;
                $result['tea_class'] = $tea_class;
                $result['card_code'] = $card_code;
                $result['room_code'] = $room_code;
                $result['week_list'] = $card_week;
                $result['course_code'] = $course_code;
                $result['week_string'] = Tools\week_encode($result['week_list']);

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
                $result['student_list'] = array_values($student_list);
                $result['teacher_list'] = array_values($teacher_list);
            }

            // 将字典数据写入请求响应
            $result = array_merge(['status' => 'success'], $result);
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

