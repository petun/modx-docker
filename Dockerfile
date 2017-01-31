FROM webdevops/php-apache

COPY ./scripts/addsite.sh /app
COPY ./scripts/init_structure.php /app
COPY ./scripts/package.php /app
COPY ./scripts/config.xml /app

RUN /app/addsite.sh