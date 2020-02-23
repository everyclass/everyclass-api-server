# coding=utf-8
import re
import json
import Util
import Common
import Lesson
from flask import abort
from flask import jsonify
from Lesson.database import *
from flask import current_app as app


@Lesson.lesson_blue.route("/<code>/timetable/<semester>")
def room_timetable(code, semester):
    # 校验数据
    if re.match(r"^\w+$", code) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")
    if re.match(r"^[0-9|-]+$", semester) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询课程数据
    try:
        lesson, session = code.split("_")
    except ValueError:
        return abort(400)

    lesson_info = read_lesson_info(conn, lesson, session, semester)
    student_list = read_student_list(conn, lesson, session, semester)

    lesson_info.pop("code")
    lesson_info["teacher_list"] = json.loads(lesson_info["teacher_list"])
    lesson_info["week_list"] = json.loads(lesson_info.pop("week"))
    lesson_info["week_string"] = lesson_info.pop("week_str")
    lesson_info["name"] = lesson_info.pop("course_name")
    lesson_info["room"] = lesson_info.pop("room_name")

    for item in lesson_info["teacher_list"]:
        item["unit"] = item.pop("department")

    for item in student_list:
        item["deputy"] = item.pop("department")
        item["student_code"] = item.pop("code")

    res = {
        "status": "success",
        "semester": semester,
        "card_code": code,
        "lesson": session
    }
    res.update(lesson_info)
    res["student_list"] = student_list

    return jsonify(res)
