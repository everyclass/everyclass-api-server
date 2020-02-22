# coding=utf-8
from flask import Blueprint

room_blue = Blueprint('room', __name__)
from .room import *
