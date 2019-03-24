#!/bin/bash
# 制作docker镜像
docker build --tag everyclass-api-server:latest .
# 启动docker实例
docker run -itd --name everyclass-api-server -v "$PWD":/www -p 25600:80 lemon_tree