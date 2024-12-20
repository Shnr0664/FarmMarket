# Farmer Market System Documentation
My contribution:
- Buyer main page: Advanced search and filtering for buyers.
- Email notifications
- Email verifications
- Inventory tracking
- Reports:
    - Buyer report avaliable as JSON, dowloadable PDF and CSV
    - Sales report avaliable as JSON, dowloadable PDF and CSV
    - Farmer report avaliable as JSON, dowloadable PDF and CSV 


## Team D
1.Amina Alisheva
2.Nurdana Razakhbergen
3.Miras Shaltayev
4.Shynaray Sagidullayeva
5.Baglan Zhubatkanov
6.Medgat Auken

## Table of Contents
1. [Introduction](#introduction)
2. [Project Overview](#project-overview)
3. [Technology Stack](#technology-stack)
4. [System Architecture](#system-architecture)
5. [Features](#features)
6. [Database Design](#database-design)
7. [API Documentation](#api-documentation)
8. [Frontend Design](#frontend-design)
9. [Deployment Guide](#deployment-guide)
10. [Branch Structure](#branch-structure)
11. [Conclusion](#conclusion)

---

## Introduction

The Farmer Market System (FMS) is a platform designed to facilitate direct transactions between farmers and buyers, offering features like product listings, order management, payment processing, and delivery management. It includes a web application and a mobile application, sharing a unified backend.

---

## Project Overview

### Purpose
To connect farmers and buyers, enabling transparent, efficient, and direct sales of agricultural products.

### Scope
FMS enables:
- **Farmers** to list, manage, and sell products.
- **Buyers** to search, negotiate, and purchase products.
- **Administrators** to oversee platform operations.

### Objectives
- Streamline agricultural e-commerce.
- Ensure secure and scalable operations.
- Enhance user experience with responsive interfaces.

---

## Technology Stack

### Backend
- **Framework:** Laravel (PHP)
- **Database:** Postgre (Relational Database)
- **API:** RESTful architecture with OAuth 2.0 for security.

### Frontend
- **Web Application:** React.js for a dynamic, component-based UI.
- **Mobile Application:** React Native for cross-platform development.

### Tools and Integrations
- **Notifications:** MailTrap.
- **Hosting:** Ngrok.

---

## System Architecture

### Overview
The system employs a microservices architecture for scalability. Components include:
1. **Backend API:** Laravel-based services.
2. **Web and Mobile Applications:** React.js and React Native.
3. **Database Layer:** PostgreSQL.

### UML Diagrams
Include diagrams like:
- **Use Case Diagram**
- **Activity Diagram**
- **Class Diagram**

---

## Features

### Core Features
1. **User Registration and Authentication**
    - Role-based access (Farmer, Buyer, Admin).
    - Secure login with multi-factor authentication.

2. **Farm Management**
    - Profile and farm details management.
    - Inventory tracking.

3. **Product Management**
    - Create, update, delete product listings.
    - Advanced search and filtering for buyers.

4. **Order Management**
    - Add to cart, place orders, and track delivery.
    - Built-in chat for price negotiation.

---

## Database Design

### Entity-Relationship Diagram (ERD)
The core entities are:
- **User**: Represents buyers, farmers, and admins.
- **Farm**: Managed by farmers.
- **Product**: Agricultural items listed for sale.
- **Order**: Tracks transactions between buyers and farmers.
- **Payment**: Records financial transactions.
- **Delivery**: Monitors product shipping.

### Schema Design
- Relationships are normalized.
- Keys and constraints ensure data integrity.

---

## API Documentation

### Authentication
- **Endpoint:** `POST /api/login`
- **Description:** Authenticates a user.
- **Parameters:** `email`, `password`

### Product Management
- **Endpoint:** `POST /api/products`
- **Description:** Create a new product.
- **Parameters:** `name`, `category`, `price`, `quantity`, `description`

### Order Tracking
- **Endpoint:** `GET /api/orders/:id`
- **Description:** Fetch order details.
- **Parameters:** `orderId`


---

## Frontend Design

### Web Application
- **Framework:** React.js
- **Components:**
    - `Navbar`
    - `ProductCard`
    - `OrderHistory`

### Mobile Application
- **Framework:** React Native
- **Features:**
    - Real-time notifications.
    - Optimized for iOS and Android.

---

## Deployment Guide

### Prerequisites
- **Server Requirements:** Node.js, PHP 8.0, MySQL.
- **Tools:** Docker, Git.

### Steps
1. **Backend Deployment**
    - Clone the repository.
    - Run `composer install`.
    - Configure `.env` file and migrate database.

2. **Frontend Deployment**
    - Run `npm install`.
    - Build with `npm run build`.
    - Deploy to hosting platform.


---

## Branch Structure

### Main Branch
- Contains the **frontend** application.
- Includes all React.js components for the web interface.

### New_Branch
- Contains the **backend** application.
- Includes Laravel services, database migrations, and API endpoints.

---

## Conclusion

The Farmer Market System is a robust, user-centric platform designed to streamline agricultural transactions. With its modern architecture, intuitive UI, and powerful features, it stands poised to revolutionize e-commerce for the agricultural sector. By utilizing a clear branch structure, development and maintenance are simplified, ensuring continuous and seamless improvements. 

---
