# My Chia Farm

MyChiaFarm is a web application created to give you control on your chia-blockchain farm from everywhere over the
internet with nice GUI.

## Features

- Accessible via internet from anywhere (with configured apache/nginx webserver)
- Built-in webserver for local running
- Nice GUI

## *RELEASE INFO*

*This is beta version and developed for Linux only for now. Support for Windows and Mac will be added in the future. The
primary focus was on plotting process managing, automating and optimization. The whole platform concept gives more
flexibility, not only plotting. I have some ideas which way to develop this project and, of course, community demands
and wishes would be realized to the best of my ability.*

## Documentations and descriptions:

- [installation](installing/README.md)
- [plotting manager](plotting/README.md)

## Lots of work planned ahead / TODOS:

general:

- Adding support for multiply keys registered.
- Localizations support
- Notification: sending notifications/logs/errors through mail/telegram/e.t.c.
- Backup: export and import for (selectable) settings, users, jobs and other configurations to/from single file

user interface:

- Application custom name to identify one in case of many nodes.
- Optimize UI on user feedback

plotting:

- Destination enhancements
- Monitoring system resources in real-time (CPU, RAM, disks)
- Job events throttling
- Plot checking after plot is done
- Wallet for storing keys and using it with mnemonic name (for plotting)
- Simulation of seeding on captured statistics to build resources usage map for configured jobs in seconds

farming:

- Chia log monitor with notification sending
- Plot checking for target folder(s)

wallet:

- Wallet info and monitoring with notifications of balance changes
- Address book
- Making transactions (?)

OS support

- Adaptation for Mac
- Adaptation for Win

and much more with your support...
