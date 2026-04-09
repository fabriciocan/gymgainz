FROM mcr.microsoft.com/devcontainers/php:8.4

# Extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    libldap2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install \
        ldap \
        pdo_pgsql \
        pgsql \
        pcntl \
        zip \
        bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 22 LTS
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configura php.ini para desenvolvimento
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/dev.ini \
    && echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/dev.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/dev.ini
