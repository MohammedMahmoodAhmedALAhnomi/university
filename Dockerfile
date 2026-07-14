FROM php:8.2-apache

# تثبيت الامتدادات المطلوبة
RUN docker-php-ext-install pdo pdo_mysql mysqli

# تفعيل mod_rewrite
RUN a2enmod rewrite

# تغيير مجلد الموقع إلى public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . .

EXPOSE 80
