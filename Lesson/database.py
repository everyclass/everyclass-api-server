# coding=utf-8
import pymysql


# 读取课程基本信息
def read_course_info(conn, lesson, session, semester):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = """
    SELECT
        `course`.`code`,
        `course`.`name`,
        `course`.`type`,
        `course`.`faculty`
    FROM
        `link` LEFT JOIN `course` ON `link`.`object` = `course`.`code`
    WHERE
        `link`.`lesson` = %s
        AND `link`.`session` = %s
        AND `link`.`semester` = %s
        AND `link`.`group`='course'
    """
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
        `student`.`faculty`
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

    student_list = []
    for item in cursor.fetchall():
        item["student_code"] = item.pop("code")
        student_list.append(item)
    return student_list


# 读取教师基本信息
def read_teacher_list(conn, lesson, session, semester):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = """
    SELECT
        `teacher`.`code`,
        `teacher`.`name`,
        `teacher`.`title`,
        `teacher`.`department`
    FROM
        `link` LEFT JOIN `teacher` ON `link`.`object` = `teacher`.`code`
    WHERE
        `link`.`lesson` = %s
        AND `link`.`session` = %s
        AND `link`.`semester` = %s
        AND `link`.`group`='teacher'
    """
    cursor.execute(sql, args=[lesson, session, semester])
    teacher_list = []
    for item in cursor.fetchall():
        item["teacher_code"] = item.pop("code")
        teacher_list.append(item)
    return teacher_list
