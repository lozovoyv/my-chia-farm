# My Chia Farm

MyChiaFarm is a web application created to give you control on your chia-blockchain node from everywhere over the
internet with nice GUI.

## PRERELEASE INFO

This is pre-release version and developed for Linux only for now. Support for Windows and Mac will be added in the
future. The primary focus was on plotting process managing, automating and optimization. And yes, second focus will be
on system resources monitoring. But, whole platform concept gives more flexibility, not only plotting. I have some ideas
which way to develop this project (see below) and, of course, community demands and wishes would be realized to the best
of my ability.

## Features

- Event driven plotting manager for multiple configurable jobs
- Nice GUI
- Accessible via internet from anywhere (with configured apache/nginx webserver)
- Built-in webserver for local running

## Installation

Install `php 8.0` and `ext-pdo`

Install composer: [getcomposer.org/download/](https://getcomposer.org/download/)

Install `git`

```shell
git clone https://github.com/lozovoyv/my-chia-farm

cd my-chia-farm

composer install

php artisan init

## and follow onscreen instructions.
```

Also, you can set up apache/nginx web server pointing to `my-chia-farm/public` and add LetsEncrypt certificate to secure
your connection.

## First run

After you installed and launched My Chia Farm go to `settings` page and set `Chia installation path`.

Next go to `jobs` page and configure your desired settings, events and start points. Each worker doing the job will fire
events configured for this one to entry system. And all jobs will listen to those events and start desired number of
workers.

And finally open `dashboard` and click play button on job you want to run.

I hope it seems intuitive. Later I will create wiki with full description.

## Bugs and issues

- There is no user input validation yet on jobs configurations. Please, check yourself.
- There is no plotting errors handling yet (temporary drive overflow, e.t.c.)

If you found some bugs or issues please [report issue](https://github.com/lozovoyv/my-chia-farm/issues)

## Sponsor/support this project

Developing this project takes a lots of time. I will be glad to get your help in developing and keeping this project up
to date. Just push PR or buy me some time I could spend on this project =).

- XCH: xch10v9f4ev5uesp4nlukgydxxk8fhkszwn8kzwr2cjwljwd47f06wqslrmlgx
- BTC: bc1q6ax4058we962dkqs0vpyvh3y2vp9vkpymyl2t6
- ETH: 0x35f7938c9F1C17305401048c68D0221966f47eCA

## Lots of work planned ahead:

### general:

- Adding support for multiply keys registered.
- Localizations support
- Notification: sending notifications/logs/errors through mail/telegram/e.t.c.
- Backup: export and import for (selectable) settings, users, jobs and other configurations to/from single file

### user interface:

- Toasts to handle errors/successes messages
- Application custom name to identify one in case of many nodes.
- User input validation
- Optimize UI on user feedback

### plotting:

- Add support for alternate plotting mods
- Monitoring system resources in real-time (CPU, RAM, disks)
- Job events throttling
- Executing custom command after job finishes (with %plot_filename% parameter)
- Plot checking after plot is done
- Replacing the earliest plot in destination (replotting) up to specified date
- Wallet for storing keys and using it with mnemonic name (for plotting)
- Simulation of seeding on captured statistics to build resources usage map for configured jobs

### farming:

- Chia log monitor with notification sending
- Plot checking for target folder(s)

### wallet:

- Wallet info and monitoring with notifications of balance changes
- Address book
- Making transactions

### OS support

- Adaptation for Mac
- Adaptation for Win

### and much more with your support...
