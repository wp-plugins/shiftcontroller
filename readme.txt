=== ShiftController - Employee Shift Scheduling ===

Contributors: HitCode
Tags: staff scheduling, shift scheduling, employee scheduling, rota shift scheduling
License: GPLv2 or later

Stable tag: trunk
Requires at least: 3.3
Tested up to: 4.1

Schedule staff and shifts anywhere at anytime online from your WordPress powered website.

== Description ==

ShiftController is a staff scheduling plugin for any business that needs to manage and schedule staff. 
It provides the ability for the administrators to assign staff members to the shifts.
ShiftController allows to manage timeoffs and holidays so you can assign only those people who are available for work. 
It helps overcome schedule conflicts as you can see and correct any conflicts due to overlapping shifts or timeoffs. 

The monthly shifts are listed by position (location) or by staff member, the plugin automatically calculates the working time and the number of shifts. The plugin automatically emails the schedule to every staff member and lets them know when they work.

###Pro Version Features###

* __Recurring Shifts__ to quickly schedule shifts weeks ahead
* __Shift Trade__ to let your staff exchange their shifts
* __Notes for shifts and timeoffs__ to keep track of what is going on

Please visit [our website](http://www.shiftcontroller.com "WordPress Employee Scheduling") for more info and [get the Premium version now!](http://www.shiftcontroller.com/order/).

ShiftController users database is automatically synchronized with WordPress - you can define which WordPress user roles will be administrators and staff members in ShiftController. 

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

= 2.4.1 =
* A small fix in code that might break redirects with WP high error reporting level

= 2.4.0 =
* A fix for multiple staff ids in the shortcode param.

= 2.3.9 =
* A slight optimization on login/logout internal process.

= 2.3.7 =
* BUG: On plugin complete uninstall might delete all WordPress tables.

= 2.3.6 =
* BUG: (Pro Versions) multiple shifts could be deleted when deleting a single shift created as non recurring from shift edit form in the Delete tab. 

= 2.3.5 =
* Configuration option to set min and max values for time selection dropdowns, that will speed up time selection.
* Drop our database tables on plugin uninstall (delete) from WordPress admin. Also release the license code for the Pro version so it can be reused in another installation.
* Backend appearance restyled for a closer match to the latest WordPress version.
* Cleaned and optimized some files thus reducing the package size.

= 2.3.4 =
* Shift pickup links didn't work for staff members on the everyone schedule page (shortcode page).

= 2.3.3 =
* JavaScript error when staff picking up free shifts from everyone schedule page (shortcode page).

= 2.3.2 =
* A fix in session handling function that lead to an error on first user access of the system.

= 2.3.1 =
* Archived staff members are now not showing in the stats display if they have no shifts during the requested period.
* In the shortcode if you need to filter more than one location or employee, now you can supply a comma separated list of ids, for example [shiftcontroller staff="1,8"].
* Also if you do not want to show the list of locations in the shortcode page, you can supply the location parameter as 0 so it will list shifts for all locations [shiftcontroller location="0"]

= 2.3.0 =
* Added more options for shortcode to filter by location or by staff, as well as specify the start and end date and how many days to show.
* Extended options for the shift notes premium module, now one can define who can see the shift note - everyone, staff members, this shift staff or admin only.

= 2.2.9 =
* If more than one locations are available in the "Everyone Schedule" then it first asks to choose a location first.
* Added the description field for locations. If specified, it will be given in the "Everyone Schedule" and "Shift Pick Up" parts if more than one location available.
* Redesigned the "Everyone Schedule" (wall) page view so that lists all upcoming shifts in a simple list. It is supposed to eliminate all the compatibility issues for the shortcode page display as the calendar output would look cumbersome under certain themes.

= 2.2.8 =
* If there are open shifts in more than one location, an employee is asked to choose a location first, then the available shifts in this location ara displayed.
* Minor fixes and code updates

= 2.2.7 =
* Added an option to supply parameters to the shortcode to define the range (week or month) and the starting date, please check out the Configuration > Shortcode page
* Minor fixes and code updates

= 2.2.6 =
* Minor fixes and code updates

= 2.2.5 =
* BUG: In the schedule list view, if you choose filtering by location, the shifts for all locations were still displayed as if there were no filter applied. 
* BUG: When creating a new shift, if you selected one or several employees to assign right now, but there was a validation error (no location selected, or the start and end times were incorrect), it showed a database error. 

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

 
