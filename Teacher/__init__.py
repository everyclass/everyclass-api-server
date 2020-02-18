# coding=utf-8
from flask import Blueprint

teacher_blue = Blueprint('teacher', __name__)
from .teacher import *
