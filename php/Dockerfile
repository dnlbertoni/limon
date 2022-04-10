# A basic apache server. To use either add or bind mount content under /var/www
FROM ubuntu:14.04

# System 
#ENV TIMEZONE Etc/UTC
ENV DEBIAN_FRONTEND noninteractive

# Locale specific
#ENV LANGUAGE en_US.UTF-8
#ENV LANG en_US.UTF-8
#ENV LC_ALL en_US.UTF-8
#ENV LC_CTYPE en_US.UTF-8
#ENV LC_MESSAGES en_US.UTF-8

RUN apt-get update 
RUN apt-get install -y apache2 
RUN apt-get install -y wget curl locales tzdata m4
RUN apt-get install -y php5 libapache2-mod-php5 php5-mysql php5-cli php5-gd php5-curl
RUN apt-get clean 
RUN rm -rf /var/lib/apt/lists/*

ENV APACHE_RUN_USER                         www-data
ENV APACHE_RUN_GROUP                        www-data
ENV APACHE_LOG_DIR                          /var/log/apache2
ENV APACHE_PID_FILE                         /var/run/apache2.pid
ENV APACHE_RUN_DIR                          /var/run/apache2
ENV APACHE_LOCK_DIR                         /var/lock/apache2
ENV APACHE_SERVER_ADMIN                     admin@localhost
ENV APACHE_SERVER_NAME                      localhost
ENV APACHE_SERVER_ALIAS                     docker.localhost
ENV APACHE_DOCUMENT_ROOT                    /var/www/html

ENV APACHE_WORKER_START_SERVERS             2
ENV APACHE_WORKER_MIN_SPARE_THREADS         2
ENV APACHE_WORKER_MAX_SPARE_THREADS         10
ENV APACHE_WORKER_THREAD_LIMIT              64
ENV APACHE_WORKER_THREADS_PER_CHILD         25
ENV APACHE_WORKER_MAX_REQUEST_WORKERS       4
ENV APACHE_WORKER_MAX_CONNECTIONS_PER_CHILD 0

# Configure timezone and locale
#RUN locale-gen $LANGUAGE && \
#    dpkg-reconfigure locales && \
#    echo "$TIMEZONE" > /etc/timezone && \
#    dpkg-reconfigure -f noninteractive tzdata


RUN mkdir -p /var/www/httpsdocs
RUN chown www-data:www-data -R /var/www
RUN sed -i 's/short_open_tag = Off/short_open_tag = On/' /etc/php5/apache2/php.ini

#COPY ./apache2.conf /etc/apache2/
#COPY ./httpd.conf /etc/apache2/
#COPY ./rewrite.load /etc/apache2/mods-enabled/
#COPY ./default /etc/apache2/sites-available/

VOLUME ["/var/www"]

EXPOSE 80

CMD ["/usr/sbin/apache2", "-D", "FOREGROUND"]
