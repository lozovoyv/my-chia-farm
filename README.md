# My Chia Farm

MyChiaFarm is a web application created to give you control on your chia-blockchain node from everywhere over the
internet with nice GUI.

## Features

- Event driven plotting manager for multiple configurable jobs.

## Installation

## Bugs and issues

- There is no user input validation yet on jobs configurations. Please, check yourself.
- There is no plotting errors handling yet (temporary drive overflow, e.t.c.)

If you found some bugs or issues please [report issue](https://github.com/lozovoyv/my-chia-farm/issues)

## Sponsor/support this project

Developing this project takes a lots of time. I will be glad to get your help in developing and keeping this project up
to date by pushing PR or buying some time I could spend on this project =).

XCH: xch10v9f4ev5uesp4nlukgydxxk8fhkszwn8kzwr2cjwljwd47f06wqslrmlgx
BTC: bc1q6ax4058we962dkqs0vpyvh3y2vp9vkpymyl2t6
ETH: 0x35f7938c9F1C17305401048c68D0221966f47eCA

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
