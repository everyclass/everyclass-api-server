# coding=utf-8
import re
import json
import Util
import Room
import Common
from flask import abort
from flask import url_for
from flask import jsonify
from flask import request
from flask import redirect
from Room.database import *
from flask import current_app as app


@Room.room_blue.route("")
def room_group_k():
    return redirect(url_for("room.room_group"))


@Room.room_blue.route("/")
def room_group_x():
    return redirect(url_for("room.room_group"))


@Room.room_blue.route("/group")
def room_group():
    conn = app.mysql_pool.connection()

    room_group_dict = Common.read_kvdb(conn, "room_group")
    room_group_dict = json.loads(room_group_dict)

    res = {
        "status": "OK",
        "room_group": room_group_dict
    }

    return jsonify(res)


@Room.room_blue.route("/status")
def room_status():
    week = request.args.get("week")
    campus = request.args.get("campus")
    session = request.args.get("session")
    building = request.args.get("building")
    if week is None or session is None:
        return abort(400)

    week = int(week)
    week = max(1, week)
    week = min(week, 20)

    conn = app.mysql_pool.connection()
    room_status_list = read_active_room(conn, week, session)
    filter_room_data = read_filter_room_list(conn, campus, building)

    act_room = {}
    for room in room_status_list:
        room["data"] = json.loads(room.pop("week%s" % week))
        room["status"] = "active"
        act_room[room["code"]] = room

    room_list = []
    for key, val in filter_room_data.items():
        if key in act_room.keys():
            room_list.append(act_room[key])
        else:
            room_list.append({
                "code": key,
                "name": val,
                "status": "available"
            })

    res = {
        "status": "OK",
        "room_status": room_list
    }

    return jsonify(res)


@Room.room_blue.route("/available")
def available_room():
    week = request.args.get("week")
    campus = request.args.get("campus")
    session = request.args.get("session")
    building = request.args.get("building")
    if week is None or session is None:
        return abort(400)

    week = int(week)
    week = max(1, week)
    week = min(week, 20)

    conn = app.mysql_pool.connection()
    room_status_list = read_active_room(conn, week, session)
    filter_room_data = read_filter_room_list(conn, campus, building)

    act_room = {}
    for room in room_status_list:
        room["data"] = json.loads(room.pop("week%s" % week))
        room["status"] = "active"
        act_room[room["code"]] = room

    room_list = []
    for key, val in filter_room_data.items():
        if key in act_room.keys():
            continue
        else:
            room_list.append({
                "code": key,
                "name": val,
                "status": "available"
            })

    res = {
        "status": "OK",
        "available_room": room_list
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
