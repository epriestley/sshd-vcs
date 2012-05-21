Patches to OpenSSH to allow it to authorize access to version control systems
(like SVN and Git) in a flexible way. This allows you to run a daemon which
gives users access to application resources without real system accounts,
similar to how GitHub allows access to Git repositories over SSH without
creating real system accounts for each user (instead, users upload public SSH
keys).

If you're just using Git and don't have all that many user accounts, Gitosis
is probably a much better solution to this problem (you could also try Gitolite
or Gitorious). sshd-vcs is a much more raw solution; while it is more powerful,
it is far more difficult to set up.

In more detail, the problem this solves is:

  - "Application" is whatever you're building, like GitHub. "Application
    Users" are people who use your application, like GitHub users.
  - "System" is the underlying Lunix system. "System Users" are real user
    accounts that have UIDs.
  - You have an arbitrary number of application users (like GitHub users)
    who need to access some application resource (like Git repositories).
    You don't want to create system accounts for each user. sshd-vcs allows
    all the application users to act as a single system user, while using
    application credentials (instead of system credentials) to authenticate
    them.
  - You either want a more flexible configuration than Gitosis provides,
    or want to support something other than git. Gitosis is a much simpler
    solution to this problem if it is suitable for your needs (it should
    be suitable in nearly all cases).

Specifically, there are two new config parameters available:

  - AuthorizedKeysScript: Public keys can now be loaded from an external
    source instead of ~/.ssh/authorized_keys.
  - ForceUser: Ignore the login user and always use a specific account.

Basically this allows you to set up sshd something like this:

  - Write a script which accepts an SSH public key on stdin, and produces
    either a nonzero exit code to deny login, or a zero exit code with
    an optional option string on stdout to accept login. Suppose this script
    is called "auth.sh".
  - Write a script which accepts a user as argv[1], a command from
    environmental variable SSH_ORIGINAL_COMMAND, and uses stdin/stdout to
    communicate with the remote client. Suppose this script is called
    "serve.sh". You can adapt something like Gitosis to accomplish this, if
    your backing service is git but you're choosing sshd-vcs for additional
    flexibility.
  - Have "auth.sh" emit something like 'command="serve.sh <user>",
    no-port-forwarding,no-X11-forwarding,no-agent-forwarding,no-pty' when it
    finds a valid public key.
  - Set AuthorizedKeysScript to "auth.sh".
  - Create a real system user account like "git" which all users will act as
    when logged into the system.
  - Set ForceUser to "git".
  - Have application users put their public SSH keys into a database.
  - Now, when users login, sshd invokes "auth.sh" and sends it the public key
    on stdin. "auth.sh" checks the database and either sees that the key is
    not present (in which case it exits nonzero), or sees that the key is
    valid and identifies an application user, in which case it emits
    'command="serve.sh <appuser>",...' to stdout and exits 0. sshd now invokes
    the command, and puts the user's original command in the environmental
    variable SSH_ORIGINAL_COMMAND. "serve.sh" runs with the valid user
    in argv[1]. It can now execute Gitosis, or wrap raw git commands, or
    whatever else.

You should run sshd-vcs on port 22 and run a real copy of sshd on some other
port if you need shell access to the box. It is strongly recommended you do not
try to run a copy of sshd-vcs as your real sshd. Instead, run sshd-vcs with
every possible access setting locked down and real sshd elsewhere.

For some very basic examples, see example/.

Building
    ./configure --prefix=/opt/sshd-vcs
    make

Installing
    sudo make install

Once off setup
    sudo mkdir /var/empty
    sudo adduser --system --no-create-home --home /var/run/sshd --shell /usr/sbin/nologin sshd
    sudo apt-get install -y php5
    sudo mkdir /opt/sshd-vcs/etc
    sudo ssh-keygen -t rsa -f /opt/sshd-vcs/etc/ssh_host_rsa_key
    sudo ssh-keygen -t dsa -f /opt/sshd-vcs/etc/ssh_host_dsa_key
    Setup paths to auth script in ./example/sshd_config and path to serve.php in auth.php

Running
    sudo /opt/sshd-vcs/sbin/sshd-vcs -f ./example/sshd_config

Testing
    ssh git@127.0.0.1

sshd-vcs is based extensively on prior work:

  https://github.com/wuputahllc/openssh-for-git
  http://www.openssh.com/
