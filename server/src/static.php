<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/11
 * Time: 14:52
 */
return [
    'Check_list' => [
        'MySQL connection' => false,
        'Mongo connection' => false,
    ],
    'SQL' => [
        'room' => '
            SELECT 
            `room`.`code`,
            `room`.`name`, 
            `room`.`building`, 
            `room`.`campus`, 
            `card`.`name`,
            `card`.`code`,
            `card`.`room`,
            `card`.`week`,
            `card`.`lesson`,
            `card`.`room_code`,
            `card`.`course_code`,
            `teacher`.`code`,
            `teacher`.`name`,
            `teacher`.`title`
            FROM `room` as room
            LEFT JOIN `card` as card ON room.code = card.room_code 
            LEFT JOIN `teacher_link` as t_link USING(cid)
            LEFT JOIN `teacher` as teacher USING(tid) 
            WHERE card.`semester` = ? AND room.code = ?',
        'card' => '
            SELECT 
            `card`.`name`,
            `card`.`code`,
            `card`.`room`,
            `card`.`pick`,
            `card`.`hour`,
            `card`.`type`,
            `card`.`week`,
            `card`.`lesson`,
            `card`.`tea_class`,
            `card`.`room_code`,
            `card`.`course_code`,
            `student`.`name`,
            `student`.`code`,
            `student`.`class`,
            `student`.`deputy`,
            `teacher`.`name`,
            `teacher`.`code`,
            `teacher`.`title`,
            `teacher`.`unit`
            FROM `card` AS card
            LEFT JOIN `student_link` AS s_link USING(cid)
            LEFT JOIN `teacher_link` AS t_link USING(cid) 
            LEFT JOIN `student` AS student USING(sid)
            LEFT JOIN `teacher` as teacher USING(tid)
            WHERE card.`semester` = ? AND card.`code` = ?',
//        'student_base' => "SELECT `name`, `code`, `class`, `deputy`, `campus`, `semester` WHERE `code` = '%s'",
        'student' => '
            SELECT 
            `card`.`name`,
            `card`.`code`,
            `card`.`room`,
            `card`.`week`,
            `card`.`lesson`,
            `card`.`room_code`,
            `card`.`course_code`,
            `teacher`.`name`,
            `teacher`.`code`,
            `teacher`.`title`,
            `teacher`.`unit`
            FROM `student` as student
            JOIN `student_link` as s_link USING(sid) 
            JOIN `card` as card USING(cid)
            JOIN `teacher_link` as t_link USING(cid)
            JOIN `teacher` as teacher USING(tid)
            WHERE student.`semester` = ? AND student.`code` = ?',
        'teacher' => '
            SELECT 
            `card`.`name`,
            `card`.`code`,
            `card`.`room`,
            `card`.`week`,
            `card`.`lesson`,
            `card`.`room_code`,
            `card`.`course_code`,
            `c_teacher`.`name`, 
            `c_teacher`.`code`,
            `c_teacher`.`title`,
            `c_teacher`.`unit`
            FROM `teacher` as teacher
            JOIN `teacher_link` as teacher2card ON teacher.tid = teacher2card.tid
            JOIN `card` as card ON teacher2card.cid = card.cid
            JOIN `teacher_link` as card2teacher ON card.cid = card2teacher.cid
            JOIN `teacher` as c_teacher ON card2teacher.tid = c_teacher.tid 
            WHERE teacher.`semester` = ? AND teacher.`code` = ?'
    ]
];
