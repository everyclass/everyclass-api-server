# coding=utf-8
import pymysql


# 读取数据刷新日期
def read_kvdb(conn, key):
    cursor = conn.cursor()
    sql = "SELECT `val` FROM `kvdb` WHERE `key`=%s"
    cursor.execute(sql, args=[key])
    return cursor.fetchone()[0]


# 读取可用学期
def read_available_semester(conn, code, group):
    cursor = conn.cursor()
    sql = "SELECT DISTINCT `semester` FROM `semester` WHERE `code`=%s AND `group`=%s"
    cursor.execute(sql, args=[code, group])
    return [obj[0] for obj in cursor.fetchall()]


def read_lesson_data(conn, code, group, semester):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = """
    SELECT
        `lesson`.`code`,
        `lesson`.`session`,
        `lesson`.`week`,
        `lesson`.`week_str`,
        `lesson`.`course_code`,
        `lesson`.`course_name`,
        `lesson`.`room_code`,
        `lesson`.`room_name`,
        `lesson`.`teacher_list`
    FROM
        `link`
        LEFT JOIN `lesson` ON `link`.`semester` = `lesson`.`semester` 
        AND `link`.`lesson` = `lesson`.`code` 
        AND `link`.`session` = `lesson`.`session`
    WHERE
        `link`.`object` = %s 
        AND `link`.`group` = %s 
        AND `link`.`semester` = %s
    """
    cursor.execute(sql, args=[code, group, semester])
    return cursor.fetchall()


def read_remark_data(conn, code, group, semester):
    cursor = conn.cursor()
    sql = "SELECT `remark` FROM `remark` WHERE `code`=%s AND `group`=%s AND `semester`=%s"
    cursor.execute(sql, args=[code, group, semester])
    res = cursor.fetchone()
    if res is None:
        return ""
    return res[0]
