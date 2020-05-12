# coding=utf-8
import json
import Util
import Info
import Common
from flask import abort
from flask import request
from flask import jsonify
from Info.database import *
from flask import current_app as app


@Info.info_blue.route("/service")
def service_info():
    conn = app.mysql_pool.connection()
    data = {
        "status": "success",
        "version": app.config["BASE"]["version"],
        "service_state": Common.read_kvdb(conn, "service_state"),
        "service_notice": Common.read_kvdb(conn, "service_notice"),
        "data_time": Common.read_kvdb(conn, "data_time")
    }
    return Util.common_rsp(data)


@Info.info_blue.route('/sentry-debug')
def sentry_debug():
    Util.print_red("Test sentry: {}".format(1 / 0), tag="DEBUG")
    return Util.common_rsp("DEBUG")
