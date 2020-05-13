# coding=utf-8
import pymysql


# 读取搜索数据
def search_by_key(conn, key):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT * FROM `search` WHERE `key` = %s"
    cursor.execute(sql, args=[key])
    return cursor.fetchall()


# 读取分组搜索数据
def search_by_key_with_group(conn, key, group):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT * FROM `search` WHERE `key` = %s AND `group`=%s"
    cursor.execute(sql, args=[key, group])
    return cursor.fetchall()


# 读取指定节次空教室
def read_available_room(conn, week, session):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT `code`,`name` FROM `avl_room` " \
          "WHERE `session`=%s AND `week%s`=''"
    cursor.execute(sql, args=[session, week])
    return cursor.fetchall()
