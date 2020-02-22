# coding=utf-8
from flask import Blueprint

search_blue = Blueprint('search', __name__)
from .search import *
