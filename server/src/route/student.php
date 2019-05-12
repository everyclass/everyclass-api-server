<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/15
 * Time: 0:48
 */

use \Slim\App;
use \Slim\Http\Request;
use \Slim\Http\Response;

use \WolfBolin\Everyclass\Tools as Tools;

$app->group('/student/{identifier:[0-9a-zA-Z]+}', function (App $app) {
    $app->get('', function (Request $request, Response $response, $args) {
        // 获取请求数据
        $identifier = $args['identifier'];

        // 在数据库中查询数据
        $result = [];
        // 使用MongoDB数据库
        $db = new MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
        $collection = $db->selectCollection('search');
        // 查询可用学期信息
        $select_result = $collection->findOne(
            ['code' => $identifier, 'type' => 'student'],
            ['projection' => ['_id' => 0, 'semester' => 1]]
        );
        // 实体存在性检验
        if ($select_result) {
            $semester_list = (array)$select_result->getArrayCopy();
            $semester_list = (array)$semester_list['semester'];
        } else {
            goto Not_found;
        }

        // 使用MySQL数据库
        $mysqli = $this->get('mysql_client');
        mysqli_select_db($mysqli, $this->get('MySQL')['entity']);
        // 查询学生基本信息
        if ($sql_result = mysqli_query($mysqli,
            sprintf($this->get('SQL')['student_info'], $identifier))) {
            // 实体存在性检验
            if ($row_cnt = mysqli_num_rows($sql_result) == 0) {
                goto Not_found;
            }
            $row = mysqli_fetch_row($sql_result);
            $result['student_code'] = $row[0];
            $result['name'] = $row[1];
            $result['class'] = $row[2];
            $result['deputy'] = $row[3];
            $result['campus'] = $row[4];
            $result['semester_list'] = $semester_list;
        } else {
            goto Bad_request;
        }

        // 将字典数据写入请求响应
        $result = array_merge(['status' => 'success'], $result);
        return $response->withJson($result);
        Bad_request:
        return \WolfBolin\Slim\HTTP\Bad_request($response);
        Not_found:
        return \WolfBolin\Slim\HTTP\Not_found($response);
    });

    $app->get('/timetable/{semester:20[0-9]{2}-20[0-9]{2}-[1|2]}',
        function (Request $request, Response $response, $args) {
            // 获取请求数据
            $semester = $args['semester'];
            $identifier = $args['identifier'];

            // 在数据库中查询数据
            $result = [];
            // 使用MongoDB数据库
            $db = new MongoDB\Database($this->get('mongodb_client'), $this->get('MongoDB')['entity']);
            $collection = $db->selectCollection('search');
            // 查询可用学期信息
            $select_result = $collection->findOne(
                ['code' => $identifier, 'type' => 'student'],
                ['projection' => ['_id' => 0, 'semester' => 1]]
            );
            // 实体存在性检验
            if ($select_result) {
                $semester_list = (array)$select_result->getArrayCopy();
                $semester_list = (array)$semester_list['semester'];
            } else {
                goto Not_found;
            }
            // 验证学期是否可用
            if (!in_array($semester, $semester_list)) {
                goto Bad_request;
            }

            // 使用MySQL数据库
            $mysqli = $this->get('mysql_client');
            mysqli_select_db($mysqli, $this->get('MySQL')['entity']);
            // 查询学生基本信息
            if ($sql_result = mysqli_query($mysqli,
                sprintf($this->get('SQL')['student_info'], $identifier))) {
                // 实体存在性检验
                if ($row_cnt = mysqli_num_rows($sql_result) == 0) {
                    goto Not_found;
                }
                $row = mysqli_fetch_row($sql_result);
                $result['student_code'] = $row[0];
                $result['name'] = $row[1];
                $result['class'] = $row[2];
                $result['deputy'] = $row[3];
                $result['campus'] = $row[4];
                $result['semester'] = $semester;
                $result['semester_list'] = $semester_list;
            } else {
                goto Bad_request;
            }

            // 查询学生课表信息
            $stmt = mysqli_prepare($mysqli, $this->get('SQL')['student']);
            mysqli_stmt_bind_param($stmt, "ss", $identifier, $semester);
            mysqli_stmt_execute($stmt);
            $stmt->bind_result($card_name, $card_code, $card_room, $card_week, $card_lesson,
                $room_code, $course_code, $teacher_name, $teacher_code, $teacher_title, $teacher_unit);

            $card_list = [];
            while ($stmt->fetch()) {
                // 对每个数据进行数据转换
                $card_week = json_decode($card_week, true);

                // 完成数据的映射处理
                $card_list[$card_code]['name'] = $card_name;
                $card_list[$card_code]['room'] = $card_room;
                $card_list[$card_code]['lesson'] = $card_lesson;
                $card_list[$card_code]['card_code'] = $card_code;
                $card_list[$card_code]['room_code'] = $room_code;
                $card_list[$card_code]['week_list'] = $card_week;
                $card_list[$card_code]['course_code'] = $course_code;
                $card_list[$card_code]['week_string'] = Tools\week_encode($card_list[$card_code]['week_list']);

                $card_list[$card_code]['teacher_list'] [] = [
                    'teacher_code' => $teacher_code,
                    'name' => $teacher_name,
                    'title' => $teacher_title
                ];
            }
            if (count($card_list) < 1) {
                $result['card_list'] = [];
            } else {
                $result['card_list'] = array_values($card_list);
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

