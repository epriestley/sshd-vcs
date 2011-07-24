#!/usr/bin/env php
<?php

$COMMAND = 'serve.php';

$cert = file_get_contents('php://stdin');
$user = null;
if ($cert) {
  $user = lookup_cert_in_database($cert);
}

if (!$user) {
  // Couldn't find a user for this cert. Exit nonzero.
  exit(1);
}

$options = array(
  'command="'.$COMMAND.' '.$user.'"',
  'no-port-forwarding',
  'no-X11-forwarding',
  'no-agent-forwarding',
  'no-pty',
);

// Echo options and exit zero.
echo implode(',', $options);
exit(0);


function lookup_cert_in_database($cert) {
  // Go lookup the cert in a database or whatever, and figure out which user
  // it belongs to. Return that user's name or some other identifier, or null
  // if the cert is invalid.
  $user = 'example';

  return $user;
}
