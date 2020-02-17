# coding=utf-8
import re
import Util
import Common
import Student
from Student.database import *
from flask import current_app as app


@Student.student_blue.route("/<code>")
def student_info(code):
    # 校验数据
    if re.match(r"^\w+$", code) is None:
        return Util.common_rsp("Unsupported code", status="Forbidden")

    # 连接数据库
    conn = app.mysql_pool.connection()

    # 查询数据
    student_base_info = read_student_info(conn, code)
    available_semester = Common.read_available_semester(conn, "student", code)

    res = {"status": "success"}
    res.update(student_base_info)
    res["student_code"] = res.pop("code")
    res["semester_list"] = available_semester

    return res
