FROM silintl/php7:7.2
MAINTAINER Phillip Shipley <phillip_shipley@sil.org>

ENV REFRESHED_AT 2016-12-16

# Make sure /data is available
RUN mkdir -p /data

# Copy in vhost configuration
COPY build/vhost.conf /etc/apache2/sites-enabled/

# ErrorLog inside a VirtualHost block is ineffective for unknown reasons
RUN sed -i -E 's@ErrorLog .*@ErrorLog /proc/self/fd/2@i' /etc/apache2/apache2.conf

# Copy the SimpleSAMLphp configuration files to a temporary location
COPY build/ssp-overrides /tmp/ssp-overrides

# Copy in any additional PHP ini files
COPY build/php/*.ini /etc/php/7.2/apache2/conf.d/
COPY build/php/*.ini /etc/php/7.2/cli/conf.d/

# It is expected that /data is = application/ in project folder
COPY application/ /data/

WORKDIR /data

# Install/cleanup composer dependencies
RUN composer install --prefer-dist --no-interaction --no-dev --optimize-autoloader

# Get s3-expand for ENTRYPOINT
RUN curl -o /usr/local/bin/s3-expand https://raw.githubusercontent.com/silinternational/s3-expand/master/s3-expand \
    && chmod a+x /usr/local/bin/s3-expand

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/s3-expand"]

# Record now as the build date/time (in a friendly format).
RUN date -u +"%B %-d, %Y, %-I:%M%P (%Z)" > /data/protected/data/version.txt

CMD ["/data/run.sh"]
