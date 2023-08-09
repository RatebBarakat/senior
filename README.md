# Blood Donation Management System

## Table of Contents
- [Used Technologies](#used-technologies)
- [Authentication and Authorization](#authentication-and-authorization)
- [Donation Centers and Locations](#donation-centers-and-locations)
- [Request Blood and Appointments](#request-blood-and-appointments)
- [Events](#events)
- [Admins Management](#admins-management)
- [Reports for Centers](#reports-for-centers)
- [Charts](#charts)
- [Notifications and Messages](#notifications-and-messages)
- [Contributing](#contributing)
- [License](#license)
- [Acknowledgements](#acknowledgements)

## Used Technologies 
- Laravel
- Livewire
- API
- Vue js [link here](https://github.com/RatebBarakat/senior-front)

## Authentication and Authorization

- Contains code related to user authentication, session management, token-based authentication, roles, and permissions.

## Donation Centers and Locations

- Handles the management of donation centers, including CRUD operations for centers, admins, and employees. Also includes location-based functionalities and integration with Google Maps API.

## Request Blood and Appointments

- Manages the blood donation process, including appointment scheduling, availability checking, and blood requests from users and hospitals. Handles the logic for assigning requests to centers and employees.

## Events

- Handles the creation and management of blood donation events. Allows hospitals and center admins to request events, with super admin making the final decision. Manages event dates, participation centers, and user notifications.

## Admins Management

- Handles the management of administrators for the system. Includes functionalities for adding new admins, sending email invitations to set credentials, and super admin's control over other admins.

## Reports for Centers

- Generates PDF reports for the centers based on specified criteria. Allows center admins to customize report content and save reports to the database.

## Charts

- Provides charting functionalities for displaying information about the centers. Includes charts for blood donated, stock levels, and other relevant data. Allows admins to view charts for all centers.

## Notifications and Messages

- Implements a notification system using a combination of database and email. Handles sending notifications to actors and facilitates messaging between admins and hospitals.

## Contributing

Contributions are welcome! If you'd like to contribute to this project, please follow the [Contributing Guidelines](CONTRIBUTING.md).

## License

This project is licensed under the [MIT License](LICENSE.md).

## Acknowledgements

[List any acknowledgements or external libraries used in the project.]

