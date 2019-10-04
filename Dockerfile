FROM thiagobarradas/lamp:php-5.6
LABEL description="Environment of LANP"
COPY ./SamplePage.php /var/www/html/
COPY ./inc /var/www/inc
RUN rm /var/www/html/index.html
RUN rm /var/www/html/phpinfo.php

