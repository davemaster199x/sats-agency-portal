# Agency Portal - CodeIgniter Framework

## Overview

An advanced Agency Portal built using CodeIgniter 4, designed to streamline agency operations, client management, and business processes. This portal provides a centralized platform for agencies to manage their clients, projects, and resources efficiently.

## Tech Stack

- CodeIgniter 4.x
- PHP 8.1+
- MySQL 8.0+
- Bootstrap 5
- jQuery 3.x
- DataTables
- Select2
- Chart.js

## Directory Structure

```
agency-portal/
├── app/
│   ├── Config/
│   ├── Controllers/
│   │   ├── Agency.php
│   │   ├── Clients.php
│   │   ├── Dashboard.php
│   │   ├── Projects.php
│   │   └── Reports.php
│   ├── Models/
│   │   ├── AgencyModel.php
│   │   ├── ClientModel.php
│   │   ├── ProjectModel.php
│   │   └── UserModel.php
│   ├── Views/
│   │   ├── agency/
│   │   ├── clients/
│   │   ├── dashboard/
│   │   ├── layout/
│   │   └── reports/
│   ├── Helpers/
│   ├── Libraries/
│   └── Filters/
├── public/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── uploads/
│   └── index.php
├── tests/
├── writable/
└── vendor/
```

## Features

### Dashboard

- Activity overview
- Key performance indicators
- Recent activities
- Upcoming tasks
- Project status summaries
- Revenue analytics

### Agency Management

- Agency profile management
- Branch management
- Staff directory
- Role-based access control
- Department organization
- Resource allocation

### Client Management

- Client profiles
- Contact management
- Communication history
- Document storage
- Client categorization
- Service agreements

### Project Management

- Project creation and tracking
- Task assignment
- Timeline management
- Resource allocation
- Progress monitoring
- Client approval workflow

### Financial Management

- Invoice generation
- Payment tracking
- Expense management
- Budget monitoring
- Financial reports
- Revenue forecasting

## Installation

1. Clone the repository

```bash
git clone https://github.com/your-org/agency-portal.git
cd agency-portal
```

2. Install dependencies

```bash
composer install
```

3. Configure environment

```bash
cp env .env
# Edit .env with your configuration
```

4. Setup database

```bash
php spark migrate
php spark db:seed InitialSeeder
```

5. Start development server

```bash
php spark serve
```

## Configuration

### Database Configuration

```env
database.default.hostname = localhost
database.default.database = agency_portal
database.default.username = your_username
database.default.password = your_password
database.default.DBDriver = MySQLi
```

### Email Configuration

```env
email.fromEmail = 'noreply@agency.com'
email.fromName = 'Agency Portal'
email.SMTPHost = 'smtp.mailtrap.io'
email.SMTPPort = 2525
email.SMTPUser = 'your_username'
email.SMTPPass = 'your_password'
```

## Development Guidelines

### Coding Standards

- Follow PSR-12 coding standards
- Use CodeIgniter 4 conventions
- Implement proper error handling
- Document all methods
- Write unit tests for critical functions

### Database Migrations

- Create migrations for schema changes

```bash
php spark make:migration CreateAgencyTable
```

### Model Creation

- Generate models using CodeIgniter CLI

```bash
php spark make:model AgencyModel
```

### Controller Creation

- Use RESTful conventions

```bash
php spark make:controller Agency
```

## API Endpoints

### Agency API

```
GET    /api/agencies          - List all agencies
POST   /api/agencies          - Create new agency
GET    /api/agencies/{id}     - Get agency details
PUT    /api/agencies/{id}     - Update agency
DELETE /api/agencies/{id}     - Delete agency
```

### Client API

```
GET    /api/clients           - List all clients
POST   /api/clients           - Create new client
GET    /api/clients/{id}      - Get client details
PUT    /api/clients/{id}      - Update client
DELETE /api/clients/{id}      - Delete client
```

## Security Features

- CSRF protection
- XSS filtering
- SQL injection prevention
- Input validation
- Role-based access control
- Session management
- Password hashing

## Testing

```bash
# Run all tests
php spark test

# Run specific test suite
php spark test --filter AgencyTest
```

## Deployment

### Server Requirements

- PHP 8.1 or higher
- MySQL 8.0+
- Apache/Nginx
- Composer
- SSL certificate

### Production Setup

1. Set environment to production
2. Configure server rewrite rules
3. Set appropriate file permissions
4. Enable PHP OPcache
5. Configure cron jobs

## Maintenance

### Regular Tasks

- Database backup
- Log rotation
- Cache clearing
- Security updates
- Performance monitoring

### Troubleshooting

- Check error logs in `writable/logs`
- Verify database connectivity
- Monitor server resources
- Review access logs

## Support

- Technical Documentation: `/docs`
- Issue Tracking: GitHub Issues
- Email Support: support@agency-portal.com

## Contributing

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

---

Version: 2.0.0
Last Updated: November 2024
