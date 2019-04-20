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
            `card`.`klassID`,
            `card`.`room`,
            `card`.`roomID`,
            `card`.`week`,
            `card`.`lesson`,
            `teacher`.`code`,
            `teacher`.`name`,
            `teacher`.`title`
            FROM `room` as room
            LEFT JOIN `card` as card ON room.code = card.roomID 
            LEFT JOIN `teacher_link` as t_link USING(cid)
            LEFT JOIN `teacher` as teacher USING(tid) 
            WHERE card.`semester` = ? AND room.code = ?',
        'course' => '
            SELECT 
            `card`.`name`,
            `card`.`klassID`,
            `card`.`room`,
            `card`.`roomID`,
            `card`.`week`,
            `card`.`lesson`,
            `card`.`klass`,
            `card`.`pick`,
            `card`.`hour`,
            `card`.`type`,
            `student`.`name`,
            `student`.`code`,
            `student`.`klass`,
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
            WHERE card.`semester` = ? AND card.`klassID` = ?',
        'student' => '
            SELECT 
            `card`.`name`,
            `card`.`klassID`,
            `card`.`room`,
            `card`.`roomID`,
            `card`.`week`,
            `card`.`lesson`,
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
            `card`.`klassID`,
            `card`.`room`,
            `card`.`roomID`,
            `card`.`week`,
            `card`.`lesson`,
            `c_teacher`.`code`,
            `c_teacher`.`name`, 
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
