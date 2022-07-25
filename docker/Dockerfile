ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
            curl \
            nginx \
            supervisor \
            git \
            telnet \
            net-tools \
            libz-dev \
            libpq-dev \
            libjpeg-dev \
            libpng-dev \
            libfreetype6-dev \
            libssl-dev \
            libmcrypt-dev \
            libonig-dev \
            vim

RUN set -eux; \
    docker-php-ext-install pdo_mysql; \
    docker-php-ext-install pdo_pgsql; \
    # Install the PHP gd library
    docker-php-ext-configure gd \
            --prefix=/usr \
            --with-jpeg \
            --with-freetype; \
    docker-php-ext-install gd;

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
    apt-utils \
    libzip-dev zip unzip && \
    docker-php-ext-configure zip; \
    docker-php-ext-install zip && \
    php -m | grep -q 'zip'


###########################################################################
# COMPOSER:
###########################################################################
ARG INSTALL_COMPOSER=true

RUN if [ ${INSTALL_COMPOSER} = true ]; then \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer\
;fi

###########################################################################
# PHP REDIS EXTENSION
###########################################################################

ARG INSTALL_PHPREDIS=true

RUN if [ ${INSTALL_PHPREDIS} = true ]; then \
    # Install Php Redis Extension
    pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis \
;fi

###########################################################################
# SERVICE CONFIGURATION
###########################################################################

RUN rm -Rf /etc/nginx/conf.d/*	\
	&& rm -Rf /etc/nginx/sites-available/default \
	&& rm -Rf /etc/nginx/sites-enabled/default

COPY ./nginx/server.conf	/etc/nginx/sites-enabled/default.conf
COPY ./supervisor/supervisord.conf /etc/supervisord.conf
COPY ./scripts/start.sh    /start.sh
RUN  chmod +x /start.sh

COPY ./	/var/www
RUN chmod -R 755 /var/www
WORKDIR /var/www

# Port 9090 is used for the GRPC listening inside the container
# Port 9092 is used for the PHP Worker Stream listening inside the container

EXPOSE 80 443 9090 9092

###########################################################################
# CONFIGURE NONE-ROOT USER
###########################################################################
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

CMD ["/start.sh"]
