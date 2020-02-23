# coding=utf-8
import re
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

    room_info = read_room_info(conn, lesson, session, semester)
    course_info = read_course_info(conn, lesson, session, semester)
    student_list = read_student_list(conn, lesson, session, semester)
    teacher_list = read_teacher_list(conn, lesson, session, semester)

    room_info["room"] = room_info.pop("name")
    room_info["room_code"] = room_info.pop("code")

    for item in student_list:
        item["deputy"] = item.pop("department")
        item["student_code"] = item.pop("code")

    for item in teacher_list:
        item["unit"] = item.pop("department")
        item["teacher_code"] = item.pop("code")

    res = {
        "status": "success",
        "semester": semester,
        "card_code": code,
        "lesson": session
    }
    res.update(room_info)
    res.update(course_info)
    res["student_list"] = student_list
    res["teacher_list"] = teacher_list

    return jsonify(res)
