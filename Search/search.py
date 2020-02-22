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


@Search.search_blue.route("/query")
def search_timetable():
    key = request.args.get("key")
    if key is None:
        return abort(400)

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询搜索数据
    search_data = search_by_key(conn, key)

    # 处理搜索结果
    res = {
        "status": "success",
        "data": []
    }
    for data in search_data:
        data.pop("key")
        data[data["group"] + "_code"] = data.pop("code")
        data[convert_info[data["group"]]["info1"]] = data.pop("info1")
        data[convert_info[data["group"]]["info2"]] = data.pop("info2")
        data["semester_list"] = json.loads(data.pop("semester"))
        data["type"] = data.pop("group")
        res["data"].append(data)
    res["page_index"] = 1
    res["page_size"] = len(res["data"])
    res["page_num"] = 1
    res["count"] = len(res["data"])

    return jsonify(res)
