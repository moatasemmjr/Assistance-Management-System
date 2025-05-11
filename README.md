# Assistance Management System

A full-featured system designed to efficiently and transparently manage the registration and distribution of aid to beneficiaries. The system enables administrators to control beneficiary records, monitor aid activities, generate reports, and export data in multiple formats.

## 📊 System Overview

### 🧭 Dashboard

The main control panel for system administrators. It includes a variety of essential tools for managing both beneficiaries and aid records.

**Key Functions:**
- **Add New Beneficiary:** Enter personal data including name, address, phone number, and more.
- **Edit Beneficiary Data:** Modify existing beneficiary information.
- **Delete Beneficiary:** Remove records when needed or upon request.
- **Print Beneficiary Aid Report:** Generate detailed aid history for any beneficiary.
- **View Beneficiaries & Last Aid Date:** Display a list of all beneficiaries and their last received aid.
- **Display Total Beneficiaries and Aid Count:** View real-time statistics on aid distribution.
- **Navigate to Assistance Management Page:** Direct access to the dedicated aid management section.
- **Export Full Data to Excel:** Generate full backups including all aid and beneficiary data.
- **Generate Multiple Reports:** 
  - Beneficiary data reports (PDF & Excel)
  - Aid collection invitations
  - Daily, weekly, and monthly summaries
  - Custom reports for partner organizations

---

### 🎁 Assistance Management Page

This module enables aid administrators to manage the full lifecycle of aid distribution.

**Key Functions:**
- **Add New Aid to Individual Beneficiaries:** Enter aid details such as type, amount, and date.
- **Distribute Aid to Groups:** Assign the same aid to multiple beneficiaries in a single action.
- **Print Aid Pickup Invitations (with Date & Location):** Generate collection letters with pickup details.
- **Print Invitations without Saving to Database:** Useful for urgent or temporary cases.
- **Track Non-Recipients:** Identify beneficiaries who haven’t received specific types of aid.

---

### 📄 Reports & Backups

The system supports advanced reporting and backup capabilities.

**Available Reports:**
- Full beneficiary data (PDF and Excel)
- Aid history reports per beneficiary
- Organization-specific aid summaries
- Daily, weekly, and monthly aid reports

**Backup:**
- Generate full Excel backup of the system database

---

## 🛠️ Technologies Used

- **PHP & MySQL** – Core backend and database engine
- **Bootstrap** – Responsive frontend design
- **TCPDF** – PDF report generation
- **PhpSpreadsheet** – Excel file export and backup

---

## 🚀 Installation Guide

1. Clone or download the project files.
2. Import the SQL database (`.sql` file) into your MySQL server.
3. Configure database connection settings in `config.php` or `.env`.
4. Launch the system in your browser via a local or online web server.
5. Login with admin credentials to access the dashboard.

---

## 👨‍💻 Developer

Developed by **Motasem El-Rabai**  
📧 [motsm2001@hotmail.com](mailto:motsm2001@hotmail.com)  
🌐 [LinkedIn](https://www.linkedin.com/in/motasem-elrabai-2baa9b290)  
🔗 [GitHub](https://github.com/moatasemmjr)

---

## 📜 License

This project is open-source 
