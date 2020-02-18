# coding=utf-8
import pymysql


# 读取学生基本信息
def read_teacher_info(conn, code):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT `code`,`name`,`title`,`degree`,`department` FROM `teacher` WHERE `code`=%s"
    cursor.execute(sql, args=[code])
    return cursor.fetchone()



