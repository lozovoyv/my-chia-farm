# Plotting manager

![](imgs/d02.jpg?raw=true)

## Features

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
- Automatically cleans up temporary folders to prevent garbage collecting.

## Concept

The core concept of plotting manager is a jobs, workers and events.

The job is a set of arguments and parameters describing how to make a plot file. Worker is a process making single plot
file. In other words, worker is a wrapper for plotter application and job is a set of arguments for it. Each worker
during its lifecycle emits events configured for job to all existing jobs, so they could start some number of their
workers assigned to the emitted event.

## Worker lifecycle and events:

- checking required arguments are filled
- checking pre-process command is set and enabled (next 3 steps are skipped otherwise)
- _*_ emitting `pre-process command started` event
- _*_ running pre-process command
- _*_ emitting `pre-process command finished` event
- emitting `plotter process started` event
- running plotter process (during plotting process `plotting stage` and `plotting progress` events are emitted on stage
  ang progress changes)
- emitting `plotter process finished` event
- checking post-process command is set and enabled (next 3 steps are skipped otherwise)
- _*_ emitting `post-process command started` event
- _*_ replacing %plot% tag with actual plot filename and running post-process command
- _*_ emitting `post-process command finished` event
- cleaning up temporary directories and logs for current worker (if `save logs` not set)
- emitting `worker end` event
- destroying worker

When worker finishes, it increases counter of done plots in job. If it reaches maximum number of plots to be
done `job end` event would be emitted.

## Limits of workers number

There are two options affecting the number of workers doing the same job:

- The maximum number of workers doing the same job at the same time. So, if there is limiting number (except 0 - it's
  for infinite), number of started workers would not exceed the limiting number.
- The plots to do number. If it is not 0, workers would not start if number of done plots and number of pending plots is
  equal to or exceeds the limiting number.

These limits do not affect manual starting of workers.

## Default settings

There are some options on settings page:

- General plotting defaults
- Defaults for each plotter program

General defaults settings are shared to all plotters and can be overridden by plotter defaults.

So when job has some arguments set to default, plotting manager looks if plotter has default values for these arguments,
and if there are no ones, looks for global defaults. If both of them are not set, missing required argument error will
be reported. Each new worker for this job will get defaults arguments not from job, but from those settings. It means if
you change some default values used by jobs, new workers would start with new arguments.

For example, you can set temporary or destination directory as global default for all plotting processes. Once you
changed it, all new processes would use new locations for plotting.

![](imgs/s01.jpg?raw=true)

The required parameter for each plotter is a command what executes a plot creation. Typically, for (linux versions)
original chia plotting it is `cd ~/chia-blockchain && . ./activate && chia plots create` and for MadMax plotter
is `~/chia-plotter/build/chia_plot`. Once you set it, it could be used as default for all jobs, or could be overridden
for some jobs by its own commands. So you can manage several versions of one plotter, for example.

![](imgs/s02.jpg?raw=true)

## Creating and configuring job

### Creating job

To create new job just open "jobs" page and click "add job".

![](imgs/j01.jpg?raw=true)

First you need to give a job some title, so you could identify it later for yourself, and select one of applicable
plotter programs.

IMPORTANT NOTICE: you need to have installed plotter you want to use.

![](imgs/j02.jpg?raw=true)

### Plotter arguments

The next step is configuring plotter arguments. You can ues default settings (see [default settings](#default-settings))

![](imgs/j04.jpg?raw=true)

or override some arguments for current job.

![](imgs/j04_1.jpg?raw=true)

### Advanced settings

There are three options.

![](imgs/j05.jpg?raw=true)

You can enable CPU affinity and select cores you want to pin to plotting process.

![](imgs/j06.jpg?raw=true)

Pre-process and post-process commands, if set, would be executed before plotting process started and after it finished.
They are regular system commands. For post-process command there is a `%plot%` tag. It will be automatically replaced
with actual plot filename at command execution time, so you can do some automation like `mv %plot% /to_network/` or even
run `rsync` or something else.

Commands output will be logged to `my-chia-farm/logs/` directory. Worker ID with `_pre` or `_post` suffixes will be used
as name of log file.

![](imgs/j07.jpg?raw=true)

### Replotting option

With replotting option enabled plotting manager will search the oldest plot file in destination directory and remove it
before plotting process start if its timestamp is not greater when entered limit of date and time.

![](imgs/j08.jpg?raw=true)

### Events

Events is a heart of concept of plotting manager. Using it you can build any workflows of plotting process you want.

There are no limits in event number for job. You can configure several events to be emitted at different stages.

![](imgs/j09.jpg?raw=true)

Event emitting can be disabled for job. It means worker doing this job would not emit any events. But they still can be
started on events from other jobs.

To add event click "add event" and configure it. All events have its mnemonic name (any characters you want). But the
name is not required to be unique. Several events could have the same names, so worker(s) assigned to this event name
would be started on each one.

![](imgs/j10.jpg?raw=true)

Second option of event is a moment when it would be emitted.

There are several logical scopes of event emitting moment:

- worker stages
- plotting process stages or progress
- job end

For worker stages and job end see [worker lifecycle](#worker-lifecycle-and-events).

![](imgs/j11.jpg?raw=true)

Plotting progress is a number of percents of plotting process done. It has more informative meaning and is not accurate,
can vary for different hardware, OS and other factors.

![](imgs/j15.jpg?raw=true)

Plotting stage is current plotter task (phase or phase and table):

![](imgs/j12.jpg?raw=true)

Each stage has beginning and ending condition.

![](imgs/j13.jpg?raw=true)

And finally delay can be added to any event.

![](imgs/j14.jpg?raw=true)

*For example above event with name "next" would be fired 30 seconds later after beginning of 2nd phase of plotting.*

### Workers settings

![](imgs/j16.jpg?raw=true)

Start of workers can be disabled as events emitting. In case of workers start disabled, only manual start will be able.

Also, you can limit number of workers for current job (see [limits of workers number](#limits-of-workers-number))

To add worker start point click "add worker start" and type number of workers should be started and name of event as
starting moment.

![](imgs/j17.jpg?raw=true)

![](imgs/j18.jpg?raw=true)

Logs of worker can be saved. Just check "save logs of workers" and specify how many logs you want to save (0 for all).
In this case all worker logs (logs of pre- and post-process commands and plotting process) would not be cleaned at
plotting process end. All logs are stored in `my-chia-farm/logs/` directory.

![](imgs/j19.jpg?raw=true)

## Running

After you configured your desired job(s), it(they) must be launched. Go to "dashboard" page and click play button on job
you wish to start.

![](imgs/d01.jpg?raw=true)

Congratulations! Now workers doing their jobs, emitting events to start new workers and so on.

![](imgs/d02.jpg?raw=true)

