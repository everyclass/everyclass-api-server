# coding=utf-8
import os
import Util
import Config
import pymysql
import sentry_sdk
from flask import Flask
from flask import jsonify
from flask_cors import CORS
from ddtrace import tracer
from ddtrace import patch_all
from DBUtils.PooledDB import PooledDB
from sentry_sdk.integrations.flask import FlaskIntegration

from Info import info_blue
from Room import room_blue
from Search import search_blue
from Lesson import lesson_blue
from Student import student_blue
from Teacher import teacher_blue

# 获取配置
app_config = Config.get_config()
base_path = os.path.split(os.path.abspath(__file__))[0]

# Sentry
sentry_sdk.init(
    dsn=app_config['SENTRY']['dsn'],
    integrations=[FlaskIntegration()],
    environment=os.environ.get("SERVICE_ENV", "unknown")
)

# DataDog
patch_all()
tracer.configure(
    hostname=app_config["DDOG"]["host"],
    port=app_config["DDOG"]["port"]
)

# 初始化应用
app = Flask(__name__)
app.config.from_mapping(app_config)

# 初始化连接池
for key in app.config.get('POOL').keys():
    app.config.get('POOL')[key] = int(app.config.get('POOL')[key])
app.config.get('MYSQL')["port"] = int(app.config.get('MYSQL')["port"])
pool_config = app.config.get('POOL')
mysql_config = app.config.get('MYSQL')
app.mysql_pool = PooledDB(creator=pymysql, **mysql_config, **pool_config)

# 初始化路由
app.register_blueprint(info_blue, url_prefix='/info')
app.register_blueprint(room_blue, url_prefix='/room')
app.register_blueprint(search_blue, url_prefix='/search')
app.register_blueprint(lesson_blue, url_prefix='/lesson')
app.register_blueprint(student_blue, url_prefix='/student')
app.register_blueprint(teacher_blue, url_prefix='/teacher')
CORS(app, supports_credentials=True, resources={r"/*": {"origins": "*"}})


@app.route("/")
def hello_world():
    data = {
        "status": "success",
        "info": "Hello, world!"
    }
    return jsonify(data)


@app.errorhandler(400)
def http_forbidden(msg):
    Util.print_red("{}: <HTTP 400> {}".format(Util.str_time(), msg))
    return Util.common_rsp("Bad Request", status='Bad Request')


@app.errorhandler(403)
def http_forbidden(msg):
    return Util.common_rsp(str(msg)[15:], status='Forbidden')


@app.errorhandler(404)
def http_not_found(msg):
    return Util.common_rsp(str(msg)[15:], status='Not Found')


@app.errorhandler(500)
def service_error(msg):
    Util.print_red("{}: <HTTP 500> {}".format(Util.str_time(), msg))
    return Util.common_rsp(str(msg)[15:], status='Internal Server Error')


if __name__ == '__main__':
    app.run(host=app_config['HOST'], port=app_config['PORT'], debug=True)
    exit()
