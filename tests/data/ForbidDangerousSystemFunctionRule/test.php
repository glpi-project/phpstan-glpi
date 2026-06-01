<?php

declare(strict_types=1);

// Forbidden functions
exec('ls');
shell_exec('ls');
system('ls');
passthru('ls');
popen('ls', 'r');
proc_open('ls', [], $pipes);
pcntl_fork();
posix_kill(1, 9);
dl('extension.so');
link('target', 'link');
symlink('target', 'link');

// Allowed functions (whitelisted)
posix_geteuid();
pcntl_async_signals(true);
pcntl_signal(SIGTERM, function() {});
pcntl_signal_get_handler(SIGTERM);
pcntl_signal_dispatch();
