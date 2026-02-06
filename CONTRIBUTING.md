# Contributing to Art Noir

Thank you for your interest in Art Noir! This project was developed as an academic assignment, but contributions and suggestions are welcome.

## 📋 How to Contribute

### Reporting Bugs

If you find a bug, please create an issue with:

- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Your environment (OS, PHP version, browser)

### Suggesting Enhancements

Feature requests are welcome! Please include:

- Clear description of the feature
- Use case / why it’s needed
- Possible implementation approach

### Pull Requests

1. Fork the repository
1. Create a feature branch: `git checkout -b feature/your-feature-name`
1. Make your changes
1. Test thoroughly
1. Commit with clear messages: `git commit -m "Add feature: description"`
1. Push to your fork: `git push origin feature/your-feature-name`
1. Create a Pull Request

## 🎨 Code Style Guidelines

### PHP

- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Comment complex logic
- Keep functions focused and small

### JavaScript

- Use ES6+ features where appropriate
- Keep AJAX calls in separate JS files
- Add error handling for all async operations
- Use consistent indentation (2 spaces)

### Database

- Use prepared statements (already implemented)
- Follow naming conventions (snake_case for tables/columns)
- Add appropriate indexes for performance

### HTML/CSS

- Semantic HTML5 elements
- Bootstrap classes for consistency
- Custom CSS in separate files
- Responsive design considerations

## 🧪 Testing

Before submitting:

- [ ] Test all CRUD operations
- [ ] Verify AJAX calls work without page reload
- [ ] Check admin and user views
- [ ] Test on different browsers
- [ ] Verify database operations
- [ ] Check for SQL injection vulnerabilities
- [ ] Test file uploads

## 📝 Commit Message Format

```
type: Brief description

Detailed explanation (if needed)

Examples:
- feat: Add artwork voting system
- fix: Resolve session timeout issue
- docs: Update installation guide
- style: Fix code formatting in dashboard.js
- refactor: Improve database connection handling
```

## 🔐 Security

- Never commit sensitive data (passwords, API keys)
- Always use prepared statements for SQL
- Validate and sanitize user input
- Implement proper session management
- Use HTTPS in production

## 📄 Documentation

When adding features:

- Update README.md if needed
- Add inline code comments
- Update INSTALLATION.md for setup changes
- Include examples in documentation

## ❓ Questions?

Feel free to open an issue for questions or clarifications.

## 📜 Academic Integrity

This project is an academic work. If you’re a student:

- Don’t copy this project for your own assignments
- Use it for learning and inspiration only
- Always cite sources and give credit

-----

**Note:** This is primarily an academic project. Contributions are reviewed as time permits.

Thank you for helping improve Art Noir! 🎨