# coding=utf-8
from flask import Blueprint

info_blue = Blueprint('info', __name__)
from .info import *
