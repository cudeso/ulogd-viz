# Requirements

ulogd-viz depends on a LAMP stack with ulogd installed

------------------------------------------------------------------------------------------

# Apache

Apply user authentication and restriction on the web server level.

# Mysql

sudo apt-get install mysql-server php5-mysql

# PHP

## Pear

sudo apt-get install php-pear
sudo pear install Net_GeoIP

## GeopIP

Make sure that there's a geoip.dat file on the location defined in the ini file. You can get a version from Maxmind.
 http://dev.maxmind.com/geoip/legacy/geolite/

```
[geoip]
database = "/var/www/html/ulogd-viz/library/geoipdb.dat"
```

# Ulog

## ulogd2-msql

```
sudo apt-get install ulogd2-mysql
sudo chown ulog /var/log/ulog/
```
