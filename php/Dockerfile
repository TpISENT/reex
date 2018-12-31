FROM devwithlando/php:7.1-apache

RUN apt-get update -y \
  && docker-php-ext-install pcntl
RUN curl -sL https://deb.nodesource.com/setup_9.x | bash -
RUN apt-get install -y nodejs gcc g++ build-essential xdg-utils libnspr4 libnss3 libxss1 libx11-xcb1 libgtk-3-0 libatk-bridge2.0-0 libappindicator1 libindicator7 fonts-liberation libappindicator3-1 libasound2 
RUN wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN dpkg -i google-chrome*.deb
RUN npm config set strict-ssl false -g
RUN npm config set unsafe-perm true -g
RUN npm install gulp-cli gulp -g
RUN chown -R www-data /usr/lib/node_modules
RUN ln -s /app/vendor/acquia/blt/bin/blt /usr/bin/blt
RUN composer global require consolidation/cgr
