# DentalEduHub - Dental Health Education Platform

![DentalEduHub](https://images.unsplash.com/photo-1606811841689-23dfddce3e95?w=1200&h=400&fit=crop)

## 📋 Overview

**DentalEduHub** is a comprehensive web-based educational platform dedicated to patient education in oral and dental health. The platform provides simplified, easy-to-understand educational content with an interactive community that helps patients share experiences and get answers to their questions.

### 🎯 Project Goals

Empower patients with better understanding of oral and dental health through:
- Reliable and simplified educational content
- Instructional video tutorials
- Supportive community for communication and experience sharing
- Quick answers to frequently asked questions

---

## ✨ Key Features

### 📚 Educational Content
- **Educational Articles**: Comprehensive written content on oral and dental health topics
- **Video Library**: Instructional videos for procedures and tips
- **Organized Categories**: Content divided by categories (Prevention, Treatment, Nutrition, etc.)
- **Advanced Search**: Ability to search and filter by category

### ❓ Frequently Asked Questions (FAQ)
- Comprehensive Q&A database
- Categorized by topics
- Answers from specialized dentists

### 💬 Interactive Forum
- Active patient community
- Ask questions and share experiences
- Responses from verified doctors
- Ability to pin and lock important topics

### 🤖 AI Assistant
- Smart assistant to answer questions
- Available 24/7
- Powered by Gemini AI technology

### 👥 User System
- **Patients**: Access content and participate in forum
- **Doctors**: Create content and answer questions
- **Admins**: Complete platform management

### 🎨 Modern Design
- Modern and user-friendly interface
- Responsive design for all devices
- Glassmorphism effects
- "Slate & Sky" color scheme

---

## 🛠️ Technologies Used

### Backend
- **PHP 8.2+**: Primary programming language
- **MySQL/MariaDB**: Database
- **PDO**: Secure database handling

### Frontend
- **HTML5 & CSS3**: Page structure and styling
- **Bootstrap 5**: CSS framework
- **JavaScript**: Interactivity
- **Google Fonts**: Outfit and Inter fonts

### Additional Tools
- **CKEditor 5**: Rich text editor
- **Bootstrap Icons**: Icons
- **Gemini AI API**: Smart assistant

---

## 📦 Requirements

- **Web Server**: Apache/Nginx
- **PHP**: Version 8.0 or higher
- **MySQL/MariaDB**: Version 5.7 or higher
- **XAMPP/WAMP/LAMP**: For local development

---

## 📖 Usage Scenarios

### Scenario 1: Patient Searching for Dental Care Information

**Persona**: Ahmed, 28 years old, suffering from tooth sensitivity

**Steps:**

1. **Platform Access**
   - Ahmed visits DentalEduHub website
   - Browses homepage and views featured articles

2. **Information Search**
   - Uses search bar to look for "tooth sensitivity"
   - Finds article titled "Complete Guide to Proper Tooth Brushing"
   - Reads article and watches instructional video

3. **Community Interaction**
   - Registers new account as patient
   - Goes to forum and searches for "best toothpaste for sensitive teeth" topic
   - Reads other patients' experiences
   - Gets verified response from dentist

4. **AI Assistant Usage**
   - Uses AI assistant to ask quick question
   - Gets instant answer about how to use dental floss

**Outcome**: Ahmed obtained comprehensive and reliable information, and felt confident in dealing with his problem.

---

### Scenario 2: Dentist Sharing Expertise

**Persona**: Dr. Sarah, dentist specialized in preventive dentistry

**Steps:**

1. **Login**
   - Logs in with doctor account
   - Goes to dashboard

2. **Create Educational Content**
   - Creates new article titled "Cavity Prevention: Essential Tips"
   - Uses CKEditor to format content
   - Adds illustrative images
   - Selects "Prevention" category
   - Publishes article

3. **Upload Educational Video**
   - Adds YouTube video about "Proper Brushing Technique"
   - Writes comprehensive description
   - Sets duration and category

4. **Patient Interaction**
   - Browses forum
   - Finds patient question about "pain after dental filling"
   - Writes detailed and verified response
   - Response gets "verified" badge

5. **Add FAQs**
   - Adds new question in FAQ section
   - "How often should I visit the dentist?"
   - Writes comprehensive answer

**Outcome**: Dr. Sarah contributed to patient education and built her professional reputation.

---

### Scenario 3: Administrator Managing Platform

**Persona**: Khaled, platform administrator

**Steps:**

1. **Dashboard Review**
   - Logs in as admin
   - Views platform statistics:
     - Users: 150
     - Articles: 45
     - Videos: 30
     - Forum Topics: 89

2. **User Management**
   - Goes to "User Management" page
   - Reviews new doctor's join request
   - Changes user role from "patient" to "doctor"
   - Sends approval email

3. **Content Management**
   - Reviews new articles
   - Edits article containing outdated information
   - Deletes inappropriate video
   - Organizes categories and adds new category

4. **Forum Management**
   - Browses recent topics
   - Pins important topic about "How to Handle Dental Emergencies"
   - Locks finished discussion topic
   - Deletes inappropriate reply

5. **FAQ Management**
   - Reviews FAQs
   - Reorders questions by importance
   - Updates old answer
   - Publishes new questions

6. **Performance Monitoring**
   - Reviews most viewed articles
   - Analyzes user activity
   - Plans new content campaign

**Outcome**: Khaled maintains content quality and ensures safe and beneficial environment for users.

---

## 📁 Project Structure

```
DentalEduHub/
├── admin/                  # Admin Dashboard
│   ├── dashboard.php      # Main Dashboard
│   ├── users.php          # User Management
│   ├── articles.php       # Article Management
│   ├── videos.php         # Video Management
│   ├── categories.php     # Category Management
│   ├── forum.php          # Forum Management
│   └── faqs.php           # FAQ Management
├── assets/                # Static Files
│   ├── css/
│   │   └── custom.css     # Custom Styles
│   ├── js/
│   └── images/
├── config/                # Configuration Files
│   └── config.php         # Main Settings
├── database/              # Database
│   └── test_data.sql      # Test Data
├── includes/              # Shared Files
│   ├── header.php         # Page Header
│   ├── footer.php         # Page Footer
│   └── functions.php      # Helper Functions
├── uploads/               # Uploaded Files
├── index.php              # Homepage
├── articles.php           # Articles Page
├── article.php            # Single Article View
├── videos.php             # Videos Page
├── video.php              # Single Video View
├── forum.php              # Forum Page
├── forum-topic.php        # Topic View
├── faq.php                # FAQs
├── ai-assistant.php       # AI Assistant
├── login.php              # Login
├── register.php           # Registration
├── profile.php            # User Profile
└── patient_education.sql  # Database Structure
```

---

## 🗄️ Database

### Main Tables

| Table | Description |
|-------|-------------|
| `users` | User information (patients, doctors, admins) |
| `categories` | Content categories |
| `articles` | Educational articles |
| `videos` | Video content |
| `faqs` | Frequently asked questions |
| `forum_topics` | Forum topics |
| `forum_replies` | Topic replies |

### Relationships

- Each article/video/FAQ belongs to one category
- Each content has an author (user)
- Each forum topic has multiple replies
- Replies can be verified by doctors

---

## 👥 User Roles

### 1. Patient
**Permissions:**
- ✅ Read articles and videos
- ✅ Browse FAQs
- ✅ Create forum topics
- ✅ Reply to topics
- ✅ Use AI assistant
- ❌ Create educational content
- ❌ Access admin dashboard

### 2. Doctor
**Permissions:**
- ✅ All patient permissions
- ✅ Create and edit articles
- ✅ Add videos
- ✅ Add FAQs
- ✅ Verify forum replies
- ✅ Access some dashboard sections
- ❌ User management

### 3. Admin
**Permissions:**
- ✅ All permissions
- ✅ User management
- ✅ Category management
- ✅ Delete any content
- ✅ Pin and lock topics
- ✅ Full dashboard access

---

## 🎨 Design System

### Colors

```css
--primary-color: #0f172a    /* Slate Dark */
--secondary-color: #0ea5e9  /* Sky Blue */
--success-color: #10b981    /* Green */
--danger-color: #ef4444     /* Red */
--warning-color: #f59e0b    /* Amber */
```

### Typography

- **Headings**: Outfit (700)
- **Body Text**: Inter (400-600)

### Components

- **Glass Cards**: Transparent glass effect
- **Hover Effects**: Lift effects on hover
- **Gradients**: Modern color gradients
- **Shadows**: Soft shadows

---

## 🔒 Security

### Implemented Measures

- ✅ **Password Encryption**: Using `password_hash()`
- ✅ **SQL Injection Protection**: Using PDO Prepared Statements
- ✅ **XSS Protection**: Using `htmlspecialchars()`
- ✅ **Session Verification**: Session management
- ✅ **Permission Verification**: Role-based access control
- ✅ **Input Sanitization**: Input cleaning

### Additional Recommendations

- 🔐 Change default admin credentials
- 🔐 Use HTTPS in production
- 🔐 Regularly update PHP and MySQL
- 🔐 Perform regular backups

---

## 📱 Compatibility

### Supported Browsers

- ✅ Chrome/Edge (latest 2 versions)
- ✅ Firefox (latest 2 versions)
- ✅ Safari (latest 2 versions)
- ⚠️ Internet Explorer (not supported)

### Devices

- ✅ Desktop (1920x1080 and above)
- ✅ Laptop (1366x768 and above)
- ✅ Tablet (768x1024)
- ✅ Mobile (375x667 and above)

---

## 🚧 Future Development

### Planned Features

- [ ] Mobile App (iOS & Android)
- [ ] Advanced notification system
- [ ] Live chat with doctors
- [ ] Appointment booking system
- [ ] Personal health reports
- [ ] Multi-language support
- [ ] Dark Mode
- [ ] PWA (Progressive Web App)

---

## 🤝 Contributing

Contributions are welcome! If you'd like to contribute:

1. Fork the project
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

## 📞 Contact

- **Email**: support@dentaledu.com
- **Website**: https://dentaledu.com
- **GitHub**: https://github.com/yourusername/DentalEduHub

---

## 🙏 Acknowledgments

- **Bootstrap Team** - CSS Framework
- **CKEditor Team** - Text Editor
- **Google Fonts** - Typography
- **Unsplash** - Images
- **Gemini AI** - Smart Assistant

---

## 📊 Stats

![GitHub stars](https://img.shields.io/github/stars/yourusername/DentalEduHub?style=social)
![GitHub forks](https://img.shields.io/github/forks/yourusername/DentalEduHub?style=social)
![GitHub issues](https://img.shields.io/github/issues/yourusername/DentalEduHub)
![GitHub license](https://img.shields.io/github/license/yourusername/DentalEduHub)

---

<div align="center">
  <p>Made with ❤️ to improve oral and dental health</p>
  <p>© 2024 DentalEduHub. All rights reserved.</p>
</div>
