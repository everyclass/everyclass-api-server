# coding=utf-8
import pymysql


# 读取可用学期
def read_available_semester(conn, group, code):
    cursor = conn.cursor()
    sql = "SELECT DISTINCT `semester` FROM `link` WHERE `object`=%s AND `group`=%s"
    cursor.execute(sql, args=[code, group])
    return [obj[0] for obj in cursor.fetchall()]


def read_lesson_data(conn, group, code, semester):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = """
    SELECT
        `link`.`lesson`,
        `link`.`session`,
        `lesson`.`week`,
        `lesson`.`week_str`,
        `lesson`.`course_code`,
        `lesson`.`course_name`,
        `lesson`.`room_code`,
        `lesson`.`room_name`,
        `lesson`.`teacher_list`
    FROM
        `link`
        LEFT JOIN `lesson` ON `link`.`lesson` = `lesson`.`code` 
        AND `link`.`session` = `lesson`.`session` 
        AND `link`.`semester` = `lesson`.`semester` 
    WHERE
        `object` = %s 
        AND `group` = %s 
        AND `link`.`semester` = %s
    """
    cursor.execute(sql, args=[code, group, semester])
    return cursor.fetchall()
