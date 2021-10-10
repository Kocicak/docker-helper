FROM adminer:latest
USER root

# Store PHP Configs at /usr/local/etc/php/conf.d
RUN echo "upload_max_filesize = 500M" >> /usr/local/etc/php/conf.d/upload_large_dumps.ini \
    && echo "post_max_size = 500M"       >> /usr/local/etc/php/conf.d/upload_large_dumps.ini \
    && echo "memory_limit = -1"           >> /usr/local/etc/php/conf.d/upload_large_dumps.ini \
    && echo "max_execution_time = 0"      >> /usr/local/etc/php/conf.d/upload_large_dumps.ini

# aktualizace na nejnovejsi verzi driv, nez to aktualizuje ten jouda na docker hub
#ENV	ADMINER_VERSION 4.8.1

#RUN	set -x \
#&&	curl -fsSL https://github.com/vrana/adminer/releases/download/v$ADMINER_VERSION/adminer-$ADMINER_VERSION.php -o adminer.php \
#&&	curl -fsSL https://github.com/vrana/adminer/archive/v$ADMINER_VERSION.tar.gz -o source.tar.gz \
#&&	tar xzf source.tar.gz --strip-components=1 "adminer-$ADMINER_VERSION/designs/" "adminer-$ADMINER_VERSION/plugins/" \
#&&	rm source.tar.gz

RUN echo "display_errors=0"            >> /usr/local/etc/php/conf.d/no_display_errors.ini

USER adminer
