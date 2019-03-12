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
            WHERE `klassID` = ?'
    ]
];
