CITYCARE - SMART CITY GRIEVANCE REPORTING
==========================================

FEATURES
- Citizens can report potholes, garbage, water leaks and other issues.
- Optional photo upload (JPG, PNG, WEBP; max 3 MB).
- Every report receives a tracking ID such as SC-00001.
- Recent reports and their statuses are visible to everyone.
- Admin dashboard can update status: Submitted, Assigned, In Progress, Resolved, Rejected.
- PHP + MySQL + HTML + CSS. Designed to run with XAMPP.

REQUIREMENTS
- Windows computer
- XAMPP (Apache + MySQL)
- Web browser
- VS Code (optional, for editing)

STEP-BY-STEP RUN IN XAMPP
1. Install and open XAMPP.
2. Start Apache and MySQL. Both should show as running.
3. Extract smart_city_grievance_reporting.zip.
4. Copy the extracted folder named "smart_city_grievance_reporting" into:
   C:\xampp\htdocs\
   Final path should be C:\xampp\htdocs\smart_city_grievance_reporting\
5. Open this URL in your browser:
   http://localhost/phpmyadmin
6. In phpMyAdmin, click the Import tab.
7. Choose database.sql from the project folder and click Import/Go.
   This creates the database "smart_city_grievance" and the reports table.
8. Open the project folder in VS Code:
   File > Open Folder > C:\xampp\htdocs\smart_city_grievance_reporting
9. If your MySQL username/password differs from XAMPP defaults, edit config.php.
   Default XAMPP settings are username=root and blank password.
10. Open the citizen website:
    http://localhost/smart_city_grievance_reporting/
11. Open the authority dashboard:
    http://localhost/smart_city_grievance_reporting/admin.php
12. Demo admin password: citycare123

HOW TO TEST
1. On the citizen page, submit a test report.
2. Note the tracking ID shown after submission.
3. Check that the new report appears under Recent Community Reports.
4. Open admin.php and sign in using citycare123.
5. Change the report status to Assigned, In Progress or Resolved and click Save.
6. Return to the citizen page and refresh to see the updated status.

IMPORTANT
- This is a college/demo project, not a production civic service.
- Change the demo admin password in admin.php before any real deployment.
- For production, add proper admin accounts, CSRF protection, rate limiting,
  stronger upload scanning, privacy notices, backups and HTTPS.
- The upload folder contains user-submitted images. Do not run uploaded files.
- If you see "Database connection failed", confirm MySQL is running and check config.php.
- If Apache does not start, another program may be using port 80. Check XAMPP's
  port settings or close the conflicting service.
