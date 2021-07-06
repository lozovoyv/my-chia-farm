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
file. Each worker during its lifecycle emits events configured for job to all existing jobs, so they could start some
number of their workers assigned to the emitted event. In other words, worker is a wrapper for plotter application and
job is a set of arguments for it.

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

## Default settings

There are some options on settings page:

- General plotting defaults
- Defaults for each plotter program

General defaults settings are shared to all plotters and can be overridden by plotter defaults.

So when job has some arguments set to default, plotting manager looks if plotter has default values for these arguments,
and if there are no ones, logs for global defaults. If both of them are not set, missing required argument error will be
reported. Each new worker for this job will get defaults arguments not from job, but from those settings. It means if
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
with actual plot filename when command would be executed, so you can do some automation like `mv %plot% /to_network/` or
even run `rsync` or something else.

Commands output will be logged to `my-chia-farm/logs` directory. Worker ID with `_pre` or `_post` suffixes will be used
as name of log file.

![](imgs/j07.jpg?raw=true)

### Replotting option

With replotting option enabled plotting manager would search the oldest plot file in destination directory and remove it
before plotting process start if its timestamp is not greater when entered limit of date and time.

![](imgs/j08.jpg?raw=true)

### Events



![](imgs/j09.jpg?raw=true)
![](imgs/j10.jpg?raw=true)
![](imgs/j11.jpg?raw=true)
![](imgs/j12.jpg?raw=true)
![](imgs/j13.jpg?raw=true)
![](imgs/j14.jpg?raw=true)
![](imgs/j15.jpg?raw=true)

### Workers settings

![](imgs/j16.jpg?raw=true)
![](imgs/j17.jpg?raw=true)
![](imgs/j18.jpg?raw=true)
![](imgs/j19.jpg?raw=true)

## Running 
