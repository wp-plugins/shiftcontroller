=== ShiftController Staff Scheduler - Employee Shift Scheduling ===

Contributors: HitCode
Tags: staff scheduling, shift scheduling, employee scheduling, rota shift scheduling
License: GPLv2 or later

Stable tag: 2.2.4
Requires at least: 3.3
Tested up to: 3.7

== Description ==

ShiftController is a staff scheduling plugin for any business that needs to manage and schedule staff. 
It provides the ability for the administrators to assign staff members to the shifts.
ShiftController allows to manage timeoffs and holidays so you can assign only those people who are available for work. 
It helps overcome schedule conflicts as you can see and correct any conflicts due to overlapping shifts or timeoffs. 
The monthly shifts are listed by position (location) or by staff member, the plugin automatically calculates the working time and the number of shifts.
The plugin automatically emails the schedule to every staff member and lets them know when they work.
ShiftController users database is automatically synchronized with WordPress - you can define which WordPress user roles will be administrators and staff members in ShiftController. 
Please visit [our website](http://www.shiftcontroller.com "WordPress Employee Scheduling") for more info, or try our online staff scheduling [demo](http://www.shiftcontroller.com/demo/ "ShiftController Premium Demo") with all features.

== Support ==
Please contact us at http://www.shiftcontroller.com/contact/

Author: HitCode
Author URI: http://www.shiftcontroller.com

== Installation ==

1. After unzipping, upload everything in the `shiftcontroller` folder to your `/wp-content/plugins/` directory (preserving directory structure).

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. When you first open ShiftController in your WordPress admin panel, it will ask which existing WordPress user accounts would you like to import into ShiftController..

== Screenshots ==
1. Month Schedule Overview

== Upgrade Notice ==
The upgrade is simply - upload everything up again to your `/wp-content/plugins/` directory, then go to the ShiftController menu item in the admin panel. It will automatically start the upgrade process if any needed.

== Changelog ==

= 2.2.4 =
* Fixed an issue with shortcode that might be moving into infinite loop for admin and staff users
* An option to color code shifts in the calendar according to the employee
* An option to hide the shift end time for the employees 
* An option to disable shift email notifications
* Minor fixes and code updates

= 2.2.3 =
* Reworked the calendar view controls - now the list and stats display can also be filtered by location and by employee. 
* Fix with the timezone assignment
* Locations are sorted properly in the form dropdown
* Wrong employee name when a time off was requested by an employee
* when synchronizing users from WordPress you can append the original WP role name to the staff name

= 2.2.2 =
* Configure which user levels can take shifts
* Assign employees to shifts from the calendar view
* Fixed a problem with irrelevant email notifications
* Select multiple staff members or define the required number of employees when creating a shift

= 2.2.1 =
* Fixed problem when shortcode was not working properly

= 2.2.0 =
* Shift history module
* More convenient schedule views (show calendar by location and by staff member, week or month view)
* Updated view framework (Bootstrap 3)
* Minor code optimizations and bug fixes

= 2.1.1 =
* Login log module
* BUG: Select All in Timoffs and Shift Trades admin views were not working
* BUG: Repeating options were not active in the Premium version
* Minor code optimizations and bug fixes

= 2.1.0 =
* Fixed bug when email notification was not sent after publishing just one shift
* Remove location label if just one location is configured
* Shift notes view in the calendar
* Archived users do not appear in the dropdown list when creating or editing shifts


= 2.0.6 =
* Shifts month calendar

= 2.0.5 =
* Shifts list in a table view and CSV/Excel export

= 2.0.4 =
* Custom weekdays for recurring shifts

= 2.0.3 =
* Display shifts grouped by locations

= 2.0.2 =
* Public employee schedule calendar and minor bug fixes

= 2.0.1 =
* Bug fix: error when creating a new user in the free version.

= 2.0.0 =
* Completely reworked calendar view and the premium version.

= 1.0.2 =
* Bug fixes: time display, forgotten password and password change, email notification on a new timeoff.

= 1.0.1 =
* Bug fixes after not complete form in setup and error after timeoff delete.

= 1.0.0 =
* Initial release



Thank You.

 
