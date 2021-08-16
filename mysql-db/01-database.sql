# create databases
CREATE DATABASE IF NOT EXISTS `ala`;

# create root user and grant rights
GRANT ALL ON *.* TO 'root'@'%';