#!/usr/bin/env php
<?php

if ($argc < 2) {
  echo "Not authenticated.\r\n";
  exit(1);
}

$user = $argv[1];
$command = getenv('SSH_ORIGINAL_COMMAND');

if (empty($command)) {
  // This means they just typed "ssh user@host", instead of
  // "ssh user@host command". With SVN or Git over SSH, there will be a
  // specified command.
  echo "Authenticated as {$user}. No interactive logins.\r\n";
  exit(1);
}

echo "You tried to run '{$command}', but this is an example serve script.\r\n";
