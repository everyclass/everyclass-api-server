# coding=utf-8
import pymysql


# 读取可用学期
def read_available_semester(conn, group, code):
    cursor = conn.cursor()
    sql = "SELECT DISTINCT `semester` FROM `link` WHERE `object`=%s AND `group`=%s"
    cursor.execute(sql, args=[code, group])
    return [obj[0] for obj in cursor.fetchall()]
