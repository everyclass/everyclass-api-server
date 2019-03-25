FROM php:7.2.16-cli-stretch
MAINTAINER WolfBolin wolfbolin@foxmail.com

WORKDIR /www

RUN apt-get update \
	&& apt-get install git -y --no-install-recommends \
	&& rm -rf /var/lib/apt/lists/* \
	&& pecl install mongodb \
	&& docker-php-ext-enable mongodb \
	&& docker-php-ext-install mysqli \
	&& php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
	&& php composer-setup.php \
	&& php -r "unlink('composer-setup.php');"

COPY . /www

RUN php composer.phar install

EXPOSE 80

CMD php -S 0.0.0.0:80 -t public public/index.php