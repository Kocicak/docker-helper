FROM php:8.2-cli

RUN apt-get update && apt-get install -y openssh-client git rsync

ENV DEPLOYER_VERSION=7.3.0
RUN curl -L https://github.com/deployphp/deployer/releases/download/v$DEPLOYER_VERSION/deployer.phar > /usr/local/bin/deployer \
    && chmod +x /usr/local/bin/deployer

VOLUME ["/project", "$HOME/.ssh"]
WORKDIR /project

COPY docker-entrypoint.sh /bin/docker-entrypoint.sh
RUN chmod +x /bin/docker-entrypoint.sh
RUN git config --global --add safe.directory '*'
ENTRYPOINT ["/bin/docker-entrypoint.sh"]