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

$app->group('/course', function (App $app) {
    $app->get('', function (Request $request, Response $response) {
        $result = ['status' => 'success', 'info' => 'Hello, course!'];
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
            $sql = str_replace('template', $semester, $this->get('SQL')['course']);
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, "s", $identifier);
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($course_name, $klass_code, $course_room, $room_code, $course_week,
                $course_lesson, $course_klass, $course_pick, $course_hour, $course_type, $student_name, $student_code,
                $student_klass, $student_deputy, $teacher_name, $teacher_code, $teacher_title, $teacher_unit);

            $result = [];
            while ($stmt->fetch()) {
                $result['name'] = $course_name;
                $result['klass_code'] = $klass_code;
                $result['room'] = $course_room;
                $result['room_code'] = $room_code;
                $result['week'] = $course_week;
                $result['lesson'] = $course_lesson;
                $result['klass'] = $course_klass;
                $result['pick'] = $course_pick;
                $result['hour'] = $course_hour;
                $result['type'] = $course_type;

                $result['student'] [] = $student_code;
                $result[$student_code]['name'] = $student_name;
                $result[$student_code]['code'] = $student_code;
                $result[$student_code]['klass'] = $student_klass;
                $result[$student_code]['deputy'] = $student_deputy;

                $result['teacher'] [] = $teacher_code;
                $result[$teacher_code]['name'] = $teacher_name;
                $result[$teacher_code]['code'] = $teacher_code;
                $result[$teacher_code]['title'] = $teacher_title;
                $result[$teacher_code]['unit'] = $teacher_unit;
            }
            if (count($result) < 1) {
                goto Not_found;
            } else {
                $result['student'] = array_values(array_unique($result['student']));
                $result['teacher'] = array_values(array_unique($result['teacher']));
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

