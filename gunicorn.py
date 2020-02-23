# coding=utf-8
import os
import multiprocessing

if not os.path.exists('./cache/log/'):
    os.makedirs('./cache/log/')

backlog = 2048
bind = '0.0.0.0:80'
workers = multiprocessing.cpu_count() * 2
threads = 2
