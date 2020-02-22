# coding=utf-8
import json


def lesson2card(lesson_data):
    for lesson in lesson_data:
        lesson["name"] = lesson.pop("course_name")
        lesson["room"] = lesson.pop("room_name")
        lesson["lesson"], lesson["session"] = lesson["session"], lesson["lesson"]
        lesson["card_code"] = lesson.pop("session") + "_" + lesson["lesson"]
        lesson["week_list"] = lesson.pop("week")
        lesson["week_string"] = lesson.pop("week_str")
        lesson["week_list"] = json.loads(lesson["week_list"])
        lesson["teacher_list"] = json.loads(lesson["teacher_list"])
        for teacher in lesson["teacher_list"]:
            teacher["teacher_code"] = teacher.pop("code")
    return lesson_data
