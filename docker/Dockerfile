FROM php:7.2-apache

MAINTAINER Scripts.robotess.net

RUN docker-php-ext-install mysqli
RUN apt-get update &&\
    apt-get install --no-install-recommends --assume-yes --quiet ca-certificates curl git &&\
    rm -rf /var/lib/apt/lists/*

RUN curl -Lsf 'https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz' | tar -C '/usr/local' -xvzf -
ENV PATH /usr/local/go/bin:$PATH
RUN go get github.com/mailhog/mhsendmail
RUN cp /root/go/bin/mhsendmail /usr/bin/mhsendmail
RUN echo 'sendmail_path = /usr/bin/mhsendmail --smtp-addr mailhog:1025' > /usr/local/etc/php/php.ini

RUN echo 'display_errors = on' > /usr/local/etc/php/php.ini \
    && echo 'log_errors = off' > /usr/local/etc/php/php.ini \
    && echo 'date.timezone = Europe/London' > /usr/local/etc/php/php.ini

RUN a2enmod rewrite
RUN service apache2 restart
