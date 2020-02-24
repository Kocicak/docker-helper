#!/bin/bash

installDir=${installDir:-~/.doker}
read -p "Nainstalovat prikaz 'doker' do adresare '$installDir' [Y/n]? " confirm

git clone --depth=1 --branch master https://github.com/Kocicak/docker-helper.git "$installDir" || {
  error "git clone selhal"
  exit 1
}

echo "";
command="doker";
echo "Linkuji do /usr/local/bin/$command"
sudo ln -s "$installDir/doker" "/usr/local/bin/$command"

echo "hotovo."
exit 0
