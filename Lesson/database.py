# coding=utf-8
import pymysql


# 读取节次基本数据
def read_lesson_info(conn, lesson, session, semester):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT * FROM `lesson` WHERE `code`=%s AND `session`=%s AND `semester`=%s"
    cursor.execute(sql, args=[lesson, session, semester])
    return cursor.fetchone()


# 读取学生基本信息
def read_student_list(conn, lesson, session, semester):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = """
    SELECT
        `student`.`code`,
        `student`.`name`,
        `student`.`class`,
        `student`.`department`
    FROM
        `link`
        LEFT JOIN `student` ON `link`.`object` = `student`.`code`
    WHERE
        `link`.`lesson` = %s
        AND `link`.`session` = %s
        AND `link`.`semester` = %s
        AND `link`.`group`='student'
    """
    cursor.execute(sql, args=[lesson, session, semester])
    return cursor.fetchall()
