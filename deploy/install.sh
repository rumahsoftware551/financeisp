#!/usr/bin/env bash

set -Eeuo pipefail

REPOSITORY_URL="https://github.com/rumahsoftware551/financeisp.git"
APP_DIR="/var/www/financeisp"
DOMAIN="${1:-}"
DB_NAME="keuangan"
DB_USER="financeisp"

if [[ "${EUID}" -ne 0 ]]; then
  echo "Jalankan installer sebagai root." >&2
  exit 1
fi

if [[ ! -r /etc/os-release ]]; then
  echo "Tidak dapat mendeteksi sistem operasi." >&2
  exit 1
fi

. /etc/os-release
if [[ "${ID}" != "ubuntu" && "${ID}" != "debian" ]]; then
  echo "Installer hanya mendukung Ubuntu atau Debian." >&2
  exit 1
fi

if [[ -e "${APP_DIR}" ]]; then
  echo "${APP_DIR} sudah ada. Installer dihentikan agar data tidak tertimpa." >&2
  exit 1
fi

export DEBIAN_FRONTEND=noninteractive
apt-get update
apt-get install -y \
  ca-certificates \
  composer \
  curl \
  git \
  mariadb-client \
  mariadb-server \
  nginx \
  openssl \
  php-cli \
  php-curl \
  php-fpm \
  php-gd \
  php-mbstring \
  php-mysql \
  php-xml \
  php-zip \
  unzip

systemctl enable --now mariadb
systemctl enable --now nginx

PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;')"
PHP_FPM_SERVICE="php${PHP_VERSION}-fpm"
PHP_FPM_SOCKET="/run/php/php${PHP_VERSION}-fpm.sock"

systemctl enable --now "${PHP_FPM_SERVICE}"

git clone --depth 1 --branch main "${REPOSITORY_URL}" "${APP_DIR}"
composer install \
  --working-dir="${APP_DIR}" \
  --no-dev \
  --no-interaction \
  --optimize-autoloader \
  --prefer-dist

install -d -m 775 -o www-data -g www-data "${APP_DIR}/gambar/bukti"
chown -R root:www-data "${APP_DIR}"
chmod 775 "${APP_DIR}/gambar" "${APP_DIR}/gambar/user" "${APP_DIR}/gambar/bukti"

DB_PASSWORD="$(openssl rand -hex 24)"
ADMIN_PASSWORD="$(openssl rand -hex 12)"

mariadb <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

mariadb "${DB_NAME}" < "${APP_DIR}/database/schema.sql"

ADMIN_HASH="$(FINANCEISP_ADMIN_PASSWORD="${ADMIN_PASSWORD}" php -r 'echo password_hash(getenv("FINANCEISP_ADMIN_PASSWORD"), PASSWORD_DEFAULT);')"
mariadb "${DB_NAME}" <<SQL
INSERT INTO user (user_nama, user_username, user_password, user_foto, user_level)
VALUES ('Administrator', 'admin', '${ADMIN_HASH}', '', 'administrator')
ON DUPLICATE KEY UPDATE
  user_nama = VALUES(user_nama),
  user_password = VALUES(user_password),
  user_level = VALUES(user_level);
SQL

install -d -m 755 /etc/nginx/snippets
install -m 640 -o root -g www-data /dev/null /etc/nginx/snippets/financeisp-db.conf
cat > /etc/nginx/snippets/financeisp-db.conf <<EOF
fastcgi_param FINANCEISP_DB_HOST 127.0.0.1;
fastcgi_param FINANCEISP_DB_PORT 3306;
fastcgi_param FINANCEISP_DB_NAME ${DB_NAME};
fastcgi_param FINANCEISP_DB_USER ${DB_USER};
fastcgi_param FINANCEISP_DB_PASSWORD ${DB_PASSWORD};
EOF

if [[ -n "${DOMAIN}" ]]; then
  SERVER_NAME="${DOMAIN}"
  LISTEN_DIRECTIVE="listen 80;"
  LISTEN_IPV6_DIRECTIVE="listen [::]:80;"
else
  SERVER_NAME="_"
  LISTEN_DIRECTIVE="listen 80 default_server;"
  LISTEN_IPV6_DIRECTIVE="listen [::]:80 default_server;"
  if [[ -L /etc/nginx/sites-enabled/default ]]; then
    unlink /etc/nginx/sites-enabled/default
  fi
fi

cat > /etc/nginx/sites-available/financeisp <<EOF
server {
    ${LISTEN_DIRECTIVE}
    ${LISTEN_IPV6_DIRECTIVE}
    server_name ${SERVER_NAME};

    root ${APP_DIR};
    index index.php index.html;

    client_max_body_size 10m;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \\.php\$ {
        include snippets/fastcgi-php.conf;
        include snippets/financeisp-db.conf;
        fastcgi_pass unix:${PHP_FPM_SOCKET};
    }

    location ~ /(?:database|deploy|scripts|library/dompdf/www)/ {
        deny all;
        return 404;
    }

    location ~ /\\. {
        deny all;
    }
}
EOF

ln -s /etc/nginx/sites-available/financeisp /etc/nginx/sites-enabled/financeisp
nginx -t
systemctl reload nginx

if command -v ufw >/dev/null 2>&1 && ufw status | grep -q '^Status: active'; then
  ufw allow OpenSSH
  ufw allow 'Nginx Full'
fi

install -m 600 /dev/null /root/financeisp-credentials.txt
cat > /root/financeisp-credentials.txt <<EOF
FinanceISP URL: http://${DOMAIN:-$(hostname -I | awk '{print $1}')}
FinanceISP username: admin
FinanceISP password: ${ADMIN_PASSWORD}
Database name: ${DB_NAME}
Database username: ${DB_USER}
Database password: ${DB_PASSWORD}
EOF

echo
echo "FinanceISP berhasil dipasang."
echo "Kredensial tersimpan di /root/financeisp-credentials.txt (mode 600)."
echo "Tampilkan dengan: sudo cat /root/financeisp-credentials.txt"
