# Ulog

## Create a database, user and generate the tables

```
create database nulog;
create user 'nulog'@'localhost' identified by 'changeme';
grant all privileges on nulog.* to 'nulog'@'localhost';
flush privileges;
```

```
zcat /usr/share/doc/ulogd2/mysql-ulogd2.sql.gz | mysql -u changeme -p nulog
```

## Ulog configuration file

The ulog configuration is done in **ulogd.conf** (often in /etc)

This is a diff covering the changes compared to the default configuration.

```
+loglevel=1
-loglevel=3

+plugin="/usr/lib/x86_64-linux-gnu/ulogd/ulogd_output_MYSQL.so"
-#plugin="/usr/lib/x86_64-linux-gnu/ulogd/ulogd_output_MYSQL.so"

+#stack=log1:NFLOG,base1:BASE,ifi1:IFINDEX,ip2str1:IP2STR,print1:PRINTPKT,emu1:LOGEMU
-stack=log1:NFLOG,base1:BASE,ifi1:IFINDEX,ip2str1:IP2STR,print1:PRINTPKT,emu1:LOGEMU
 
+stack=log2:NFLOG,base1:BASE,ifi1:IFINDEX,ip2bin1:IP2BIN,mac2str1:HWHDR,mysql1:MYSQL
-#stack=log2:NFLOG,base1:BASE,ifi1:IFINDEX,ip2bin1:IP2BIN,mac2str1:HWHDR,mysql1:MYSQL

 [log2]
 group=1 # Group has to be different from the one use in log1
+netlink_socket_buffer_size=217088
+netlink_socket_buffer_maxsize=1085440
-#netlink_socket_buffer_size=217088
-#netlink_socket_buffer_maxsize=1085440

 [mysql1]
 db="nulog"
 host="localhost"
+user="changeme"
-user="nupik"
 table="ulog"
+pass="changemeto"
-pass="changeme"
```

## Iptables configuration for ulog

You have to tell ulog what packets to "log". This is done via iptables. You can limit the type of packets that are logged by adding filters to the iptables expression.

```
iptables -I INPUT -j NFLOG --nflog-group 1 --nflog-threshold 20
```

or use this to exclude one specific network.

```
iptables -I INPUT -j NFLOG --nflog-group 1 --nflog-threshold 20 ! -s 1.2.0.0/16
```

## Discard multiple IPs

The easiest way to discard multiple sources is by using a modified version of the ulogd init script from https://github.com/cudeso/tools/tree/master/ulogd

That init script takes a list of sources, builds a chain with IPs to ignore and then logs all the rest. Make sure that you have your INPUT chain (or what ever chain you want to track) jump to the ULOGD_exclude (or similar) chain.
