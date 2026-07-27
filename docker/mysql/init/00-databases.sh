#!/bin/bash
set -e

mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS keycloak;
    CREATE USER IF NOT EXISTS 'keycloak'@'%' IDENTIFIED BY '${KC_DB_PASSWORD}';
    GRANT ALL PRIVILEGES ON keycloak.* TO 'keycloak'@'%';
    FLUSH PRIVILEGES;
EOSQL
