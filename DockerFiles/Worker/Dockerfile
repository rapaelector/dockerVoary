FROM debian:stretch

LABEL Stephan R. "m6ahenina@gmail.com"

ENV TERM xterm

RUN apt-get update --fix-missing && apt-get install -y --force-yes curl sudo vim
RUN apt-get install -y --allow-yes=true wget apt-transport-https lsb-release ca-certificates gnupg2
RUN curl --silent -fsSL https://packages.sury.org/php/apt.gpg | sudo apt-key add -

RUN printf "deb http://deb.debian.org/debian/ $(lsb_release -sc) main\ndeb-src http://deb.debian.org/debian/ $(lsb_release -sc) main\ndeb http://security.debian.org $(lsb_release -sc)/updates main\ndeb-src http://security.debian.org $(lsb_release -sc)/updates main" > /etc/apt/sources.list
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN curl --silent --location https://deb.nodesource.com/setup_12.x  | bash -
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list


RUN DEBIAN_FRONTEND="noninteractive" apt-get update && apt-get install -y --allow-yes=true \
    nginx \
    php8.0 \
    php8.0-cli \
    php8.0-fpm \
    php8.0-curl \
    php8.0-bcmath \
    php8.0-mysql \
    php8.0-intl \
    php8.0-xml \
    php8.0-intl \
    php8.0-mbstring \
    php8.0-xdebug \
    php8.0-gd \
    php8.0-sqlite3  \
    php-amqplib \
    git \
    nodejs \
    yarn \
    libpcre3-dev \
    openssh-server \
    phpunit \
    zip \
    libxrender1 \
    cron \
    nano

RUN sed -i 's/^;*clear_env = .*/clear_env = no/' /etc/php/8.0/fpm/pool.d/www.conf
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && chmod +x /usr/local/bin/composer
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN mkdir /var/run/sshd
RUN echo 'root:root' | chpasswd
RUN sed -i 's/PermitRootLogin without-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd
RUN echo "UseDNS no" >> /etc/ssh/sshd_config

RUN echo "KexAlgorithms diffie-hellman-group1-sha1" >> /etc/ssh/sshd_config

RUN echo "fastcgi_param PATH_TRANSLATED \$document_root\$fastcgi_script_name;" >> /etc/nginx/fastcgi_params

RUN mkdir /etc/nginx/ssl
RUN openssl ecparam -out /etc/nginx/ssl/nginx.key -name prime256v1 -genkey
RUN openssl req -new -batch -key /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/csr.pem
RUN openssl req -x509 -nodes -days 365 -key /etc/nginx/ssl/nginx.key -in /etc/nginx/ssl/csr.pem -out /etc/nginx/ssl/nginx.pem
RUN chmod 600 /etc/nginx/ssl/*

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

ADD docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint
RUN sed -i 's/^user  nginx;/user  www-data;/' /etc/nginx/nginx.conf

RUN printf '[PHP]\ndate.timezone = "Europe/London"\n' > /etc/php/8.0/fpm/conf.d/tzone.ini
RUN echo "apc.shm_size=128M" >> /etc/php/8.0/fpm/conf.d/20-apcu.ini
RUN sed -i "s/\(max_execution_time *= *\).*/\1180/" /etc/php/8.0/fpm/php.ini
RUN sed -i "s/\(upload_max_filesize *= *\).*/\1100M/" /etc/php/8.0/fpm/php.ini
RUN sed -i "s/\(post_max_size *= *\).*/\1100M/" /etc/php/8.0/fpm/php.ini
RUN sed -i "s/\(^.*max_input_vars *= *\).*/max_input_vars = 10000/" /etc/php/8.0/fpm/php.ini
RUN sed -i "s/\(pm.max_children = 5\).*/\pm.max_children = 50/" /etc/php/8.0/fpm/pool.d/www.conf
RUN sed -i "s/\(pm.max_spare_servers = 3\).*/\pm.max_spare_servers = 10/" /etc/php/8.0/fpm/pool.d/www.conf
RUN echo "xdebug.default_enable=1" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.profiler_output_dir=/var/www/cachegrind/" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.profiler_enable_trigger=1" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.profiler_output_name= cachegrind.out" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_enable= true" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_port= 9001" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_connect_back= 0" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.idekey= \"PHPSTORM\"" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_autostart= true" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN printf "xdebug.remote_host=" >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN /sbin/ip route|awk '/default/ { print $3; }' >> /etc/php/8.0/fpm/conf.d/20-xdebug.ini
RUN mkdir /root/.ssh/
ADD default.conf /etc/nginx/sites-available/default

EXPOSE 22 443 80
WORKDIR /var/www/

ENTRYPOINT ["docker-entrypoint"]
CMD ["nginx", "-g", "daemon off;"]
