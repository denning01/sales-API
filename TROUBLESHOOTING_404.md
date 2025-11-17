# Troubleshooting 404 Error

## The Problem
You're getting a 404 error when trying to access `/sales` endpoint.

## Solutions

### Solution 1: Check Your Base URL

The most common issue is using the wrong base URL. Try these:

**If using built-in PHP server:**
```bash
# Start server from the web directory
cd web
php -S localhost:8080
```
Then use: `http://localhost:8080/sales`

**If using Apache/Nginx:**
- Try: `http://localhost/sales`
- Or: `http://localhost/web/sales`
- Or: `http://localhost/sales-api/web/sales`

**If pretty URLs don't work:**
- Try: `http://localhost/web/index.php/sales`

---

### Solution 2: Test with index.php in URL

If pretty URLs aren't working, test with the full path:

**Register:**
```
POST http://localhost/web/index.php/auths/register
```

**Login:**
```
POST http://localhost/web/index.php/auths/login
```

**Get Sales:**
```
GET http://localhost/web/index.php/sales
```

---

### Solution 3: Verify Server is Running

Make sure your web server is actually running:

**For built-in PHP server:**
```bash
cd /home/denning/Desktop/Practice\ Yii/sales/sales-api/web
php -S localhost:8080
```

**Check if it's working:**
```bash
curl http://localhost:8080
```

---

### Solution 4: Check .htaccess (Apache only)

If using Apache, make sure `.htaccess` file exists in the `web/` directory and mod_rewrite is enabled.

---

### Solution 5: Test a Simple Endpoint First

Try accessing the root first:
```
GET http://localhost:8080/
```

If that works, then try:
```
GET http://localhost:8080/sales
```

---

## Quick Test Commands

### Test 1: Check if server responds
```bash
curl http://localhost:8080/
```

### Test 2: Test sales endpoint
```bash
curl http://localhost:8080/sales
```

### Test 3: Test with index.php
```bash
curl http://localhost:8080/index.php/sales
```

---

## Correct Endpoints Based on Your Setup

### If server is in `/web/` directory:
- Base URL: `http://localhost:8080`
- Endpoints:
  - `http://localhost:8080/auths/register`
  - `http://localhost:8080/auths/login`
  - `http://localhost:8080/sales`

### If accessing via full path:
- Base URL: `http://localhost/web` or `http://localhost/sales-api/web`
- Endpoints:
  - `http://localhost/web/auths/register`
  - `http://localhost/web/auths/login`
  - `http://localhost/web/sales`

### If pretty URLs don't work:
- Base URL: `http://localhost/web/index.php`
- Endpoints:
  - `http://localhost/web/index.php/auths/register`
  - `http://localhost/web/index.php/auths/login`
  - `http://localhost/web/index.php/sales`

---

## Still Not Working?

1. **Check your server logs** for any errors
2. **Verify the controller exists**: `controllers/SaleController.php`
3. **Check URL manager config** in `config/web.php`
4. **Try accessing** `http://localhost:8080/index.php` first to see if Yii is working


