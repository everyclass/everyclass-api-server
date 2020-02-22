# coding=utf-8
import pymysql


# 读取课程基本信息
def read_room_info(conn, code):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT `code`,`name`,`type`,`campus`,`building` FROM `room` WHERE `code`=%s"
    cursor.execute(sql, args=[code])
    return cursor.fetchone()
