# Exclusive Repairs — Local launch notes

Recommended PHP runtime: PHP 7.1 - 7.4 (CodeIgniter 3.1.9 + bundled libs are known to work).

Required PHP extensions (install if missing):
- `mysqli`, `pdo_mysql`, `mbstring`, `xml`, `zip`, `gd`, `json`, `fileinfo`

Quick local launch steps

1. Verify PHP version:

```bash
php -v
```

2. Create a database and import the dump (adjust user/password as needed):

```bash
# create DB (adjust mysql user as needed)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS bookingsoftware CHARACTER SET utf8 COLLATE utf8_general_ci;"
# import dump
mysql -u root -p bookingsoftware < database/db.sql
```

3. Update database credentials in `application/config/database.php` (set `hostname`, `username`, `password`).

4. Verify `base_url` in `application/config/config.php`.

5. Ensure writable directories:

```bash
# from project root
mkdir -p application/cache application/logs
chmod -R 0777 application/cache application/logs
```

6. Start PHP built-in server for quick testing (from project root):

```bash
php -S localhost:8000 -t .
# then open http://localhost:8000/index.php
```

Notes & troubleshooting
- The repo contains legacy `mysql_*` driver files under `system/database/drivers/mysql/`, but the app's active driver is `mysqli` by default (`application/config/database.php`). If you switch to `mysql` driver the site will fail on PHP 7+.
- `ereg()` occurrences are only present in the bundled `PHPExcel`/PCLZip files and are commented out; no active `ereg()` calls were found in application code.
- If you encounter runtime PHP errors, capture the full error text (or application/logs entries) and share them; I will create minimal, targeted fixes.

Next actions I can take for you:
- Run repo-wide static checks and produce a short patch list. (pending)
- Apply safe replacements for any active deprecated calls found at runtime. (on request)

If you want me to attempt automated patches now (e.g., replace active `ereg` or `mysql_*` usages), reply `patch` and I will produce a focused set of changes.

---
Generated: February 17, 2026
# Exclusive Repairs
An open source FREE mobile / cell phone repair jobs management and invoicing software. 

## Core Features:
* Dashboard Reporting
* Mobile repair booking with all basic inspection parameters
* Multi Stores management
* Multi users login
* Customer Management
* Thermal Printer Invoices
* Lesear Printer Invoices
* Email notifications
* No limitations
* Easy interface with basic features
* 100% open source software you are free to extend or modify.

## DEMO
http://exclusiveunlock.co.uk/repairs/index.php/job
* Username: admin
* Password: demo1234

## Video
[![IMAGE ALT TEXT HERE](http://img.youtube.com/vi/PSkUD1JIRP0/0.jpg)](https://youtu.be/PSkUD1JIRP0)

## Installation:
1. Create database with name `exclusiverepairs`.
2. Import database file `database/db.sql`.
3. Configure database credentials in file `application/config/database.php`.
4. Set base_url in file `application/config/config.php`

## Default Credentials:
http://localhost/exclusiverepairs/

U: `admin`
P: `demo1234`

## Installation Service
If you need installation service or pro-features, Please contact me at shariq2k@yahoo.com.

## Issues

If you come across any issues please [report them here](https://github.com/muhammad-shariq/exclusiverepairs/issues).

## Contributing

Thank you for considering contributing to the ExclusiveRepairs project! Please feel free to make any pull requests, or e-mail me a feature request you would like to see in the future email at shariq2k@yahoo.com.

## Security Vulnerabilities

If you discover a security vulnerability within this project, please send an e-mail to shariq2k@yahoo.com, or create a pull request if possible. All security vulnerabilities will be promptly addressed.

## License

MIT: [https://opensource.org/licenses/MIT](https://opensource.org/licenses/MIT)