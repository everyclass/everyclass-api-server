# coding=utf-8
import re
import json
import Util
import Room
import Common
from flask import abort
from flask import request
from flask import jsonify
from Room.database import *
from flask import current_app as app


@Room.room_blue.route('/')
def room_group():
    conn = app.mysql_pool.connection()

    room_group_dict = Common.read_kvdb(conn, "room_group")
    room_group_dict = json.loads(room_group_dict)

    res = {
        "status": "OK",
        "room_group": room_group_dict
    }

    return jsonify(res)


@Room.room_blue.route("/<code>")
def room_info(code):
    # 校验数据
    if re.match(r"^\w+$", code) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询数据
    room_base_info = read_room_info(conn, code)
    available_semester = Common.read_available_semester(conn, code, "room")

    # 格式调整
    res = {
        "status": "success"
    }
    res.update(room_base_info)
    res["room_code"] = res.pop("code")
    res["semester_list"] = available_semester

    return jsonify(res)


@Room.room_blue.route("/<code>/timetable/<semester>")
def room_timetable(code, semester):
    # 校验数据
    if re.match(r"^\w+$", code) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")
    if re.match(r"^[0-9|-]+$", semester) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询数据
    room_base_info = read_room_info(conn, code)
    available_semester = Common.read_available_semester(conn, code, "room")
    room_data_list = Common.read_lesson_data(conn, code, "room", semester)

    # 格式调整
    res = {
        "status": "success",
        "semester": semester
    }
    res.update(room_base_info)
    res["room_code"] = res.pop("code")
    res["semester_list"] = available_semester
    res["card_list"] = Common.lesson2card(room_data_list)

    return jsonify(res)
