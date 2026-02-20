#!/usr/bin/env php
<?php
/**
 * DEPLOYMENT CHECKLIST & GUIDE
 * Exclusive Repairs Booking System - PHP 8.3 + PhpSpreadsheet
 * 
 * This document provides step-by-step instructions for deploying the application
 * to staging or production environments after PHP 8.3 upgrades and PhpSpreadsheet migration.
 */

echo <<<'HEADER'
╔══════════════════════════════════════════════════════════════════════════════╗
║                DEPLOYMENT CHECKLIST & GUIDE                                  ║
║        Exclusive Repairs - PHP 8.3 + PhpSpreadsheet Migration Ready          ║
╚══════════════════════════════════════════════════════════════════════════════╝

This application has been upgraded to PHP 8.3 and migrated from PHPExcel to     
PhpSpreadsheet. Follow the steps below for successful deployment.

════════════════════════════════════════════════════════════════════════════════

HEADER;

// Generate the checklist
$checklist = [
    'PRE-DEPLOYMENT' => [
        '[ ] Verify target server PHP version is ≥ 8.0 (recommend 8.3.x)',
        '[ ] Confirm staging/production database is accessible',
        '[ ] Backup existing database and application files',
        '[ ] Verify file permissions are correct (755 for dirs, 644 for files)',
        '[ ] Test all export endpoints in current environment before deployment',
    ],
    'ENVIRONMENT SETUP' => [
        '[ ] Clone/pull latest code to deployment directory',
        '[ ] Run: composer install --no-dev (for production)',
        '[ ] OR: composer install (for staging)',
        '[ ] Verify vendor/autoload.php exists',
        '[ ] Verify vendor/phpoffice/phpspreadsheet is installed',
    ],
    'CONFIGURATION' => [
        '[ ] Update application/config/database.php with deployment credentials',
        '[ ] Verify database driver is set to "mysqli"',
        '[ ] Check database.php hostname/port/username/password match server',
        '[ ] Ensure config.php has correct base_url for your domain',
        '[ ] Verify encryption_key is set in config.php if using encrypted data',
    ],
    'PERMISSION CHECKS' => [
        '[ ] Make application/cache/ writable (chmod 777)',
        '[ ] Make application/logs/ writable (chmod 777)',
        '[ ] Verify storage directory for exports is writable',
        '[ ] Set correct file ownership (chown www-data:www-data on Linux)',
    ],
    'DATABASE MIGRATION' => [
        '[ ] Import database schema from database/db.sql if starting fresh',
        '[ ] Or run database migrations if applicable',
        '[ ] Verify all 9+ tables exist (jobs, customers, employees, etc.)',
        '[ ] Check that data is properly imported (run SELECT COUNT(*) on tables)',
    ],
    'EXTENSION VERIFICATION' => [
        '[ ] Verify php -m shows: mysqli, pdo_mysql, mbstring, xml, zip, gd',
        '[ ] Check bcmath, curl, json are installed',
        '[ ] If using PDF exports, verify ext-imagick or GD is installed',
        '[ ] Run: php -i | grep -i "php version" to confirm PHP version',
    ],
    'EXPORT FEATURE TESTING' => [
        '[ ] Test Job export endpoint: GET /job/export_all?format=xlsx',
        '[ ] Test single job export: GET /job/export_single/1?format=xlsx',
        '[ ] Test Customer export: GET /customer/export_all?format=xlsx',
        '[ ] Test Email template export: GET /email/export_all?format=xlsx',
        '[ ] Test CSV format: GET /job/export_all?format=csv',
        '[ ] Verify files download with correct headers (Content-Disposition)',
        '[ ] Open exported files and verify data is correct',
    ],
    'CORE FUNCTIONALITY TESTS' => [
        '[ ] Test login functionality (verify session handling works)',
        '[ ] Test CRUD operations on Customers, Jobs, Email Templates',
        '[ ] Create a new job and verify it saves correctly',
        '[ ] Test AJAX list operations (dataTables)',
        '[ ] Test form validation error messages',
        '[ ] Check admin-only features work correctly',
    ],
    'ERROR LOGGING & MONITORING' => [
        '[ ] Check application/logs/ for any errors or warnings',
        '[ ] Verify error_reporting is set appropriate to environment',
        '[ ] Set up log rotation if needed (daily/weekly)',
        '[ ] Enable application monitoring/alerting if available',
        '[ ] Configure email notifications for critical errors if needed',
    ],
    'PERFORMANCE TUNING' => [
        '[ ] Enable PHP opcode caching (OPcache) for production',
        '[ ] Configure MySQL query cache appropriately',
        '[ ] Test export with large datasets (100+ records)',
        '[ ] Monitor memory usage during export operations',
        '[ ] Consider adding database indexes on frequently queried columns',
    ],
    'SECURITY' => [
        '[ ] Verify HTTPS is enabled on production domain',
        '[ ] Check .htaccess or web.config protection is in place',
        '[ ] Verify session.secure and session.httponly are set',
        '[ ] Test SQLi and XSS protections (CodeIgniter built-in)',
        '[ ] Ensure admin actions require proper authentication',
        '[ ] Review and update firewall rules if needed',
    ],
    'POST-DEPLOYMENT' => [
        '[ ] Create backup of deployed code and database',
        '[ ] Document exact PHP version and extensions deployed',
        '[ ] Update deployment runbook with any custom configurations',
        '[ ] Notify team of successful deployment',
        '[ ] Monitor error logs and performance for 24 hours',
        '[ ] Begin user acceptance testing (UAT)',
    ],
];

// Display checklist with sections
foreach ($checklist as $section => $items) {
    echo "\n" . str_repeat("═", 80) . "\n";
    echo "  " . strtoupper($section) . "\n";
    echo str_repeat("═", 80) . "\n";
    foreach ($items as $item) {
        echo "  $item\n";
    }
}

// Deployment commands
echo "\n\n" . str_repeat("═", 80) . "\n";
echo "  DEPLOYMENT COMMANDS\n";
echo str_repeat("═", 80) . "\n";
?>

# 1. PREPARE DEPLOYMENT DIRECTORY
cd /var/www/html
git clone <your-repo-url> exclusive-repairs-new
cd exclusive-repairs-new

# 2. INSTALL DEPENDENCIES
composer install --no-dev --optimize-autoloader

# 3. SET PERMISSIONS
chmod 755 application/cache application/logs
chown -R www-data:www-data .
chown -R www-data:www-data application/cache
chown -R www-data:www-data application/logs

# 4. UPDATE CONFIGURATION
cp application/config/database.php.example application/config/database.php
# Edit database.php with deployment credentials
nano application/config/database.php

# 5. VERIFY PHP VERSION AND EXTENSIONS
php -v
php -m | grep -E 'mysqli|pdo_mysql|mbstring|xml|zip|gd|bcmath|curl|json'

# 6. TEST DATABASE CONNECTION
php -r "
require 'vendor/autoload.php';
\$config = require 'application/config/database.php';
try {
    \$db = new mysqli(
        \$config['default']['hostname'],
        \$config['default']['username'],
        \$config['default']['password'],
        \$config['default']['database']
    );
    if (\$db->connect_error) {
        die('Connection failed: ' . \$db->connect_error);
    }
    echo 'Database connection successful!' . PHP_EOL;
    \$result = \$db->query('SELECT COUNT(*) as cnt FROM jobs');
    \$row = \$result->fetch_assoc();
    echo 'Jobs table contains: ' . \$row['cnt'] . ' records' . PHP_EOL;
} catch (Exception \$e) {
    die('Error: ' . \$e->getMessage());
}
"

# 7. TEST EXPORT FUNCTIONALITY
php -r "
require 'vendor/autoload.php';
if (!file_exists('vendor/phpoffice/phpspreadsheet/src/PhpOffice/PhpSpreadsheet/Spreadsheet.php')) {
    die('PhpSpreadsheet not found!');
}
\$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
\$sheet = \$spreadsheet->getActiveSheet();
\$sheet->setCellValue('A1', 'Test');
echo 'PhpSpreadsheet is working!' . PHP_EOL;
"

# 8. CONFIGURE WEB SERVER
# For Apache:
# 1. Copy .htaccess to web root if using URL rewriting
# 2. Enable mod_rewrite: a2enmod rewrite
# 3. Restart Apache: systemctl restart apache2

# For Nginx:
# 1. Configure location blocks to route requests through index.php
# 2. Example rewrite rule:
# try_files \$uri \$uri/ /index.php?\$query_string;

# 9. ROLLBACK PLAN (if issues occur)
# Keep old deployment directory for 48 hours minimum
# Restore from backup: mysqldump bookingsoftware > backup.sql
# If critical issues, update DNS/load balancer to point to old version

<?php
echo str_repeat("═", 80) . "\n";
echo "  TROUBLESHOOTING\n";
echo str_repeat("═", 80) . "\n";
?>

1. "Headers already sent" errors:
   - Check for BOM in PHP files
   - Verify no output before ob_start()
   - Check for PHP errors in logs

2. "Undefined class PhpSpreadsheet" errors:
   - Verify vendor/autoload.php exists
   - Run: composer dump-autoload
   - Check composer.json has phpoffice/phpspreadsheet

3. Database connection failures:
   - Verify database credentials in database.php
   - Check MySQL server is running
   - Verify network access to database host
   - Check firewall rules allow 3306 (MySQL)

4. Export files not downloading:
   - Verify headers aren't being sent before output
   - Check file permissions in application/cache/
   - Verify PHP memory limit is sufficient (256M minimum)
   - Check browser console for network errors (F12)

5. Memory exhaustion during export:
   - Increase php.ini: memory_limit = 512M
   - Split large exports into smaller batches
   - Enable compression in PhpSpreadsheet

<?php
echo "\n" . str_repeat("═", 80) . "\n";
echo "  ROLLBACK PROCEDURE\n";
echo str_repeat("═", 80) . "\n";
?>

If critical issues are encountered:

1. Stop new application:
   - Update web server config to point to old version
   - Or restore old code from backup

2. Restore database:
   - mysqldump bookingsoftware < backup.sql
   - Or use point-in-time recovery from backups

3. Verify old version is working:
   - Test critical endpoints
   - Check error logs
   - Verify data integrity

4. Document issues:
   - Collect all error logs
   - Note exact error messages
   - Save failed deployment configuration
   - Plan fixes before next deployment attempt

<?php
echo "\n" . str_repeat("═", 80) . "\n";
echo "  SUPPORT CONTACTS & RESOURCES\n";
echo str_repeat("═", 80) . "\n";
?>

PhpSpreadsheet Documentation:
  https://phpspreadsheet.readthedocs.io/

CodeIgniter 3 Documentation:
  https://codeigniter.com/userguide3/

PHP 8.3 Compatibility:
  https://www.php.net/manual/en/migration83.php

Common Changes from PHP 7.x to 8.3:
  - Dynamic properties now require #[\AllowDynamicProperties]
  - String/array offset now use {} for curly-braces (deprecated)
  - Constructor property promotion available

Monitoring & Logging:
  - Monitor application/logs/ for errors
  - Set up email alerts for critical errors
  - Track export performance metrics

<?php
echo "\n" . str_repeat("═", 80) . "\n";
echo "  DEPLOYMENT COMPLETE CHECKLIST\n";
echo str_repeat("═", 80) . "\n";
echo <<<'FOOTER'

Once all steps above are completed:

[ ] Development environment upgraded to PHP 8.3
[ ] All CodeIgniter core classes patched for PHP 8.x compatibility
[ ] PHPExcel replaced with PhpSpreadsheet via Composer
[ ] All export endpoints created and tested
[ ] Database migrated and verified
[ ] All required PHP extensions installed and verified
[ ] File permissions set correctly
[ ] Security checks passed
[ ] Performance validated with expected load
[ ] Rollback procedure documented
[ ] Team trained on new export features
[ ] Monitoring and alerting configured

DEPLOYMENT READY FOR PRODUCTION!

════════════════════════════════════════════════════════════════════════════════

Questions or Issues? Review:
1. Application logs at: application/logs/
2. Error handling in: system/core/Exceptions.php
3. Database config at: application/config/database.php
4. Export feature code at: application/controllers/{Job,Customer,Email}.php

FOOTER;
?>
