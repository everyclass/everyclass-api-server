# coding=utf-8
import re
import Util
import Common
import Teacher
from flask import jsonify
from Teacher.database import *
from flask import current_app as app


@Teacher.teacher_blue.route("/<code>")
def teacher_info(code):
    # 校验数据
    if re.match(r"^\w+$", code) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询数据
    teacher_base_info = read_teacher_info(conn, code)
    available_semester = Common.read_available_semester(conn, "teacher", code)

    # 格式调整
    res = {"status": "success"}
    res.update(teacher_base_info)
    res["unit"] = res.pop("department")
    res["teacher_code"] = res.pop("code")
    res["semester_list"] = available_semester

    return jsonify(res)


@Teacher.teacher_blue.route("/<code>/timetable/<semester>")
def teacher_timetable(code, semester):
    # 校验数据
    if re.match(r"^\w+$", code) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")
    if re.match(r"^[0-9|-]+$", semester) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询数据
    teacher_base_info = read_teacher_info(conn, code)
    available_semester = Common.read_available_semester(conn, "teacher", code)
    lesson_data_list = Common.read_lesson_data(conn, "teacher", code, semester)

    # 格式调整
    res = {
        "status": "success",
        "semester": semester
    }
    res.update(teacher_base_info)
    res["unit"] = res.pop("department")
    res["teacher_code"] = res.pop("code")
    res["semester_list"] = available_semester
    res["card_list"] = Common.lesson2card(lesson_data_list)

    return jsonify(res)
