FROM php:cli-alpine

ENV DEPLOYER_VERSION=6.8.0

RUN apk update --no-cache \
    && apk add --no-cache \
        openssh-client git rsync

RUN curl -L https://deployer.org/releases/v$DEPLOYER_VERSION/deployer.phar > /usr/local/bin/deployer \
    && chmod +x /usr/local/bin/deployer

VOLUME ["/project", "$HOME/.ssh"]
WORKDIR /project

COPY docker-entrypoint.sh /bin/docker-entrypoint.sh
RUN chmod +x /bin/docker-entrypoint.sh
ENTRYPOINT ["/bin/docker-entrypoint.sh"]