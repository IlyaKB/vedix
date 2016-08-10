# VediX native web MVC-engine

Native hierarchical "model-view-controller" engine for development "from scratch" web-applications, websites and information systems with a web interface. The core engine uses some of the ready-made solutions, such as the modified template "mustache" and popular Javascript-libraries. Back-end part using PHP as a server programming language and MySQL as database. The front-end part is usually written in HTML5, LESS/CSS3, Javascript/Typescript and popular libraries on the user's choice, for example, jQuery, Bootstrap, KnockoutJS (MVVM) and others.

Developer Site: [web-applications.ru](http://web-applications.ru).

## Setup
To start the engine needs HTTP-Apache server with PHP5 and database MySQL. Engine has been developed under the PHP 5.5.9, MySQL 5.6.16, Apache 2.4.7

You must create (import) database and configure access to it from php. Source database dump is located in the \db_dump\history.sql. The root directory of the website is \www. Connection settings found in \www\config.php, see ConfigSite class.

For Windows operating systems, add a line to the "hosts" file, for example:
```
127.0.0.1 my-host.loc
```

In the setting of Apache httpd.conf (or httpd-vhosts.conf) add a section (example):

```
NameVirtualHost *:80
#...
# my-host.loc
<VirtualHost *:80>
    ServerAdmin admin@my-host.loc
    DocumentRoot "C:/sites/myhost/www"
    ServerName my-host.loc
    ErrorLog "C:/sites/logs/my-host-error.log"
    CustomLog "C:/sites/logs/my-host-access.log" common
    <Directory "C:/sites/myhost/www">
	 Options All
	 AllowOverride All
	 Require all granted
    </Directory>
</VirtualHost>
```

## Contribute
If you like this project, please contribute to it, help me to do it better!

##License
This project under GPLv3 license
