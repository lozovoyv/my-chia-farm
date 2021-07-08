# Installation

Install `php 8.0` and php extensions: `ext-curl`, `ext-mbstring`, `ext-dom`, `ext-sqlite3`. I believe you can find in Google how to do it.

Install composer: [getcomposer.org/download/](https://getcomposer.org/download/)

Install `git` if it is not installed.

then go to directory you want to install My Chia Farm and run following commands:

```shell
git clone https://github.com/lozovoyv/my-chia-farm

cd my-chia-farm

composer update

php artisan init

## and follow onscreen instructions.
```

Also, you can set up apache/nginx web server pointing to `my-chia-farm/public` and add LetsEncrypt certificate to secure
your connection.

Once you set up application and added its starter to cron no matter running you web server or not (local or apache/nginx) application would be working on cron events. Web server is needed to handle web-interface.

## First run

After you installed and launched My Chia Farm go to `settings` page and set executables for plotters (typically, for
original chia it would be `cd ~/chia-blockchain && . ./activate && chia plots create` and `~/chia-plotter/build/chia_plot`
for MadMax).

Next go to `jobs` page and configure your desired settings, events and start points.

In short: each worker doing the job will fire events configured for this one to entry system and all jobs will listen to
those events and start desired number of workers.

And finally open `dashboard` and click play button on job you want to run.

I hope it seems intuitive. Later I will create wiki with full description.
