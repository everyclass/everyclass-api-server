# coding=utf-8
from flask import Blueprint

student_blue = Blueprint('student', __name__)
from .student import *
