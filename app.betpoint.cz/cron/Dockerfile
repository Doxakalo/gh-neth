FROM php:8.4-cli

# Install cron and required dependencies
RUN apt-get update && apt-get install -y \
    cron \
    libzip-dev \
    zip \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    --no-install-recommends \
    && docker-php-ext-install \
        zip \
        pdo_mysql \
        intl \
        mbstring \
        xml \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure cron
COPY crontab /etc/cron.d/yii-cron
RUN chmod 0644 /etc/cron.d/yii-cron && \
    crontab /etc/cron.d/yii-cron

# Create log directory
RUN mkdir -p /var/log/cron && \
    touch /var/log/cron/cron.log && \
    chmod 777 /var/log/cron/cron.log

# Keep container running
CMD ["cron", "-f", "-L", "2"]
