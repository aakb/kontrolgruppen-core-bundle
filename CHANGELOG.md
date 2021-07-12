# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- [KON-428](https://jira.itkdev.dk/browse/KON-428): Company or person as client on process
- [KON-428](https://jira.itkdev.dk/browse/KON-428): Disabled revenue on non-person processes

## [1.11.5] - 2021-06-11
### Changed
- [KON-442](https://jira.itkdev.dk/browse/KON-442): Setting end time for a date in export revenue to be last possible time on day.

## [1.11.4] - 2021-06-04
### Changed
- [KON-438](https://jira.itkdev.dk/browse/KON-436): Selecting processes in revenue export based on the originally closed date.

## [1.11.3] - 2021-04-07
### Changed
- [SUPPORT-132](https://jira.itkdev.dk/browse/SUPP0RT-132): Adding more limits for processes shown on the dashboard.
- [SUPPORT-131](https://jira.itkdev.dk/browse/SUPP0RT-131): Disabling max execution time when creating exports.

## [1.11.2] - 2021-03-24
### Changed
- [SUPPORT-131](https://jira.itkdev.dk/browse/SUPP0RT-131): Streams exports to the browser.

## [1.11.1] - 2021-03-22
### Added
- [SUPPORT-131](https://jira.itkdev.dk/browse/SUPP0RT-131): Added possibility for fetching all processes in a more memory effecient way.

### Removed
- [SUPPORT-131](https://jira.itkdev.dk/browse/SUPP0RT-131): Removed composer.lock file.

## [1.11.0] - 2021-02-23
### Fixed
- [KON-404](https://jira.itkdev.dk/browse/KON-404): Provenu duplicates.

### Added
- [SUPPORT-93](https://jira.itkdev.dk/browse/SUPP0RT-93): Added process delete console command.
- [SUPPORT-101](https://jira.itkdev.dk/browse/SUPP0RT-101): Adding page for resuming processes
- [SUPPORT-118](https://jira.itkdev.dk/browse/SUPP0RT-118): Enabling cache for PHPSpreadsheet

## [1.10.0] - 2021-02-15
### Added
- [KON-388-](https://jira.itkdev.dk/browse/KON-388): Added format to BI exports.
### Fixed
- [SUPP0RT-62](https://jira.itkdev.dk/browse/SUPP0RT-62): Fixed check for when a case is considered as won.
- [DEVSUPP-384](https://jira.itkdev.dk/browse/DEVSUPP-384): Preventing double submissions on the new reminder form.
- [SUPPORT-93](https://jira.itkdev.dk/browse/SUPP0RT-93): Preventing multiple form submissions when creating new processes.

## [1.9.0] - 2021-01-04
### Added
- [KON-412](https://jira.itkdev.dk/browse/KON-299): Cli command for updating addresses on clients.
- [KON-394](https://jira.itkdev.dk/browse/KON-394): Adding draft functionality for conclusions.

## [1.8.0] - 2020-11-19
### Added
- [KON-299](https://jira.itkdev.dk/browse/KON-299): Adding companies to clients

### Changed
- [KON-393](https://jira.itkdev.dk/browse/KON-393): Preventing double submission on economy form

### Fixed
- [KON-391](https://jira.itkdev.dk/browse/KON-391): Journal entries sorted by update date instead of create date

## [1.7.5] - 2020-11-16
### Changed
- [KON-407](https://jira.itkdev.dk/browse/KON-407): Re-enabling CPR service from Serviceplatformen
- Using composer version 1 in github actions.

## [1.7.4] - 2020-10-29
### Changed
- [KON-414](https://jira.itkdev.dk/browse/KON-414): Added errors where appropriate.

## [1.7.3] - 2020-10-22
### Changed
- Disabled Serviceplatformen CPR service. Enabled internal CPR service.

## [1.7.2] - 2020-10-01
### Added
- [KON-407](https://jira.itkdev.dk/browse/KON-407): Adding CPR service from Serviceplatformen

### Changed
- [KON-377](https://jira.itkdev.dk/browse/KON-377): Moving field in form so the edit and new form looks the same
- [KON-402](https://jira.itkdev.dk/browse/KON-402): Adding column names to dashboard

## [1.7.1] - 2020-09-17
### Added
- [KON-375](https://jira.itkdev.dk/browse/KON-375): Search for existing processes on process creation page
- [KON-400](https://jira.itkdev.dk/browse/KON-400): Adding page for setting missing completing status on completed processes
- [KON-401](https://jira.itkdev.dk/browse/KON-375): Adding column in process status table that shows if status is completing status

### Changed
- [KON-385](https://jira.itkdev.dk/browse/KON-385): Changing markup and styling for reports
- [KON-396](https://jira.itkdev.dk/browse/KON-396): Feedback changes
- [KON-403](https://jira.itkdev.dk/browse/KON-403): Making status required when completing process

### Removed
- [KON-379](https://jira.itkdev.dk/browse/KON-379): Removing not visited processes from dashboard

## [1.7.0] - 2020-08-03
### Added
- [KON-333](https://jira.itkdev.dk/browse/KON-333): Possible to show completed processes on dashboard
- [KON-288](https://jira.itkdev.dk/browse/KON-288): Added links between processes
- [KON-356](https://jira.itkdev.dk/browse/KON-356): Added weekly choice when entering future savings revenue entries

## [1.6.8] - 2020-06-26
### Added
- [KON-317](https://jira.itkdev.dk/browse/KON-317): Added possibility for changing net value for completed processes by service
- [KON-330](https://jira.itkdev.dk/browse/KON-330): Mark statuses for use when completing processes
- [KON-355](https://jira.itkdev.dk/browse/KON-355): Add pr. week as possibility when registering services on case economy
- [KON-364](https://jira.itkdev.dk/browse/KON-364): Preventing double submissions when creating new journal entry

### Fixed
- [KON-371](https://jira.itkdev.dk/browse/KON-371): Show available statuses on process based on process type

## [1.6.7] - 2020-05-25
### Fixed
- [DEVSUPP-241](https://jira.itkdev.dk/browse/DEVSUPP-241): Fixed null pointer exception

## [1.6.6] - 2020-05-20
### Fixed
- [KON-368](https://jira.itkdev.dk/browse/KON-368): Fixed error on search

## [1.6.5] - 2020-05-20
### Fixed
- [KON-368](https://jira.itkdev.dk/browse/KON-368): Fixed error on missing case worker

## [1.6.4] - 2020-05-13
### Fixed
- [KON-289](https://jira.itkdev.dk/browse/KON-289): Fixed missing name on existing users

## [1.6.3] - 2020-05-06
### Added
- [Changelog](https://github.com/aakb/kontrolgruppen-core-bundle/pull/177): Added CHANGELOG file
- [KON-23](https://jira.itkdev.dk/browse/KON-23): Delete old processes
- [KON-289](https://jira.itkdev.dk/browse/KON-289): Show name of user when hovering AZ

### Changed
- [KON-362](https://github.com/aakb/kontrolgruppen-core-bundle/pull/184): Include non-revenue cases in KL report
- [KON-354](https://github.com/aakb/kontrolgruppen-core-bundle/pull/187): Changed BI fields
- [KON-360](https://github.com/aakb/kontrolgruppen-core-bundle/pull/185): Adjusted report
- [KON-361](https://github.com/aakb/kontrolgruppen-core-bundle/pull/188): Changed datetime picker (connected with https://github.com/aakb/kontrolgruppen-core-bundle/pull/189)

### Fixed
- [KON-362](https://github.com/aakb/kontrolgruppen-core-bundle/pull/183): Fixed annotation
- [KON-359](https://github.com/aakb/kontrolgruppen-core-bundle/pull/186): Added null check

## [1.6.1] - 2020-03-13
### Changed
- [KON-352](https://jira.itkdev.dk/browse/KON-352): Adjustments to report

## [1.6.0] - 2020-03-09
### Added
- [KON-350](https://jira.itkdev.dk/browse/KON-350): Client postal code added in BI export

## [1.5.0] - 2020-02-27
### Changed
- [KON-332](https://jira.itkdev.dk/browse/KON-332): New revenue form and calculation.

### Fixed
- [KON-348](https://jira.itkdev.dk/browse/KON-348): Middle name fix in CPR service
- [KON-304](https://jira.itkdev.dk/browse/KON-319): Fixed search params to include CPR with and without dashes.


## [1.4.1] - 2020-02-13
### Changed
- [KON-347](https://jira.itkdev.dk/browse/KON-347): Replaced first name and last name search parameters with full name search parameter.
- [KON-320](https://jira.itkdev.dk/browse/KON-320): Non assigned processes are shown at the bottom on dashboard page. Number of processes shown on the dashboard is user specific.
- [KON-345](https://jira.itkdev.dk/browse/KON-320): Result with unknown addresses from CPR service is now handled.

### Fixed
- [KON-321](https://jira.itkdev.dk/browse/KON-321): Fixed default sorting of processes on process index page to descending order.
