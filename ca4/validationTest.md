# Validation Test for Product Creation

## Test Cases

### Test Case 1: All Valid Inputs

**Inputs:**
- Product Name: `Sample product`
- Description: `This is a test product description.`
- Sale Type: `Fixed Price`
- Price: `100`
- Product Image: (Valid image file)

**Expected Result:**  
Product created successfully.

---

### Test Case 2: Product Name character limit out of bounds(3-30)

**Inputs:**
- Product Name: `po`(character limit)
- Description: `This is a test product description.`
- Sale Type: `Fixed Price`
- Price: `100`
- Product Image: (Valid image file)


**Expected Result:**  
Error – "Product Name should have 3-30 characters."

---

### Test Case 3: Description character limit out of bounds(10-100)

**Inputs:**
- Product Name: `Sample product`
- Description: `a`(character limit)
- Sale Type: `Fixed Price`
- Price: `100`
- Product Image: (Valid image file)


**Expected Result:**  
Error – "Description should have 10-100 characters."

---

### Test Case 4: Invalid Price (Over 1100)

**Inputs:**
- Product Name: `Sample product`
- Description: `This is a test product description.`
- Sale Type: `Fixed Price`
- Price: `2300`
- Product Image: (Valid image file)


**Expected Result:**  
Error – "Cannot sell items worth over 1100."

---

### Test Case 5: No image file

**Inputs:**
- Product Name: `Sample product`
- Description: `This is a test product description.`
- Sale Type: `Fixed Price`
- Price: `100`
- Product Image: (No image file path)


**Expected Result:**  
Error – "Need producr image to list product"

## Test Results

| Test Case | Pass/Fail |
|-----------|-----------|
| 1         |    Pass   |
| 2         |    Fail   |
| 3         |    Fail   |
| 4         |    Fail   |
| 5         |    Fail   |

