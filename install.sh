#!/bin/bash
AT=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)
read -p 'Enter Your Linode Token: ' TOKEN </dev/tty
read -p 'Enter Number of proxies: ' PROXNUM </dev/tty
sudo apt-get -y update
#sudo apt-get -y upgrade
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get -y update
sudo apt-get -y install apache2 libapache2-mod-php7.2 php7.2 php7.2-xml php7.2-gd php7.2-opcache php7.2-mbstring php7.2-curl memcached php-memcached
sudo service apache2 restart
sudo wget https://yt-dl.org/latest/youtube-dl -O /usr/local/bin/youtube-dl
sudo chmod a+x /usr/local/bin/youtube-dl
hash -r
sudo chmod a+x /usr/local/bin/youtube-dl
rm /var/www/html/*
git clone https://github.com/majedoh/lin_y /var/www/html
mv /var/www/html/config-sample.php /var/www/html/config.php
sed -i "s/\"Your Linode access token\"/\"$TOKEN\"/g" /var/www/html/config.php
sed -i "s/3/$PROXNUM/g" /var/www/html/config.php
sed -i "s/AT/$AT/g" /var/www/html/config.php
sudo service apache2 restart
php /var/www/html/create.php
echo "Your access token:"
echo $AT
echo "Please allow 5 minutes before you start using site"
