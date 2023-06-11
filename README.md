Laravel Blood Donation Management System
[Project Description]

Table of Contents
Authentication and Authorization
Donation Centers and Locations
Request Blood and Appointments
Events
Admins Management
Reports for Centers
Charts
Notifications and Messages
Authentication and Authorization
Repository: project-authentication
Description: Contains code related to user authentication, session management, token-based authentication, roles, and permissions.
Donation Centers and Locations
Repository: project-donation-centers
Description: Handles the management of donation centers, including CRUD operations for centers, admins, and employees. Also includes location-based functionalities and integration with Google Maps API.
Request Blood and Appointments
Repository: project-blood-requests
Description: Manages the blood donation process, including appointment scheduling, availability checking, and blood requests from users and hospitals. Handles the logic for assigning requests to centers and employees.
Events
Repository: project-events
Description: Handles the creation and management of blood donation events. Allows hospitals and center admins to request events, with super admin making the final decision. Manages event dates, participation centers, and user notifications.
Admins Management
Repository: project-admins
Description: Handles the management of administrators for the system. Includes functionalities for adding new admins, sending email invitations to set credentials, and super admin's control over other admins.
Reports for Centers
Repository: project-reports
Description: Generates PDF reports for the centers based on specified criteria. Allows center admins to customize report content and save reports to the database.
Charts
Repository: project-charts
Description: Provides charting functionalities for displaying information about the centers. Includes charts for blood donated, stock levels, and other relevant data. Allows admins to view charts for all centers.
Notifications and Messages
Repository: project-notifications-messages
Description: Implements a notification system using a combination of database and email. Handles sending notifications to actors and facilitates messaging between admins and hospitals.
Contributing
Contributions are welcome! If you'd like to contribute to this project, please follow the Contributing Guidelines.

License
This project is licensed under the MIT License.

Acknowledgements
[List any acknowledgements or external libraries used in the project.]

Feel free to customize this template according to your project's requirements. Add sections like Installation, Usage, and Configuration if needed. Also, include any relevant badges or shields (e.g., build status, code coverage) to showcase the project's status.
