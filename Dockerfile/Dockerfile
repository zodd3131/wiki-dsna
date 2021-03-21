FROM php:7.2-apache
LABEL maintainer="LFFF"

ENV MEDIAWIKI_PATH /var/www/html/w

# System Dependencies.
RUN apt-get update && apt-get install -y \
	git \
	imagemagick \
	libicu-dev \
	# Required for SyntaxHighlighting
	python3 \
	libpng-dev \
	unzip \
	default-mysql-client \
	--no-install-recommends && rm -r /var/lib/apt/lists/* && rm -rf /var/cache/apt/archives/*

#RUN apt autoremove sensible-utils

# Install the PHP extensions we need
RUN docker-php-ext-install mbstring mysqli opcache intl gd zip

# Install the default object cache.
RUN pecl channel-update pecl.php.net \
	&& pecl install apcu \
	&& docker-php-ext-enable apcu

# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
RUN { \
	echo 'opcache.memory_consumption=128'; \
	echo 'opcache.interned_strings_buffer=8'; \
	echo 'opcache.max_accelerated_files=4000'; \
	echo 'opcache.revalidate_freq=60'; \
	echo 'opcache.fast_shutdown=1'; \
	echo 'opcache.enable_cli=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# SQLite Directory Setup
RUN mkdir -p /var/www/data \
	&& chown -R www-data:www-data /var/www/data

# Version
ENV MEDIAWIKI_MAJOR_VERSION 1.31
ENV MEDIAWIKI_BRANCH REL1_31
ENV MEDIAWIKI_VERSION 1.31.7
ENV MEDIAWIKI_SHA512 ee49649cc37d0a7d45a7c6d90c822c2a595df290be2b5bf085affbec3318768700a458a6e5b5b7e437651400b9641424429d6d304f870c22ec63fae86ffc5152

#COPY APACHE SITE 4 wiki
COPY ./wiki.conf /etc/apache2/sites-available/wiki.conf

# MediaWiki setup 
RUN mkdir -p ${MEDIAWIKI_PATH} && \
	curl -fSL "https://releases.wikimedia.org/mediawiki/${MEDIAWIKI_MAJOR_VERSION}/mediawiki-${MEDIAWIKI_VERSION}.tar.gz" -o mediawiki.tar.gz \
	&& tar -xz --strip-components=1 -f mediawiki.tar.gz -C ${MEDIAWIKI_PATH} \
	&& rm mediawiki.tar.gz

# Extensions installation
WORKDIR ${MEDIAWIKI_PATH}/extensions/
RUN rm -R  SyntaxHighlight_GeSHi
RUN for X in \
	ApprovedRevs CSS HeaderTabs LabeledSectionTransclusion \
	Loops Variables VisualEditor TitleKey Collection CategoryTree \
	Scribunto MultimediaViewer Arrays SyntaxHighlight_GeSHi ; \
	do \
	git clone --depth 1 -b $MEDIAWIKI_BRANCH \
	https://gerrit.wikimedia.org/r/mediawiki/extensions/$X.git ; \
	done
# SimpleMathJax
RUN git clone --depth 1 -b master \
	https://github.com/jmnote/SimpleMathJax.git
# PDFEmbed
RUN git clone --depth 1 -b master \
	https://gitlab.com/hydrawiki/extensions/PDFEmbed.git
# Init Submodule 4 VisualEditor

RUN cd ${MEDIAWIKI_PATH}/extensions/VisualEditor \
	&& git submodule update --init 

WORKDIR ${MEDIAWIKI_PATH}
COPY ./composer.local.json ./composer.local.json

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
	php composer-setup.php --version=1.10.20 --install-dir=/usr/local/bin --filename=composer && \
	php -r "unlink('composer-setup.php');"
RUN composer install --no-dev

COPY Lato-Regular.ttf ${MEDIAWIKI_PATH}/fonts/Lato/

RUN chown -R www-data: \
	${MEDIAWIKI_PATH}/extensions ${MEDIAWIKI_PATH}/skins \
	${MEDIAWIKI_PATH}/cache ${MEDIAWIKI_PATH}/images \
	${MEDIAWIKI_PATH}/fonts

# docker-php-entrypoint configures db, env variable and LocalSettings.php
COPY ./docker-php-entrypoint /usr/local/bin/docker-php-entrypoint

# additional parameters to LocalSettings.php
COPY ./LocalSettings.local.php ${MEDIAWIKI_PATH}/LocalSettings.local.php

# include for LocalSettings.php
COPY ./LocalSettings.ext.php ${MEDIAWIKI_PATH}/LocalSettings.ext.php

# apache configuration for mediawiki
COPY ./wiki.conf /etc/apache2/sites-available/wiki.conf

# Disable default site
RUN a2dissite 000-default

# Activate mod_rewrite
RUN a2enmod rewrite ssl

# Enable wiki
RUN a2ensite wiki
