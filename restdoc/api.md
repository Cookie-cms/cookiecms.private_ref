# **API Documentation**

### **Standard Response Format**

All responses from the API will follow the standard format:

```
{
    "error": bool,    // Indicates if there was an error (default is false)
    "msg": string,    // A message to describe the result or error
    "url": string,    // A URL for redirection, default is null
    "data": {}        // Data object containing the response data (can be empty)
}
```

---

## **Authentication (Auth)**

### **Login**
`POST /api/auth/login`

**Request Body**:
```
{
    "username": string,   // The username of the user
    "password": string    // The password of the user
}
```

**Responses**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Login successful",
    "url": string,  // Optional redirect URL
    "data": {
        "jwt": string  // The JWT token for authenticated requests
    }
}
```

- **401 Unauthorized**:
```
{
    "error": true,
    "msg": "Invalid credentials",
    "url": null,
    "data": {}
}
```

---

### **Register**
Steps
- Register mail
- Verify mail
- Finish mail


`POST /api/auth/register`

**Request Body**:
```
{
    "mail": string,       // User's email address
    "password": string    // Account password (min 8 characters)
}
```

**Responses**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Registration successful",
    "url": string,  // URL for the next step, if applicable
    "data": {}
}
```

- **400 Bad Request**:
```
{
    "error": true,
    "msg": "Invalid email or password",
    "url": null,
    "data": {}
}
```

- **409 Conflict**:
```
{
    "error": true,
    "msg": "Email already exists",
    "url": null,
    "data": {}
}
```

---

### **Confirm Email**
`GET /api/auth/confirm?token=<confirmation_token>`

**Responses**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Email confirmed successfully",
    "url": string,  // URL to redirect after confirmation
    "data": {}
}
```

- **400 Bad Request**:
```
{
    "error": true,
    "msg": "Invalid or expired token",
    "url": null,
    "data": {}
}
```

---

### **Finish Registration**
`POST /api/auth/registration/finish`

**Request Body**:
```
{
    "username": string  // Desired username
}
```

**Responses**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Username created successfully",
    "url": null,
    "data": {}
}
```

- **409 Conflict**:
```
{
    "error": true,
    "msg": "Username already taken",
    "url": null,
    "data": {}
}
```

---

### **Logout**
`POST /api/auth/logout`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Request Body**:
```
{
    "jwt": string   // The JWT token to be invalidated
}
```

**Response**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Logged out successfully",
    "url": null,
    "data": {}
}
```

- **401 Unauthorized**:
```
{
    "error": true,
    "msg": "Invalid JWT token",
    "url": null,
    "data": {}
}
```

---

## **Home**

### **Home Page**
`GET /api/home`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Response**:
```
{
    "error": false,
    "msg": "Home data fetched successfully",
    "url": null,
    "data": {
        "Username": "Coffee",               // The username of the user
        "Uuid": "00000000-0000-0000-0000-000000000000",  // Unique identifier for the user
        "Selected_Cape": 0,                 // ID of the selected cape
        "Capes": [
            {
                "Id": 0,
                "Name": "youtube"
            }
        ],                                  // Array of capes available to the user
        "Discord_integration": true,        // Whether the user has Discord integration enabled
        "Discord": {
            "Discord_Global_Name": "",     // The global Discord username
            "Discord_Ava": ""              // The Discord avatar URL
        },
        "Mail_verification": true           // Whether the email is verified
    }
}
```

---

### **Edit User Details**
`POST /api/home/edit`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Request Body Options**:

- **Update Username and Password**:
```
{
    "username": "new_username",     // New username
    "password": "current_password"  // Current password for verification
}
```

- **Change Password**:
```
{
    "password": "current_password", // Current password for verification
    "new_password": "new_password"  // New password
}
```

- **Change Cape**:
```
{
    "cape": 1  // Cape ID to select
}
```

- **Upload Skin**:
```
{
    "skin": "file"  // The file for the new skin (image file)
}
```

**Response**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Skin uploaded successfully",
    "url": null,
    "data": {}
}
```

- **401 Unauthorized**:
```
{
    "error": true,
    "msg": "Invalid password",
    "url": null,
    "data": {}
}
```

---

### **Verify Email**
`POST /api/auth/verify-email`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Request Body**:
```
{
    "verify_code": string  // The verification code received in the email
}
```

**Response**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Email verified successfully",
    "url": null,
    "data": {}
}
```

- **401 Unauthorized**:
```
{
    "error": true, 
    "msg": "Invalid or expired verification code",
    "url": null,
    "data": {}
}
```

---

## **Discord Integration**

### **Discord Integration Settings**
`POST /home/discord`

**Request Body**:
```
{
    "discord_integration": true | null,  // Enable or disable Discord integration
    "password": "string | null"          // Current password (only required if updating integration settings)
}
```

**Response**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Discord integration settings updated",
    "url": string,  // Discord integration URL to complete the setup
    "data": {}
}
```

- **400 Bad Request**:
```
{
    "error": true,  // error indicating an error
    "msg": "Invalid request or missing password",
    "url": null,
    "data": {}
}
```

---

## **Admin Endpoints**

### **Users List**
`GET /api/admin/users`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Response**:

- **200 OK**:
```
{
    "error": false,
    "msg": "Users fetched successfully",
    "url": null,
    "data": [
        {
            "username": string,   // Username of the user
            "uuid": string        // Unique identifier for the user
        }
    ]
}
```

- **403 Forbidden**:
```
{
    "error": true,
    "msg": "You do not have permission to view the user list",
    "url": null,
    "data": {}
}
```

---

### **Get Specific User**
`GET /api/admin/user/{identifier}`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Response**:

- **200 OK**:
```
{
    "error": false,
    "msg": "User details fetched successfully",
    "url": null,
    "data": {
        "username": string,                // Username of the user
        "uuid": string,                    // Unique identifier for the user
        "selected_cape": 0,                // ID of the currently selected cape
        "capes": [
            {
                "id": 0                      // Cape ID
            }
        ],                                   // List of capes associated with the user
        "discord": {
            "discord_id": "int"              // Discord ID of the user
        },
        "mail_verification": true,           // Email verification status
        "mail": string                     // User's email address
    }
}
```

- **403 Forbidden**:
```
{
    "error": true,
    "msg": "You do not have permission to access this user's details",
    "url": null,
    "

data": {}
}
```




#### **Send Mail to All Users**
`POST /api/admin/mail`

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Request Body**:
```
{
    "subject": string,       // Subject of the email
    "message": string        // The body content of the email
}
```

**Response**:

- **200 OK**:
```
{
    "msg": "Email sent successfully to all users"
}
```

- **403 Forbidden**:
```
{
    "error": true,
    "msg": "You do not have permission to send emails to all users"
}
```

### **Update User Role**

`POST /api/admin/user/role/{identifier}`

This endpoint allows an admin to update the role level (`role_level`) of a user and optionally set an expiration date (`expired_at`).

**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Request Body**:
```json
{
    "user": 1,
    "role_level": 1,        // New role level (1, 2, or 3)
    "expired_at": 1731798166    // Optional: Expiry date in ISO 8601 format (if not provided, role will be permanent)
}
```

**Responses**:

- **200 OK**:
```json
{
    "error": false,
    "msg": "User role and expiration updated successfully",
    "url": null,
    "data": {
        "id": 1,                  // User's unique identifier
        "new_role_level": 3,            // The updated role level
        "expired_at": 1731798166  // Expiry date if provided
    }
}
```

- **400 Bad Request**:
```json
{
    "error": true,
    "msg": "Invalid role level or expired_at must be a valid future date",
    "url": null,
    "data": {}
}
```

- **403 Forbidden**:
```json
{
    "error": true,
    "msg": "You do not have permission to update this user's role",
    "url": null,
    "data": {}
}
```

---

### **Optional Expiry Field**:

If the `expired_at` field is not provided, the role update will be permanent for the user. The expiry date must always be in the future if it is included.

---

### **Update User Role**

`POST /api/admin/user/cape/{identifier}`


**Request Headers**:
- **Authorization**: Bearer `jwt_token`

**Request Body**:
```json
{
    "user": 1,
    "cape": []
}
```

**Responses**:

- **200 OK**:
```json
{
    "error": false,
    "msg": "User role and expiration updated successfully",
    "url": null,
    "data": {
        "id": 1,                  // User's unique identifier
        "new_role_level": 3,            // The updated role level
        "expired_at": 1731798166  // Expiry date if provided
    }
}
```

- **400 Bad Request**:
```json
{
    "error": true,
    "msg": "Invalid role level or expired_at must be a valid future date",
    "url": null,
    "data": {}
}
```

- **403 Forbidden**:
```json
{
    "error": true,
    "msg": "You do not have permission to update this user's role",
    "url": null,
    "data": {}
}
```

---



## User permissions lvl


|                 | 1 lvl | 2 lvl | 3 lvl |
|-----------------|-------|-------|-------|
| change username | +     | +     | +     |
| Change skin     | +     | +     | +     |
| Change skin HD  | -     | +     | +     |
| Change mail     | +     | +     | +     |
| Change Password | +     | +     | +     |
| Use Discord     | +     | +     | +     |
|                 |       |       |       |
| **Admin**       |       |       |       |
| Users list      | -     | -     | +     |@
| User            | -     | -     | +     |
| Mail Spam       | -     | -     | +     |



## **Service Endpoints**

#### **Find user account**
`POST /api/service/user`

**Request Headers**:
- **Authorization**: Bearer `ServiceApiToken`

**Request Body**:
```
{
    "Discord_id": Int,       // Subject of the email
    "Playername": string        // The body content of the email
}
```

**Response**:

- **200 OK**:
```
{
    "discord": {
        "username": "",
        "id": ""
    },
    "account": {
        "username": ""
        "uuid": ""
    }
}
```

- **403 Forbidden**:
```
{
    "error": true,
    "msg": "You do not have permission to do this."
}
```

