FROM webdevops/php-apache

RUN mkdir /scripts
COPY ./scripts/addsite.sh /scripts/
COPY ./scripts/init_structure.php /scripts/
COPY ./scripts/package.php /scripts/
COPY ./scripts/config.xml /scripts/