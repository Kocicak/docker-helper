#!/bin/sh
set -e

mkdir /root/.ssh
cp /tmp/.ssh/id_ed25519 /root/.ssh/id_ed25519
# cp /tmp/.ssh/id_rsa.pub /root/.ssh/id_rsa.pub
cp /tmp/.ssh/known_hosts /root/.ssh/known_hosts

chmod 700 /root/.ssh
# chmod 644 /root/.ssh/id_rsa.pub
chmod 600 /root/.ssh/id_ed25519

deployer "$@"
