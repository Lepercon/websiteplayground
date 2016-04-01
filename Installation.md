# Installation Instructions
1. Install Apache [httpd server](https://httpd.apache.org/) (via [WAMP](http://www.wampserver.com/en/)/[MAMP](https://www.mamp.info/en/) if preferred)
1. Install [MySQL](https://www.mysql.com/) (If not installed with WAMP)
1. Install [phpmyadmin](https://www.phpmyadmin.net/) (If not installed with WAMP)
1. Import the database `db/mysql.sql`
1. Copy files in `application/config/local` to `application/config/development`
1. Set details in `application/config/development/database.php`
1. Rename `index.development.php` to `index.php`
1. Set up apache alis url for `localhost/butler.jcr/` by either:
   1. Edit the apache [config files](https://httpd.apache.org/docs/current/mod/mod_alias.html)
   1. Graphically In [WAMP](http://www.techrepublic.com/blog/smb-technologist/create-aliases-on-your-wamp-server/)
1. Start apache and MySQL