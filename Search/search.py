# coding=utf-8
import re
import json
import Util
import Common
import Search
from flask import abort
from flask import jsonify
from flask import request
from Search.database import *
from flask import current_app as app

convert_info = {
    "room": {"info1": "campus", "info2": "building"},
    "course": {"info1": "type", "info2": "faculty"},
    "teacher": {"info1": "title", "info2": "unit"},
    "student": {"info1": "class", "info2": "deputy"},
}
group_type = ["course", "teacher", "student", "room"]


@Search.search_blue.route("/query")
def search_timetable():
    key = request.args.get("key")
    group = request.args.get("group")
    if key is None:
        return abort(400)
    if group is not None and group not in group_type:
        return abort(400)

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询搜索数据
    if group is None:
        search_data = search_by_key(conn, key)
    else:
        search_data = search_by_key_with_group(conn, key, group)

    # 处理搜索结果
    res = {
        "status": "OK",
        "data": []
    }

    for data in search_data:
        data.pop("key")
        # data[data["group"] + "_code"] = data.pop("code")
        data[convert_info[data["group"]]["info1"]] = data.pop("info1")
        data[convert_info[data["group"]]["info2"]] = data.pop("info2")
        data["semester_list"] = json.loads(data.pop("semester"))
        res["data"].append(data)

    return jsonify(res)


@Search.search_blue.route('/room/available')
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
    available_room_list = read_available_room(conn, week, session)

    if campus is not None:
        room_group_dict = Common.read_kvdb(conn, "room_group")
        room_group_dict = json.loads(room_group_dict)

        if campus not in room_group_dict.keys():
            return abort(400)

        building_list = room_group_dict[campus]

        if building is not None:
            if building not in building_list.keys():
                return abort(400)

            filter_list = building_list[building]
        else:
            filter_list = []
            for item in building_list.values():
                filter_list += item

        room_list = []
        for room in available_room_list:
            if room["code"] in filter_list:
                room_list.append(room)
        available_room_list = room_list

    res = {
        "status": "OK",
        "available_room": available_room_list
    }

    return jsonify(res)


