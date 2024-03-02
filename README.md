# Cài git trên CentOs7

sudo yum makecache
yum -y install epel-release
sudo yum -y install git
git --version

# Cài đặt docker CentOs7

yum -y update
yum -y install yum-utils device-mapper-persistent-data lvm2
yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
yum -y install docker-ce
systemctl start docker
systemctl enable docker

# Cài đặt Docker Compose

curl -L "https://github.com/docker/compose/releases/download/1.25.5/docker-compose-$(uname -s)-$(uname -m)" -o
/usr/local/bin/docker-compose

chmod +x /usr/local/bin/docker-compose

ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose

`docker-compose --version`

# Sau khi clone code về thì chạy các câu lệnh dưới đây

# `docker-compose up -d --build`

## `docker-compose run --rm composer install`

## `docker-compose run --rm artisan migrate`

<!-- ## `composer config --global process-timeout 2000` -->

## `docker-compose run --rm artisan db:seed`

# Compser install mà lỗi 255 thì chạy lệnh

## `docker-compose run --rm artisan package:discover --ansi`

# Nếu báo không tìm thấy class thì chạy lệnh

## `docker-compose run --rm composer dumpautoload`

# Webpack Mix

## `docker-compose run --rm --service-ports npm run watch`

## Lưu data MySQL Storage

### Tạo folder mysql để khi composer-down không bị mất data

1. Create a `mysql` folder in the project root, alongside the `nginx` and `src` folders.
2. Under the mysql service in your `docker-compose.yml` file, add the following lines:

```
volumes:
  - ./mysql:/var/lib/mysql
```

## Using BrowserSync with Laravel Mix

If you want to enable the hot-reloading that comes with Laravel Mix's BrowserSync option, you'll have to follow a few
small steps. First, ensure that you're using the updated `docker-compose.yml` with the `:3000` and `:3001` ports open on
the npm service. Then, add the following to the end of your Laravel project's `webpack.mix.js` file:

```javascript
browserSync({
    proxy: 'site',
    open: false,
    port: 3000,
});
```

From your terminal window at the project root, run the following command to start watching for changes with the npm
container and its mapped ports:

```bash
docker-compose run --rm --service-ports npm run watch
```

That should keep a small info pane open in your terminal (which you can exit with Ctrl + C).
Visiting [localhost:3000](http://localhost:3000) in your browser should then load up your Laravel application with
BrowserSync enabled and hot-reloading active.

# Dùng để BUILD image theo profile

`docker-compose --profile prod up -d --build` sau profile là tên profile tìm trong docker-compose để build        # report-php
