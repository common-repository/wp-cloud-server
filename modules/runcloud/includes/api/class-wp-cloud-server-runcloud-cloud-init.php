<?php

/**
 * WP Cloud Server - RunCloud Module Cloud Init Data
 *
 * @author		Gary Jordan (gary@designedforpixels.com)
 * @since      	1.0.0
 *
 * @package    	WP_Cloud_Server_RunCloud
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Cloud_Server_RunCloud_User_Meta {

    /**
     * Returns the script
     *
     * @since    1.0.0
     */
    public static function wordpress_install_script( $domain, $admin_user, $admin_password, $admin_email, $wp_database, $wp_db_user ) {

    return $script = <<<EOF
#cloud-config
package_update: true
packages:
  - unzip
  - wget
write_files:
  - path: /setup/scripts/wordpress_plugins.sh
    content: |
      #!/bin/bash
      # Version 0.0.1: Initial Release (05/10/2019)
      PLUGINS="false"
      SSL_PLUGIN="really-simple-ssl"
      # Configure WordPress Plugins
      if [ \$PLUGINS == "true" ]; then
          echo "Installing & Activating Plugins"
          wp plugin install \$SSL_PLUGIN --allow-root --path='/var/www/$domain/htdocs'
      fi
  - path: /tmp/secure_our_mysql.sh
    content: |
      #!/usr/bin/expect

      spawn /usr/bin/mysql_secure_installation

      expect "Enter password for user root:"
      send "[lindex \$argv 0]\\r"

      expect "Press y|Y for Yes, any other key for No:"
      send "y\\r"

      expect "Please enter 0 = LOW, 1 = MEDIUM and 2 = STRONG:"
      send "1\\r"

      expect "Change the password for root ? ((Press y|Y for Yes, any other key for No) :"
      send "n\\r"

      expect "Remove anonymous users? (Press y|Y for Yes, any other key for No) :"
      send "y\\r"

      expect "Disallow root login remotely? (Press y|Y for Yes, any other key for No) :"
      send "y\\r"

      expect "Remove test database and access to it? (Press y|Y for Yes, any other key for No) :"
      send "y\\r"

      expect "Reload privilege tables now? (Press y|Y for Yes, any other key for No) :"
      send "y\\r"

      expect eof 
  - path: /etc/apache2/apache2.conf.new
    content: |
      DefaultRuntimeDir \${APACHE_RUN_DIR}
      Mutex file:\${APACHE_LOCK_DIR} default
      PidFile \${APACHE_PID_FILE}
      Timeout 300
      KeepAlive On
      MaxKeepAliveRequests 100
      KeepAliveTimeout 5
      User \${APACHE_RUN_USER}
      Group \${APACHE_RUN_GROUP}
      HostnameLookups Off
      ErrorLog \${APACHE_LOG_DIR}/error.log
      LogLevel warn
      IncludeOptional mods-enabled/*.load
      IncludeOptional mods-enabled/*.conf
      Include ports.conf

      <Directory />
        Options FollowSymLinks
        AllowOverride None
        Require all denied
      </Directory>

      <Directory /usr/share>
        AllowOverride None
        Require all granted
      </Directory>

      <Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
      </Directory>

      AccessFileName .htaccess

      <FilesMatch "^\.ht">
        Require all denied
      </FilesMatch>

      LogFormat "%v:%p %h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" vhost_combined
      LogFormat "%h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" combined
      LogFormat "%h %l %u %t \"%r\" %>s %O" common
      LogFormat "%{Referer}i -> %U" referer
      LogFormat "%{User-agent}i" agent

      IncludeOptional conf-enabled/*.conf
      IncludeOptional sites-enabled/*.conf
  - path: /etc/apache2/mods-enabled/dir.conf.new
    content: |
      <IfModule mod_dir.c>
          DirectoryIndex index.php index.html index.cgi index.pl index.xhtml index.htm
      </IfModule>
  - path: /etc/apache2/sites-available/$domain.conf
    content: |
      <VirtualHost *:80>
        <Directory /var/www/$domain/htdocs>
          allow from all
          Options -Indexes
        </Directory>
        ServerAdmin admin@$domain
        ServerName $domain
        ServerAlias www.$domain
        DocumentRoot /var/www/$domain/htdocs
        ErrorLog \${APACHE_LOG_DIR}/error.log
        CustomLog \${APACHE_LOG_DIR}/access.log combined
      </VirtualHost>
  - path: /tmp/composer.json
    content: |
      {
        "name"        : "$domain",
        "description" : "RunCloud WordPress VPS Setup via Composer",
        "authors"     : [
            {
              "name"    : "Gary Jordan",
              "email"   : "support@designedforpixels.com",
              "homepage": "https://designedforpixels.com/"
            }
        ],
        "type"        : "website",
        "minimum-stability": "stable",
        "repositories": [
            {
              "type": "composer",
              "url" : "https://wpackagist.org"
            },
            {
              "type": "composer",
              "url" : "https://rarst.net"
            }
        ],
        "config"      : {
            "vendor-dir":  "vendor"
        },
        "require"     : {
            "johnpbloch/wordpress"                        : "*"
        },
        "require-dev" : {
            "wpackagist-plugin/log-deprecated-notices"    : "*"
        },
        "extra"       : {
            "wordpress-install-dir": "/var/www/$domain/htdocs/",
            "installer-paths": {
                "/var/www/$domain/htdocs/wp-content/plugins/{\$name}" : ["type:wordpress-plugin"],
                "/var/www/$domain/htdocs/wp-content/themes/{\$name}"  : ["type:wordpress-theme"]
            }
        }
      }
  - path: /tmp/wp-cli.yml
    content: |
      # Global parameter defaults
      path: wp-core
      url: https://$domain
      user: $admin_user
      color: false
      disabled_commands:
        - db drop
      require:
        - path-to/command.php
    
      # Subcommand defaults (e.g. `wp config create`)
      config create:
        extra-php: |
          define( 'WP_CONTENT_DIR', dirname(__FILE__) . '/' );
          define( 'WP_SITEURL',     'https://$domain');
          define( 'WP_HOME',    'https://$domain');
          define( 'WP_CONTENT_URL', 'https://$domain');	
runcmd:
  - mkdir -p -m770 /setup/scripts
  - cp /etc/shadow /etc/shadow.orig
  - sed -i 's/^root:.*$/root:*:16231:0:99999:7:::/' /etc/shadow
  - export HOME="/root"
  - export DEBIAN_FRONTEND="noninteractive"
  - export LANG=C.UTF-8
  - echo "*** Set-up Additional Repositories ***"
  - apt-get update
  - add-apt-repository -y  ppa:ondrej/php
  - add-apt-repository -y ppa:certbot/certbot
  - apt-get update
  - wait $!
  - apt-get -y install ca-certificates software-properties-common openssl libcurl4-openssl-dev --allow-unauthenticated
  - echo "*** Install PHP 7.3 ***"
  - apt-get -y install php7.3 --allow-unauthenticated
  - apt-get -y install libapache2-mod-php7.3 php7.3-curl php7.3-gd php7.3-mbstring php7.3-mysql --allow-unauthenticated
  - apt-get -y install php-cli php7.3-gmp php7.3-xml php7.3-imap php7.3-ldap php-mailparse --allow-unauthenticated
  - echo "*** Install Composer ***"
  - apt-get -y install curl git unzip --allow-unauthenticated
  - apt-get -y install composer --allow-unauthenticated
  - echo "*** Install apache2 ***"
  - apt-get -y install apache2
  - mkdir -p /var/www/$domain/htdocs
  - mv /etc/apache2/mods-enabled/dir.conf.new /etc/apache2/mods-enabled/dir.conf
  - cp /etc/apache2/apache2.conf /etc/apache2/apache2.conf.orig
  - mv /etc/apache2/apache2.conf.new /etc/apache2/apache2.conf
  - sudo a2enmod rewrite
  - systemctl restart apache2
  - systemctl status apache2 > /tmp/apache-status.log
  - php5.6 -i | grep mailparse > /tmp/mailparse.log
  - echo "*** Create MySQL Username & Password ***"
  - rootmysqluser=root
  - password1=`dd if=/dev/urandom bs=1 count=4 2>/dev/null | base64 -w 0 | rev | cut -b 2- | rev | tr -dc 'a-zA-Z0-9'`
  - password2=`dd if=/dev/urandom bs=1 count=4 2>/dev/null | base64 -w 0 | rev | cut -b 2- | rev | tr -dc 'a-zA-Z0-9'`
  - rootmysqlpass="#9Nj\$password1"
  - wp_sqlpass="@Ry6\$password2"
  - echo "*** Install MySQL ***"
  - apt-get -y install debconf-utils
  - echo "mysql-server mysql-server/root_password password \$rootmysqlpass" | debconf-set-selections
  - echo "mysql-server mysql-server/root_password_again password \$rootmysqlpass" | debconf-set-selections
  - debconf-get-selections | grep mysql >> /home/ubuntu/mysql.log
  - apt-get -y install mysql-server
  - apt-get -y install mysql-client  
  - systemctl start mysql  
  - apt-get -y install expect
  - cd /tmp
  - chmod +x secure_our_mysql.sh
  - expect /tmp/secure_our_mysql.sh \$rootmysqlpass
  - wait $!
  - echo "*** Create WordPress Database ***"
  - /usr/bin/mysqladmin -u \$rootmysqluser -p\$rootmysqlpass create $wp_database
  - >
    /usr/bin/mysql -u \$rootmysqluser -p\$rootmysqlpass -e "CREATE USER '$wp_db_user'@'localhost' IDENTIFIED BY '"\$wp_sqlpass"';
    GRANT ALL PRIVILEGES ON *.* TO '$wp_db_user'@'localhost';
    CREATE USER '$wp_db_user'@'%' IDENTIFIED BY '"\$wp_sqlpass"';
    GRANT ALL PRIVILEGES ON *.* TO '$wp_db_user'@'%';
    FLUSH PRIVILEGES;"   
  - systemctl restart apache2
  - echo "*** Set-up Default MySQL User ***"
  - echo "[client]" > /etc/mysql/conf.d/mysql.cnf
  - echo "user = wp_db_user" >> /etc/mysql/conf.d/mysql.cnf
  - echo "password = \$wp_sqlpass" >> /etc/mysql/conf.d/mysql.cnf
  - systemctl start mysql
  - echo "*** Install & Configure Sendmail (for PHP Mail) ***"
  - apt-get -y install sendmail
  - echo "*** Download & Install WordPress ***"
  - cd /var/www/$domain/htdocs
  - mv /tmp/composer.json /var/www/$domain/htdocs
  - mv /tmp/wp-cli.yml  /var/www/$domain/htdocs
  - composer install --prefer-dist
  - echo "*** Download & Install WP-CLI ***"
  - wget https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
  - chmod +x wp-cli.phar
  - mv wp-cli.phar /usr/local/bin/wp
  - wp core config --dbname=$wp_database --dbuser=$wp_db_user --dbpass=\$wp_sqlpass --dbhost='Localhost' --dbprefix='wp_' --allow-root
  - wp core install --url=$domain --title="New WordPress Site" --admin_user=$admin_user --admin_password=$admin_password --admin_email=$admin_email --allow-root
  - cd /setup/scripts
  - chmod +x wordpress_plugins.sh
  - bash wordpress_plugins.sh 
  - wait $!
  - echo "*** Configure Websites ***"
  - a2ensite $domain.conf
  - a2dissite 000-default.conf
  - echo "*** Final Apache Restart ***"
  - systemctl reload apache2
  - echo "*** WordPress Install Complete ***"
  - cp /etc/shadow.orig /etc/shadow
EOF;
    }
    
}