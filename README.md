# HR Management System – Laravel 12 + VueJS 3 (SPA)

This is a **feature-rich Human Resource Management System** developed using Laravel 12 and VueJS 3 (SPA architecture).  
It is designed for organizations to manage employees, leaves, attendance, payroll, and documents – all in one unified platform.

> 🔒 **Note:** This repository is for portfolio and demonstration purposes only.  
> The complete source code is private and not available for reuse or distribution.

---

## 🛠️ Technologies Used

- **Backend:** Laravel 12 with Breeze (Role-based Auth)
- **Frontend:** VueJS 3 (Single Page Application)
- **Database:** MySQL (Not included here)
- **Auth System:** Laravel Breeze with RBAC
- **Export:** PDF & CSV for payslips

---

## 📦 Core Modules

### ✅ Admin Panel
- Full control over all system entities
- Manage users, roles, and locations

### ✅ Reporting Managers
- Location-based access
- Approve attendance and leave requests
- Oversee assigned employees

### ✅ Employees
- View attendance, apply for leave, see payslips

---

## 🧱 Key Features

- 🌍 Multi-location and department structure  
- 🧾 Leave Management: 3 types with rule-based eligibility  
- 🕒 Attendance: shift-based + overtime request module  
- 📄 Document Tracking: expiry-based alerts  
- 💵 Payroll: automated salary + payslip generation

---

## 🔐 Role Hierarchy

```text
Admin → Reporting Manager → Employee
