ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli-trixie

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    apt-get update -y \
    && apt-get install -y --no-install-recommends \
        git \
        libzip-dev \
        unzip \
    && docker-php-ext-install -j"$(nproc)" zip

# PIE (PHP extension installer) as a phar from the GitHub release, not the
# ghcr.io image: Docker sends stale ghcr.io credentials of an old
# `docker login` and the pull fails with "denied". To update, change the
# version and set the checksum to `sha256sum pie.phar` of that release.
ADD --chmod=755 --checksum=sha256:81041b276f4e23dfdfa6dc941e5745f9adec824a1bcf6141e226181ef43f519e \
    https://github.com/php/pie/releases/download/1.6.0/pie.phar /usr/local/bin/pie
RUN pie install xdebug/xdebug

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY conf.d/ /usr/local/etc/php/conf.d/

# Run as the host user so files created in the container (vendor/,
# composer.lock, caches) belong to you. The composer cache directory exists
# up front so the named volume mounted there is writable.
RUN groupadd --gid "$GROUP_ID" dev \
    && useradd --uid "$USER_ID" --gid "$GROUP_ID" --create-home dev \
    && mkdir -p /home/dev/.cache/composer \
    && chown -R dev:dev /home/dev/.cache

USER dev
WORKDIR /app
