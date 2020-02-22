# coding=utf-8
from flask import Blueprint

lesson_blue = Blueprint('lesson', __name__)
from .lesson import *
