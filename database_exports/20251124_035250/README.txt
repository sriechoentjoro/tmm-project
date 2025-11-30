TMM Database Export
===================
Export Date: 20251124_035250
Total Databases: 12
Total Size: 0 MB

Database List:
--------------

Import Instructions:
--------------------

On Linux/Unix (Production Server):
1. Upload all .sql files to server:
   scp *.sql root@103.214.112.58:/tmp/

2. SSH to server:
   ssh root@103.214.112.58

3. Run import script:
   cd /tmp
   bash import_all.sh root your_mysql_password

Or import individually:
   mysql -u root -p < cms_masters.sql
   mysql -u root -p < cms_lpk_candidates.sql
   ... etc

On Windows (Local):
1. Run import batch:
   import_all.bat root your_mysql_password

Or import individually:
   mysql -u root -p62xe6zyr < cms_masters.sql
   mysql -u root -p62xe6zyr < cms_lpk_candidates.sql
   ... etc

Features:
---------
- Each SQL file includes DROP DATABASE IF EXISTS statement
- Automatically creates database with utf8mb4 charset
- Includes all tables, data, triggers, routines, and events
- Safe to re-run without manual cleanup

Notes:
------
- All databases will be DROPPED and RECREATED
- Existing data will be LOST
- Backup before importing on production
- Verify database names match your configuration
