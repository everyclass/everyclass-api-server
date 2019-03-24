#!/bin/bash
# 删除已有镜像信息
docker stop entity
docker rm entity
docker rmi everyclass_entity:latest
# 制作docker镜像
docker build --tag everyclass_entity:latest .
# 启动docker实例
docker run -itd --name entity -v "$PWD":/www -p 25601:80 everyclass_entity