#!/bin/bash
# 制作docker镜像
docker build --tag lemon_tree:latest .
# 启动docker实例
docker run -itd --name lemon_tree -v "$PWD":/www -p 25600:80 lemon_tree