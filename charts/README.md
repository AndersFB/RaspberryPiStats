###Contents###

* config.php - Where you can configure all the settings (folder path, verbosity and future options)
* measuretemp.php - php script for cron job.
* cleanDB.php - script to clean the DB from sampling data and forbid future accidental deletions using a lock file.
* db.lck - lock file used by cleanDB.php to flag the clean operation as done and forbid future database cleanups. If you do want to reset the database, then delete db.lck first.

**To set it up:**

1. Open config.php and update `$config["root_dir"]` the with directory you put it in. For example, if you put it into /var/www/: `$config["root_dir"]="/var/www/"`
2. install vnstat: `$ sudo apt-get install vnstat`
3. Set up cron job
4. Initialize database with `php cleanDB.php`

VNSTAT is additional app which collects traffic information and give an output with the command `$ vnstat --dumpdb`.

Add the cron job by `$ sudo crontab -e` and add a line at the end of the file: 
`*/5 * * * * php /path/measuretemp.php` - /path/ is the place where you've
unziped the package! This will run measuretemp.php every 5 minutes. If you want
it to happen less often, change 5 to a higher number of minutes (*/10 for every
10 minutes, */35 for every 35 minutes and so on).

The database can be cleaned using cleanDB.php, which will also produce a lock
file to avoid future  accidental deletions. Delete the lock file db.lck first if you
really want to clean the database.

###Authors and Contributors###
First version by: @yuraa (<a href="http://yuraa.github.io/Raspberry-Pi-Heartbeat/" target="_blank">Raspberry Pi Heartbeat</a>)
