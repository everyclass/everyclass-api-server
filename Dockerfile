FROM python:3.8-slim-stretch
MAINTAINER WolfBolin wolfbolin@foxmail.com
LABEL maintainer="mailto@wolfbolin.com"

# Why we need these packages?
# - procps contains useful proccess control commands like: free, kill, pkill, ps, top
# - wget is quite basic tool
# - vim for online debugging
RUN sed -i 's/deb.debian.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apt/sources.list \
	&& sed -i 's/security.debian.org/mirrors.tuna.tsinghua.edu.cn/g' /etc/apt/sources.list \
	&& apt-get update && apt-get install -y --no-install-recommends procps wget vim

# Project environment
ENV SERVICE_ENV production
ENV PIPENV_VENV_IN_PROJECT 1

WORKDIR /var/app
COPY . /var/app

RUN pip install -i https://pypi.tuna.tsinghua.edu.cn/simple --upgrade pip \
    && pip install -i https://pypi.tuna.tsinghua.edu.cn/simple gunicorn \
    && pip install -i https://pypi.tuna.tsinghua.edu.cn/simple -r requirements.txt

CMD ["ddtrace-run","gunicorn","-c","gunicorn.py","service:app"]
