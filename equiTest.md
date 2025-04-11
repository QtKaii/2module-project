# Equivalence Partitioning – User Registration

## Context

User registration system accepts :

- **Username**: 4–20 characters
- **Full Name**: Letters and spaces only, 2–40 characters
- **Email**: Valid email format
- **Date of Birth**: Valid format and user is old enough
- **Password**: 6–30 characters
- **Password Confirmation**: Must match password
- **Seller Toggle**: Optional checkbox

---

## Derived Equivalence Classes

### Username
- Username is between 4–20 characters (Valid)
- Username is shorter than 4 characters (Invalid)
- Username is longer than 20 characters (Invalid)

### Full Name
- Full name is letters and spaces only, 2–40 characters (Valid)
- Full name contains digits or special characters (Invalid)
- Full name is shorter than 2 or longer than 40 characters (Invalid)

### Email
- Email format is valid (Valid)
- Email format is invalid (Invalid)

### Date of Birth
- DOB is valid and user is at least 13 years old (Valid)
- DOB is valid but user is younger than 13 (Invalid)
- DOB is invalid format (Invalid)

### Password
- Password is between 6–30 characters (Valid)
- Password is shorter than 6 or longer than 30 characters (Invalid)

### Password Confirmation
- Password match (Valid)
- Password does not match (Invalid)

### Seller Toggle
- Seller toggle is 0 (Valid)
- Seller toggle is 0(Valid)

---

## Test Cases
