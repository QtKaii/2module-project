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

## Test Cases

### Test Case 1: All Valid Inputs

**Inputs:**
- Username: `user01`
- Full Name: `Kurt Mads`
- Password: `aaaaaa`
- DOB: `2000-05-20`

**Expected Result:**  
Registration successful.

---

### Test Case 2: Username Too Short

**Inputs:**
- Username: `u`
- Full Name: `Kurt Mads`
- Password: `aaaaaa`
- DOB: `2000-05-20`

**Expected Result:**  
Error – "Username should be 4 to 20 characters."

---

### Test Case 3: Full Name Has Numbers

**Inputs:**
- Username: `user01`
- Full Name: `1111213`
- Password: `aaaaaa`
- DOB: `2000-05-20`

**Expected Result:**  
Error – "Full name must contain only letters and spaces."

---

### Test Case 4: Password Too Short

**Inputs:**
- Username: `user01`
- Full Name: `Kurt Mads`
- Password: `a`
- DOB: `2000-05-20`

**Expected Result:**  
Error – "Password must be between 6 and 30 characters."

---

### Test Case 5: Underage User

**Inputs:**
- Username: `user01`
- Full Name: `Kurt Mads`
- Password: `aaaaaa`
- DOB: `2023-05-20`

**Expected Result:**  
Error – "You must be at least 13 years old to register."

---

### Test Case 6: Full Name Too Long

**Inputs:**
- Username: `user01`
- Full Name: `A name that is far too long and exceeds forty characters total`
- Password: `aaaaaa`
- DOB: `2000-05-20`

**Expected Result:**  
Error – "Full name must be 2 to 40 characters."

---

### Test Case 7: Username Too Long

**Inputs:**
- Username: `averyveryveryverylongusername`
- Full Name: `Kurt Mads`
- Password: `aaaaaa`
- DOB: `2000-05-20`

**Expected Result:**  
Error – "Username should be 4 to 20 characters."