FROM php:7.2.15-cli
MAINTAINER WolfBolin.com

WORKDIR /www

RUN pecl install mongodb \
	&& docker-php-ext-enable mongodb

RUN php -r "copy('https://cdn.tinoy.xyz/composer-setup.php', 'composer-setup.php');" \
	&& php composer-setup.php \
	&& php -r "unlink('composer-setup.php');"

EXPOSE 80

CMD php composer.phar install && php -S 0.0.0.0:80 -t public public/index.php





