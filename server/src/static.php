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
            FROM `room_all` as room
            LEFT JOIN `card_template` as card ON room.code = card.roomID 
            LEFT JOIN `teacher_link_template` as t_link USING(cid)
            LEFT JOIN `teacher_template` as teacher USING(tid) 
            WHERE room.code = ?',
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
            FROM `card_template` AS card
            LEFT JOIN `student_link_template` AS s_link USING(cid)
            LEFT JOIN `teacher_link_template` AS t_link USING(cid) 
            LEFT JOIN `student_template` AS student USING(sid)
            LEFT JOIN `teacher_template` as teacher USING(tid)
            WHERE `klassID` = ?',
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
            FROM `student_template` as student
            JOIN `student_link_template` as s_link USING(sid) 
            JOIN `card_template` as card USING(cid)
            JOIN `teacher_link_template` as t_link USING(cid)
            JOIN `teacher_template` as teacher USING(tid)
            WHERE student.`code` = ?',
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
            FROM `teacher_template` as teacher
            JOIN `teacher_link_template` as teacher2card ON teacher.tid = teacher2card.tid
            JOIN `card_template` as card ON teacher2card.cid = card.cid
            JOIN `teacher_link_template` as card2teacher ON card.cid = card2teacher.cid
            JOIN `teacher_template` as c_teacher ON card2teacher.tid = c_teacher.tid 
            WHERE teacher.`code` = ?'
    ]
];
