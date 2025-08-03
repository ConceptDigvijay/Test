# गणपती वर्गणी सिस्टम / Ganpati Donation System

A complete PHP + MySQL system for collecting and managing Ganpati donations from society members with PDF receipt generation and admin panel.

## Features

### 🎯 Public Donation Form
- **Responsive Design**: Built with Tailwind CSS and Khand font for Marathi support
- **Form Fields**: Name, Flat Number, Amount, Payment Mode (Cash/UPI)
- **Real-time Validation**: Client-side and server-side validation
- **Marathi-English Interface**: Bilingual labels and messages

### 📄 PDF Receipt Generation
- **FPDF Integration**: Custom PDF generation with template support
- **Marathi Text Support**: Complete Marathi number-to-words conversion
- **Receipt Details**: Auto-generated receipt number, date, donor details
- **Professional Layout**: Formatted receipt with headers and signatures

### 👨‍💼 Admin Panel
- **Secure Login**: Password-protected admin access
- **Dashboard**: Statistics overview with total donations, amounts
- **Donation Management**: View all donations with search and pagination
- **Receipt Downloads**: Direct links to download/view receipts
- **Real-time Data**: Live statistics and recent donations

### 🔢 Marathi Number Conversion
- **Complete Support**: Numbers converted to Marathi words
- **Proper Format**: "एकशे एक रुपये मात्र" format
- **Large Numbers**: Support for lakhs and crores

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone/Download** the project files to your web server directory

2. **Database Setup**:
   ```bash
   mysql -u root -p < db.sql
   ```
   
3. **Configure Database** in `db.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'ganpati_donations');
   ```

4. **Set Permissions**:
   ```bash
   chmod 755 templates/
   chmod 644 templates/receipt_template.pdf
   ```

5. **Access the Application**:
   - Public Form: `http://yourdomain.com/`
   - Admin Panel: `http://yourdomain.com/admin/`

## Default Admin Credentials

- **Username**: `admin`
- **Password**: `admin123`

> ⚠️ **Important**: Change the default password after first login!

## File Structure

```
ganpati-donations/
├── index.php                 # Public donation form
├── save.php                  # Process donation submission
├── receipt.php               # Generate PDF receipts
├── db.php                    # Database connection
├── db.sql                    # Database schema
├── admin/
│   ├── index.php            # Admin login page
│   ├── login_process.php    # Login processing
│   ├── dashboard.php        # Admin dashboard
│   ├── all_donations.php    # All donations view
│   └── logout.php           # Logout functionality
├── lib/
│   ├── fpdf/
│   │   └── fpdf.php         # PDF generation library
│   ├── fpdi/
│   │   └── fpdi.php         # PDF import library
│   └── marathi_converter.php # Marathi number conversion
└── templates/
    └── receipt_template.pdf  # PDF receipt template
```

## Database Schema

### `donations` table:
- `id` - Primary key
- `receipt_number` - Unique receipt identifier (GD2024XXXXXX)
- `name` - Donor name
- `flat_number` - Flat/apartment number
- `amount` - Donation amount
- `amount_in_words` - Amount in Marathi words
- `payment_mode` - Cash or UPI
- `date_created` - Timestamp

### `admins` table:
- `id` - Primary key
- `username` - Admin username
- `password` - Hashed password
- `full_name` - Admin display name
- `created_at` - Account creation timestamp

## Usage

### For Donors:
1. Visit the main page
2. Fill in personal details and donation amount
3. Select payment mode (Cash/UPI)
4. Submit form
5. Download generated PDF receipt

### For Admins:
1. Login via `/admin/` with credentials
2. View dashboard statistics
3. Browse all donations with search/filter
4. Download individual receipts
5. Monitor real-time donation data

## Features in Detail

### Multilingual Support
- Interface in Hindi/Marathi and English
- Marathi number conversion: `1500` → `एक हजार पाचशे रुपये मात्र`
- Cultural design elements

### Security Features
- Input validation and sanitization
- SQL injection prevention with PDO
- XSS protection
- Session-based admin authentication
- Password hashing with PHP's `password_hash()`

### PDF Generation
- Custom FPDF implementation
- Template-based receipts
- Proper formatting for printing
- Marathi font support

### Admin Dashboard
- Real-time statistics
- Responsive design
- Search functionality
- Pagination for large datasets
- Export capabilities

## Customization

### Adding New Fields
1. Update database schema in `db.sql`
2. Modify form in `index.php`
3. Update processing in `save.php`
4. Adjust PDF layout in `receipt.php`

### Styling Changes
- Modify Tailwind classes in HTML files
- Update color scheme (currently orange-themed)
- Customize fonts and layouts

### Payment Integration
- Extend payment modes
- Add UPI QR code generation
- Integrate payment gateways

## Troubleshooting

### Common Issues:

1. **Database Connection Error**:
   - Check credentials in `db.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **PDF Generation Error**:
   - Check file permissions
   - Ensure `templates/` directory is writable
   - Verify FPDF library is properly included

3. **Marathi Text Issues**:
   - Ensure UTF-8 encoding in database
   - Check browser character encoding
   - Verify font support

4. **Admin Login Issues**:
   - Check default credentials
   - Verify sessions are enabled
   - Ensure database connection

## Contributing

This system can be extended with:
- SMS notifications
- Email receipts
- Bulk import/export
- Advanced reporting
- Mobile app integration
- Online payment gateway

## Technical Stack

- **Backend**: PHP 8.3+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **PDF**: Custom FPDF implementation
- **Fonts**: Khand (Google Fonts) for Marathi support
- **Security**: PDO, password hashing, input validation

## License

This project is created for community use in Ganpati celebrations and society management.

---

🙏 **गणपती बाप्पा मोरया, मंगलमूर्ती मोरया** 🙏