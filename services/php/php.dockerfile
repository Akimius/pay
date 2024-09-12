FROM php:7.4-fpm-alpine

RUN mkdir -p /var/www/html
#RUN mkdir -p /var/www/html/public

WORKDIR /var/www/html

# Install dependencies including readline-dev
RUN apk add --no-cache \
    libzip-dev \
    curl-dev \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    bzip2-dev \
    openssl-dev \
    zlib-dev \
    icu-dev \
    mongo-c-driver-dev \
    libxslt-dev \
    git \
    zeromq-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    curl \
    gd \
    mbstring \
    json \
    opcache \
    xml \
    zip

# Install GD with JPEG and FreeType support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Install MongoDB via PECL
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

# Clone and install the ZeroMQ PHP extension manually
RUN git clone https://github.com/mkoppanen/php-zmq.git /usr/src/php/ext/zmq \
    && cd /usr/src/php/ext/zmq \
    && phpize \
    && ./configure \
    && make \
    && make install

# Enable the ZMQ PHP extension
RUN echo "extension=zmq.so" > /usr/local/etc/php/conf.d/zmq.ini

ENV PHPGROUP=laravel
ENV PHPUSER=laravel

RUN adduser -g ${PHPGROUP} -s /bin/sh -D ${PHPUSER}
RUN sed -i "s/user = www-data/user = ${PHPUSER}/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = ${PHPGROUP}/g" /usr/local/etc/php-fpm.d/www.conf

# Install linux-headers and other dependencies before installing Xdebug
#RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
#    && pecl install xdebug \
#    && docker-php-ext-enable xdebug

# For cases like php artisan jetstream:install inertia when composer is requred inside the php container
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]




