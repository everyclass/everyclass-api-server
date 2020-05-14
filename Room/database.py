# coding=utf-8
import json
import Common
import pymysql


# 读取课程基本信息
def read_room_info(conn, code):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT `code`,`name`,`type`,`campus`,`building` FROM `room` WHERE `code`=%s"
    cursor.execute(sql, args=[code])
    return cursor.fetchone()


def read_filter_room_list(conn, campus, building):
    room_group = Common.read_kvdb(conn, "room_group")
    room_group = json.loads(room_group)

    if campus is None:
        room_data = {}
        for item in room_group.values():
            room_data.update(item)
    else:
        room_group.setdefault(campus, {})
        room_data = room_group[campus]

    room_group = room_data
    if building is None:
        room_data = {}
        for item in room_group.values():
            room_data.update(item)
    else:
        room_group.setdefault(building, {})
        room_data = room_group[building]

    return room_data


# 读取指定节次空教室
def read_active_room(conn, week, session):
    cursor = conn.cursor(pymysql.cursors.DictCursor)
    sql = "SELECT `code`,`name`,`week%s` FROM `act_room` " \
          "WHERE `session`=%s AND `week%s` !=''"
    cursor.execute(sql, args=[week, session, week])
    return cursor.fetchall()
