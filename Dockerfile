FROM alpine:latest
MAINTAINER Régis Meyssonnier <regis.meyssonnier@gmail.com>

#curl
RUN apk add curl

#postgresql
RUN apk add postgresql

#postgis
RUN apk add postgis

#nginx
RUN apk add nginx

#php
RUN apk add php81 php81-pgsql php81-fpm php81-phar php81-iconv php81-mbstring php81-openssl php81-ctype php81-tokenizer php81-session php81-simplexml php81-xml php81-dom php81-pdo_pgsql


#symfony
RUN apk add --no-cache bash \
&& curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
&& apk add symfony-cli 
#&& symfony new --no-git eiimpactproject

#composer
#RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
#&& php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
#&& php composer-setup.php \
#&& php -r "unlink('composer-setup.php');"

#node
RUN apk add npm \
&& npm -g install create-react-app 
#&& create-react-app eiimpactprojectreactjs

#sh
RUN apk add dash

#git
RUN apk add git

#yarn
RUN apk add yarn

ADD ./index.html /var/www/index.html
ADD ./index.php /var/www/index.php
ADD ./nginx_defconf.conf /etc/nginx/http.d/default.conf
ADD ./fastcgi.conf /etc/nginx/fastcgi.conf

ENV PGDATA=/home/regis/data

EXPOSE 80

#user 
RUN adduser -D -g '' regis
RUN mkdir /home/regis/data && chmod 0775 /home/regis/data && chown regis /home/regis/data \
&& mkdir /run/postgresql && chown regis /run/postgresql 

ADD ./initsh.sh /home/regis/initsh.sh
RUN chmod 7777 /home/regis/initsh.sh \
&& awk '{ sub("\r$", ""); print }' /home/regis/initsh.sh > /home/regis/insh.sh \
&& chmod 7777 /home/regis/insh.sh

ADD ./initpg.sh /home/regis/initpg.sh
RUN chmod 7777 /home/regis/initpg.sh \
&& awk '{ sub("\r$", ""); print }' /home/regis/initpg.sh > /home/regis/inpg.sh \
&& chmod 7777 /home/regis/inpg.sh

ADD ./initdb.sh /home/regis/initdb.sh
RUN chmod 7777 /home/regis/initdb.sh\
&& awk '{ sub("\r$", ""); print }' /home/regis/initdb.sh> /home/regis/indb.sh\
&& chmod 7777 /home/regis/indb.sh

ADD ./installer /home/regis/installer.php
RUN chmod 7777 /home/regis/installer.php\
&& awk '{ sub("\r$", ""); print }' /home/regis/installer.php > /home/regis/install.php\
&& chmod 7777 /home/regis/install.php

ADD ./database.sql /home/regis/database.sql
RUN chmod 7777 /home/regis/database.sql

ADD ./role.sql /home/regis/role.sql
RUN chmod 7777 /home/regis/role.sql

ADD ./data_sensors.json /home/regis/data_sensors.json
RUN chmod 7777 /home/regis/data_sensors.json

RUN dash









