# My Chia Farm

MyChiaFarm is a web application created to give you control on your chia-blockchain farm from everywhere over the
internet with nice GUI.

## RELEASE INFO

This is beta version and developed for Linux only for now. Support for Windows and Mac will be added in the future. The
primary focus was on plotting process managing, automating and optimization. And yes, second focus will be on system
resources monitoring. But, whole platform concept gives more flexibility, not only plotting. I have some ideas which way
to develop this project (see below) and, of course, community demands and wishes would be realized to the best of my
ability.

## Features

- Accessible via internet from anywhere (with configured apache/nginx webserver)
- Built-in webserver for local running
- Nice GUI

### Plotting manager

- Flexible event driven plotting manager for multiple configurable jobs. Next seeding could be started on any stage of
  plotting process.
- Each plotting job could be limited by total number of plots to be done and number of workers doing this job.
- Supports CPU affinity setting
- Supporting both original chia plotter and MadMax plotter and ready to be adapted to any other plotters and changing of
  existent plotters
- User input validation and checking for required parameters
- Executing custom command before plotting process started
- Executing custom command after plotting finished (%plot% tag would be replaced with absolute plot filename). For
  example, you can make command to upload plot file to network storage.
- Replotting. The one earliest plot (up to specified date) in destination folder could be removed when seeding process
  begins.

## Installation

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

## First run

After you installed and launched My Chia Farm go to `settings` page and set executables for plotters (typically, for
original chia it would be `cd ~/chia-blockchain && . ./activate && chia plots create` and `~/chia-plotter/build/chia_plot`
for MadMax).

Next go to `jobs` page and configure your desired settings, events and start points.

In short: each worker doing the job will fire events configured for this one to entry system and all jobs will listen to
those events and start desired number of workers.

And finally open `dashboard` and click play button on job you want to run.

I hope it seems intuitive. Later I will create wiki with full description.

## Bugs and issues

- There is no much input validation yet. Please, check yourself.
- There is no plotting errors handling yet (temporary drive overflow, e.t.c.). You could help me by providing log with
  errors to handle.

If you found some bugs or issues please [report issue](https://github.com/lozovoyv/my-chia-farm/issues)

## Sponsor/support this project

Developing this project takes a lots of time. I will be glad to get your help in developing and keeping this project up
to date. Just push PR or:

- XCH: xch12jrqd5ahvh7eeds2yafyxfwcjw9eu6asvesjd2h0fpkj6ugup89s7ar9xa
- BTC: bc1q6ax4058we962dkqs0vpyvh3y2vp9vkpymyl2t6
- ETH: 0x35f7938c9F1C17305401048c68D0221966f47eCA
- PayPal: [paypal.me/lozovoyv](https://paypal.me/lozovoyv)
- RUB: [YooMoney](https://sobe.ru/na/my_chia_farm)
- XFX: xfx10v9f4ev5uesp4nlukgydxxk8fhkszwn8kzwr2cjwljwd47f06wqsxqumty

## Lots of work planned ahead:

### general:

- Adding support for multiply keys registered.
- Localizations support
- Notification: sending notifications/logs/errors through mail/telegram/e.t.c.
- Backup: export and import for (selectable) settings, users, jobs and other configurations to/from single file

### user interface:

- Application custom name to identify one in case of many nodes.
- Optimize UI on user feedback

### plotting:

- Monitoring system resources in real-time (CPU, RAM, disks)
- Job events throttling
- Plot checking after plot is done
- Wallet for storing keys and using it with mnemonic name (for plotting)
- Simulation of seeding on captured statistics to build resources usage map for configured jobs in seconds

### farming:

- Chia log monitor with notification sending
- Plot checking for target folder(s)

### wallet:

- Wallet info and monitoring with notifications of balance changes
- Address book
- Making transactions (?)

### OS support

- Adaptation for Mac
- Adaptation for Win

### and much more with your support...
