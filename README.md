## Planned
* make a nicer UI
* add more help information
* add more advanced options
* add a statusbar below the navbar containing miner status, updated each second
* cleanup php code

## Proposals
* rename settings variables
* add page advanced.php
  * edit miner.conf
  * send any command to cgminer
  * backup configuration
* merge pools.php and settings.php
* merge about.php, contact.php and license.php
* save settings and data in sqlite
* (longterm) rewrite in Angular.js

## Changed

### Index

* Charts toggled with jquery
* Pools table
  * added class ellipsis to the URL
  * changed the space before the percentages to a non-breaking one: nbsp;
  * more th are shortened, maybe too much
  * the ellipsis class is updated to show the complete cell value on hover.

### Pools
* only url and user are required, pass will be set to "none" if empty
* if url or pass is not given, the row will be discarded
* rows under 3 empty rows are also discarded

### Settings
* help-text might be wrong
* added variables donateEnable & alertEnable 
* variables renamed, but still compatible


