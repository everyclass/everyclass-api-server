# coding=utf-8
import os
import Util
import Config
import pymysql
# import sentry_sdk
from flask import Flask
from flask_cors import CORS
from DBUtils.PooledDB import PooledDB

# 获取配置
app_config = Config.get_config()
base_path = os.path.split(os.path.abspath(__file__))[0]

# Sentry
# sentry_sdk.init(
#     dsn=app_config['SENTRY']['dsn'],
#     integrations=[FlaskIntegration()]
# )

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
# app.register_blueprint(webpage_blue, url_prefix='/webpage')
# app.register_blueprint(network_blue, url_prefix='/network')
# app.register_blueprint(message_blue, url_prefix='/message')
# app.register_blueprint(monitor_blue, url_prefix='/monitor')
CORS(app, supports_credentials=True, resources={r"/*": {"origins": "*"}})


@app.route('/debug/sentry')
def sentry_debug():
    Util.print_red("Test sentry: {}".format(1 / 0), tag="DEBUG")
    return Util.common_rsp("DEBUG")


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
    app.run(host=app_config['HOST'], port=app_config['PORT'])
    exit()
