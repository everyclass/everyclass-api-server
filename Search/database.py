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

