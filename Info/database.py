# coding=utf-8
import pymysql


# 读取指定节次空教室
def read_spare_room(conn, week, session):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT `code`,`name`,`week%s` FROM `spare` " \
          "WHERE `session`=%s AND `week%s`!=''"
    cursor.execute(sql, args=[week, session, week])
    print(sql % (week, session, week))
    return cursor.fetchall()


# 读取数据刷新日期
def read_kvdb(conn, key):
    cursor = conn.cursor()
    sql = "SELECT `val` FROM `kvdb` WHERE `key`=%s"
    cursor.execute(sql, args=[key])
    return cursor.fetchone()[0]
