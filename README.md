# -Parking-Web-Project
Smart Parking Management System.
A Smart Parking Management System to address urban parking issues with an online solution for effective space allocation, reservations. The system minimizes congestion, waiting time, and enhances the user experience through automation and data-driven management.

Features

User Management: Secure sign up, sign in, profile management.

Parking Lot Management: Monitoring the availability, reserved and occupied spaces in real time.

Reservation System: Pre-book parking with flexible time options.

Admin Dashboard: Analytics for tracking usage and allocating space.

Notify :Alerts and reminders for All Providers on Booked Confirmation.

System Architechure  

U -User (Web App) 
F -frontend
B-backend
DB-database.
P- Gateway Payment
A-Admin
 
Project Strucure

├── backend/                 # Server-side logic (APIs, authentication, reservations)
│   ├── config/              # Environment variables, database config
│   ├── controllers/         # Business logic
│   ├── models/              # Database schemas
│
├── frontend/                # User interface (Web)
│   ├── components/          # Reusable UI components
│   ├── pages/               # Page views
│
├── docs/                    # Documentation (ERD, diagrams)
│
├── tests/                   # Unit & integration tests
│
├── .env.example             # Example environment configuration
├── README.md                # Project documentation

Technologies Used

Frontend: JavaScript, CSS and HTML

Backend: PHP

Database: MySQL

Authentication: PHP

IoT Integration (Optional): RFID sensors, Arduino, or Raspberry Pi

Deployment: GitHub

Database Schema

  USERS 
        int user_id PK
        string name
        string email
        string password
    
  PARKING_SPACES 
        int space_id PK
        string location
        string status
    
   RESERVATIONS 
      int reservation_id PK
        int user_id FK
        int space_id FK
        datetime start_time
        datetime end_time
        string status
        
Installation and setup
Prerequisites

Visual Studio Code, version 3 and above
XAMMP control panel, version 10 and above 
Windows version 10 and above
A laptop
Wifi router
At least 50MB storage

Future Enhancements

AI-driven parking space demand forecasting.

 Dynamic pricing structure that takes location and demand into account.

 Blockchain technology for safe transactions.

 Connectivity to electric vehicle charging stations.

 Reservations with voice assistant compatibility.

    


