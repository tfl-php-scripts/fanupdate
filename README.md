# FanUpdate Enhanced (v 2.3) for PHP 7

Original author is [Jenny](http://prism-perfect.net) / Original readme is [here](fanupdate/docs/readme.txt).

#### I would highly recommend not to use this script for new installations. Although some modifications were made, this script is still pretty old, not very secure, and does not have any tests, that's why please only update it if you have already installed it before.

This version requires at least PHP 7.2.

## Upgrading instructions

I'm not providing support for those who have version lower than FanUpdate 2.2.1.

If you are using FanUpdate 2.2.1 (old version by Jenny) or 2.3.* (my version):

1. **Back up all your current FanUpdate configurations, files, and databases first.**
2. Take note of your database information in all your `fanupdate/blog-config.php` files.
3. Download [an archive of the FanUpdate folder in this repository](https://gitlab.com/tfl-php-scripts/fanupdate/-/archive/master/fanupdate-master.zip?path=fanupdate). Extract the archive.
4. Replace your current `fanupdate/` files with the `fanupdate/` files from this repository.

Please follow the instructions carefully. A lot of issues were related to facts that users had incorrect config files.

That's it! Should you encounter any problems, please create an issue here, and I will try and solve it as soon as possible.
