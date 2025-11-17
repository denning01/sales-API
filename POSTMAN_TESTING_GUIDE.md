# Postman Testing Guide for Sales API

## Base URL
**IMPORTANT:** The base URL depends on how you're running the server:

### Option 1: Using Built-in PHP Server
```bash
cd web
php -S localhost:8080
```
Then use: `http://localhost:8080`

### Option 2: Using Apache/Nginx
- If document root is `/web/`: Use `http://localhost` or `http://localhost:8080`
- If accessing via full path: Use `http://localhost/web` or `http://localhost/sales-api/web`

### Option 3: If Pretty URLs Don't Work
Try: `http://localhost/web/index.php/sales` (with `index.php` in the path)

---

## Step-by-Step Testing Guide

### **STEP 1: Register a New User** (Optional - if you don't have an account)

**Request Type:** `POST`  
**Endpoint:** `http://localhost/auths/register`  
**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "username": "testuser",
  "email": "testuser@example.com",
  "password": "password123"
}
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "username": "testuser",
    "email": "testuser@example.com"
  }
}
```

---

### **STEP 2: Login to Get Access Token**

**Request Type:** `POST`  
**Endpoint:** `http://localhost/auths/login`  
**Headers:**
```
Content-Type: application/json
```

**Body (raw JSON):**
```json
{
  "username": "testuser",
  "password": "password123"
}
```

**Expected Response (200 OK):**
```json
{
  "status": "success",
  "access_token": "your_access_token_here",
  "user": {
    "id": 1,
    "username": "testuser",
    "email": "testuser@example.com"
  }
}
```

**⚠️ IMPORTANT:** Copy the `access_token` value - you'll need it for all authenticated requests!

---

### **STEP 3: Create a Sale** (Requires Authentication)

**Request Type:** `POST`  
**Endpoint:** `http://localhost/sales`  
**Headers:**
```
Content-Type: application/json
Authorization: Bearer YOUR_ACCESS_TOKEN_HERE
```

**Body (raw JSON):**
```json
{
  "item": "Laptop",
  "price": 999.99,
  "description": "High-performance laptop for work"
}
```

**Expected Response (201 Created):**
```json
{
  "id": 1,
  "user_id": 1,
  "item": "Laptop",
  "price": "999.99",
  "description": "High-performance laptop for work",
  "image": null,
  "created_at": 1702569600,
  "updated_at": 1702569600
}
```

**Note:** The `user_id` is automatically set from your token - you don't need to include it!

---

### **STEP 4: View All Sales** (No Authentication Required)

**Request Type:** `GET`  
**Endpoint:** `http://localhost/sales`  
**Headers:** None required

**Expected Response (200 OK):**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "item": "Laptop",
    "price": "999.99",
    "description": "High-performance laptop for work",
    "image": null,
    "created_at": 1702569600,
    "updated_at": 1702569600
  }
]
```

---

### **STEP 5: View a Specific Sale** (No Authentication Required)

**Request Type:** `GET`  
**Endpoint:** `http://localhost/sales/1`  
**Headers:** None required

**Expected Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "item": "Laptop",
  "price": "999.99",
  "description": "High-performance laptop for work",
  "image": null,
  "created_at": 1702569600,
  "updated_at": 1702569600
}
```

---

### **STEP 6: Update Your Own Sale** (Requires Authentication + Ownership)

**Request Type:** `PUT`  
**Endpoint:** `http://localhost/sales/1`  
**Headers:**
```
Content-Type: application/json
Authorization: Bearer YOUR_ACCESS_TOKEN_HERE
```

**Body (raw JSON):**
```json
{
  "item": "Updated Laptop",
  "price": 899.99,
  "description": "Updated description"
}
```

**Expected Response (200 OK):**
```json
{
  "id": 1,
  "user_id": 1,
  "item": "Updated Laptop",
  "price": "899.99",
  "description": "Updated description",
  "image": null,
  "created_at": 1702569600,
  "updated_at": 1702569700
}
```

---

### **STEP 7: Try to Update Someone Else's Sale** (Should Fail)

**Steps:**
1. Register/login as a **different user** (User 2)
2. Get User 2's access token
3. Try to update User 1's sale using User 2's token

**Request Type:** `PUT`  
**Endpoint:** `http://localhost/sales/1` (Sale created by User 1)  
**Headers:**
```
Content-Type: application/json
Authorization: Bearer USER_2_ACCESS_TOKEN_HERE
```

**Body (raw JSON):**
```json
{
  "item": "Hacked Item",
  "price": 0.01
}
```

**Expected Response (403 Forbidden):**
```json
{
  "name": "Forbidden",
  "message": "You can only edit your own sales",
  "code": 0,
  "status": 403
}
```

---

### **STEP 8: Delete Your Own Sale** (Requires Authentication + Ownership)

**Request Type:** `DELETE`  
**Endpoint:** `http://localhost/sales/1`  
**Headers:**
```
Authorization: Bearer YOUR_ACCESS_TOKEN_HERE
```

**Expected Response (200 OK):**
```json
{
  "success": true,
  "message": "Sale deleted successfully"
}
```

---

### **STEP 9: Try to Delete Without Token** (Should Fail)

**Request Type:** `DELETE`  
**Endpoint:** `http://localhost/sales/2`  
**Headers:** None

**Expected Response (401 Unauthorized):**
```json
{
  "name": "Unauthorized",
  "message": "Your request was made with invalid credentials.",
  "code": 0,
  "status": 401
}
```

---

## Quick Reference: All Endpoints

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| POST | `/auths/register` | No | Register new user |
| POST | `/auths/login` | No | Login and get token |
| GET | `/sales` | No | List all sales |
| GET | `/sales/{id}` | No | Get specific sale |
| POST | `/sales` | **Yes** | Create new sale |
| PUT | `/sales/{id}` | **Yes** | Update sale (only if you own it) |
| DELETE | `/sales/{id}` | **Yes** | Delete sale (only if you own it) |
| POST | `/sales/upload` | **Yes** | Upload image for new sale |
| POST | `/sales/{id}/upload` | **Yes** | Upload image for existing sale (only if you own it) |

---

## Postman Setup Tips

### 1. **Create Environment Variables**
   - Create a new environment in Postman
   - Add variable: `base_url` = `http://localhost`
   - Add variable: `access_token` = (leave empty, will be set after login)

### 2. **Use Environment Variables**
   - Use `{{base_url}}/sales` instead of hardcoding
   - Use `{{access_token}}` in Authorization header

### 3. **Auto-save Token After Login**
   - In the Login request, go to **Tests** tab
   - Add this script:
   ```javascript
   if (pm.response.code === 200) {
       var jsonData = pm.response.json();
       pm.environment.set("access_token", jsonData.access_token);
   }
   ```

### 4. **Set Authorization Header**
   - In request, go to **Authorization** tab
   - Type: **Bearer Token**
   - Token: `{{access_token}}`

---

## Testing Scenarios Checklist

- [ ] Register a new user
- [ ] Login and get token
- [ ] Create a sale (with token)
- [ ] View all sales (without token)
- [ ] View specific sale (without token)
- [ ] Update your own sale (should succeed)
- [ ] Try to update someone else's sale (should fail with 403)
- [ ] Delete your own sale (should succeed)
- [ ] Try to delete without token (should fail with 401)
- [ ] Try to create sale without token (should fail with 401)

---

## Common Issues

### Issue: "401 Unauthorized"
**Solution:** Make sure you're including the `Authorization: Bearer TOKEN` header

### Issue: "403 Forbidden" when updating/deleting
**Solution:** You can only edit/delete sales that you created. Make sure you're using the token of the user who created the sale.

### Issue: "422 Unprocessable Entity"
**Solution:** Check that all required fields are included (`item`, `price`, `user_id` is auto-set)

### Issue: "404 Not Found"
**Solution:** Check your base URL and endpoint path. Make sure your server is running.

---

## Sample Test Data

### User 1:
```json
{
  "username": "alice",
  "email": "alice@example.com",
  "password": "password123"
}
```

### User 2:
```json
{
  "username": "bob",
  "email": "bob@example.com",
  "password": "password123"
}
```

### Sale Examples:
```json
{
  "item": "iPhone 15",
  "price": 1299.99,
  "description": "Latest iPhone model"
}
```

```json
{
  "item": "Gaming Mouse",
  "price": 79.99,
  "description": "RGB gaming mouse with high DPI"
}
```

