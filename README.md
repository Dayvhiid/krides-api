### API Documentation

#### Authentication Endpoints

##### Register
- **URL**: `/api/auth/register`
- **Method**: `POST`
- **Description**: Register a new user.
- **Request Parameters**:
  - `name` (string, required): The name of the user.
  - `email` (string, required): The email of the user.
  - `password` (string, required): The password of the user.
- **Response Example**:
  ```json
  {
    "message": "User registered successfully",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "johndoe@example.com"
    },
    "token": "your_jwt_token"
  }
  ```

##### Login
- **URL**: `/api/auth/login`
- **Method**: `POST`
- **Description**: Login a user.
- **Request Parameters**:
  - `email` (string, required): The email of the user.
  - `password` (string, required): The password of the user.
- **Response Example**:
  ```json
  {
    "message": "User logged in successfully",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "johndoe@example.com"
    },
    "token": "your_jwt_token"
  }
  ```
  Please Note that you will need to pass in the access token with the JSON Data 

##### Logout
- **URL**: `/api/auth/logout`
- **Method**: `POST`
- **Description**: Logout the authenticated user.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "message": "User logged out successfully"
  }
  ```

##### Refresh Token
- **URL**: `/api/auth/refresh`
- **Method**: `POST`
- **Description**: Refresh the JWT token.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "token": "new_jwt_token"
  }
  ```

#### User Endpoints

##### Get User Profile
- **URL**: `/api/auth/user-profile`
- **Method**: `GET`
- **Description**: Get the profile of the authenticated user.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "id": 1,
    "name": "John Doe",
    "email": "johndoe@example.com"
  }
  ```

##### Update User Profile
- **URL**: `/api/auth/editUserProfile/{email}`
- **Method**: `PUT`
- **Description**: Update the profile of a user by email.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `name` (string, optional): The new name of the user.
  - `password` (string, optional): The new password of the user.
- **Response Example**:
  ```json
  {
    "message": "User profile updated successfully",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "johndoe@example.com"
    }
  }
  ```

##### Delete User Profile
- **URL**: `/api//auth/deleteUserProfile/{email}`
- **Method**: `DELETE`
- **Description**: Delete the profile of a user by email.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "message": "User profile deleted successfully"
  }
  ```

#### Trip Endpoints

##### Create Trip
- **URL**: `/api/auth/trips/create`
- **Method**: `POST`
- **Description**: Create a new trip.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `destination` (string, required): The destination of the trip.
  - `start_time` (string, required): The start time of the trip.
  - `number_of_passengers` (string, required).
  - `rider_name` (string, required): rider name.
- **Response Example**:
  ```json
  {
    "message": "Trip created successfully",
    "trip": {
      "id": 1,
      "destination": "New York",
      "start_time": "2023-10-01 10:00:00",
      "end_time": "2023-10-01 14:00:00"
    }
  }
  ```


  ```

##### Get All Trips
- **URL**: `/api/auth/trips`
- **Method**: `GET`
- **Description**: Get all trips.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "trips": [
      {
        "id": 1,
        "destination": "New York",
        "start_time": "2023-10-01 10:00:00",
        "end_time": "2023-10-01 14:00:00"
      },
      {
        "id": 2,
        "destination": "Los Angeles",
        "start_time": "2023-10-02 08:00:00",
        "end_time": "2023-10-02 12:00:00"
      }
    ]
  }
  ```This URL JUST DISPLAYS ALL THE TRIPS

##### Accept Trip
- **URL**: `/api/auth/trips/{id}/accept`
- **Method**: `PATCH`
- **Description**: Accept a trip by ID.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "message": "Trip accepted successfully",
    "trip": {
      "id": 1,
      "destination": "New York",
      "start_time": "2023-10-01 10:00:00",
      "end_time": "2023-10-01 14:00:00",
      "status": "accepted"
    }
  }
  ```

#### Rider Endpoints

##### Create Rider
- **URL**: `/api/auth/rider/store`
- **Method**: `POST`
- **Description**: Create a new rider.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `name` (string, required): The name of the rider.
  - `driver_id` (integer, required): The ID of the driver.
- **Response Example**:
  ```json
  {
    "message": "Rider created successfully",
    "rider": {
      "id": 1,
      "name": "Jane Doe",
      "driver_id": 1
    }
  }
  ```

##### Get Rider by ID
- **URL**: `/api/rider/{driver_id}`
- **Method**: `GET`
- **Description**: Get a rider by driver ID.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "rider": {
      "id": 1,
      "name": "Jane Doe",
      "driver_id": 1
    }
  }
  ```

 ##### Register Rider
- **URL**: `/api/auth/driver/register`
- **Method**: `POST`
- **Description**: Register/Sign In Driver.
- **Request Headers**:
  - fullname,phone,password,vehicle_id.
- **Response Example**:
  ```json
  {
    "rider": {
      "id": 1,
      "name": "Jane Doe",
      "driver_id": 1
    }
  } 


 ##### Login Rider
- **URL**: `/api/auth/driver/login`
- **Method**: `POST`
- **Description**: Login the driver.
- **Request Headers**:
  - phone,password.
- **Response Example**:
  ```json
  {
    "rider": {
      "id": 1,
      "name": "Jane Doe",
      "driver_id": 1
    }
  }

 ##### Update Profile For Driver
- **URL**: `/api/auth/driver/register`
- **Method**: `POST`
- **Description**: Update Profile Picture.
- **Request Headers**:
  - Access token as Auth Header,picture (Make Sure you pass in) Content Type: Multipart-form Data, 
- **Response Example**:
  ```json
  {
    "rider": {
      "id": 1,
      "name": "Jane Doe",
      "driver_id": 1
    }
  }    

### Google Authentication Endpoints

##### Google Login Callback
- **URL**: `/api/testing/login/google/callback`
- **Method**: `GET`
- **Description**: Handle Google login callback.
- **Response Example**:
  ```json
  {
    "message": "Google login successful",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "johndoe@example.com"
    },
    "token": "your_jwt_token"
  }
  ```
### New Registration Route 
- **URL**: `/api/auth/register-step-one`
- **Method**: `POST`
- **Description**: Handles the first part of the Sign Up.
- **Request Parameters**:
  - `email` (string, required): .
  - `password` (integer, required): .
  - `phone` (integer, required): .
- **Response Example**:
  ```json
  {
    "message": " login successful",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "johndoe@example.com"
    },
    "token": "your_jwt_token"
  }


### Phone Number Verification
- **URL**: `/api/auth/verify-phone`
- **Method**: `POST`
- **Description**: Handles the phone number .
- **Request Parameters**:
  - `user_id` .
  - `verification_code`.
- **Response Example**:
  ```json


 ### New Registration Route 
- **URL**: `/api/auth/ride/{driver_name}`
- **Method**: `GET`
- **Description**: Fetches Driver History by filtering with the name.
- **Response Example**:
  ```json
  {
    "success": " true",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "johndoe@example.com"
    },
    "token": "your_jwt_token"
  }


  
 

##### Get Trips by User
- **URL**: `/api/auth/trips/user_history`
- **Method**: `GET`
- **Description**: Get all trips for a specific user.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "trips": [
      {
        "id": 1,
        "destination": "New York",
        "start_time": "2023-10-01 10:00:00",
        "end_time": "2023-10-01 14:00:00"
      },
      {
        "id": 2,
        "destination": "Los Angeles",
        "start_time": "2023-10-02 08:00:00",
        "end_time": "2023-10-02 12:00:00"
      }
    ]
  } 

##### Get A List of driver pending rides
- **URL**: `/api/auth/driver/trips`
- **Method**: `GET`
- **Description**: Get A list of  pending  rides using status as a filter.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "trips": [
      {
        "success": true,
        "data": "trips resource",
       
      },
      {
        "success": false,
        "data": "No pending trips found",
      },
      
    ]
  }   
 


##### New Route to Accept rides
- **URL**: `/api/auth/trip/accept/{trip_id}`
- **Method**: `POST`
- **Description**: New Route to accept rides.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "trips": [
      {
        "success": true,
        "data": "trips resource (with appended driver details)",
      },      
    ]
  }   
  


##### Get Trip Info
- **URL**: `/api/auth/trips/{trip_id}`
- **Method**: `GET`
- **Description**: Get full information about a trip including driver details.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Response Example**:
  ```json
  {
    "trips": [
      {
        "success": true,
        "data": "trips resource (with appended driver details)",
      },      
    ]
  }   

##### Wallet Funding
- **URL**: `/api/auth/wallet/fund`
- **Method**: `POST`
- **Description**: Handles wallet funding.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `amount` .

- **Response Example**:
  Redirects to the flutterwave platform. 


##### Trip Payment
- **URL**: `/api/auth/trip/pay`
- **Method**: `POST`
- **Description**: Hanldes the payment of trips, the trip first has to have been accepted by a rider, and the method parameter is either "direct" or "wallet".
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `trip_id` .
   - `method` direct or wallet.

- **Response Example**:
    ```json
  {
    "transactions": [
      {
        "success": true,
        "data": "Trip paid via wallet (direct bank transfer + wallet updated",
      },      
    ]
  } 


##### Updating Driver details
- **URL**: `/api/auth/driver/bank-details`
- **Method**: `POST`
- **Description**: Handles updating bank detials. Note that the bank code is not required but the account_number and bank_name are neccessary 
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `bank_code` optional.
  - `bank_name` required.
  - `account_number` required.

- **Response Example**:
    ```json
  {
    "driver": [
      {
        "success": true,
        "data": "bank details updated succesfully",
      },      
    ]
  } 

##### Email Verification
- **URL**: `/api/email/verify`
- **Method**: `POST`
- **Description**: Handles Email Verification
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:

- **Response Example**:
    ```json
  {
    "driver": [
      {
        "success": true,
        "data": "Email Verfication Link Sent || Email verified already", 
      },      
    ]
  }

##### Password Reset
- **URL**: `/api/auth/forgot-password`
- **Method**: `POST`
- **Description**: Handles password rest
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
   - `email` required.
- **Response Example**:
    ```json
  {
    "driver": [
      {
        "success": true,
        "data": "Password reset Link sent", 
      },      
    ]
  }   

##### Google Oauth
- **URL**: `/api/auth/google`
- **Method**: `GET`
- **Description**: Handles Google Oauth
- **Response Example**:
    ```json
  {
    "user": [
      {
        "success": true,
        "data": "User Login Meta Data", 
      },      
    ]
  } 
  

## Method
`POST`
- **URL**: `/api/auth/update/profile-picture`
## Description
This endpoint allows authenticated users to update their profile picture. The uploaded image is validated, stored on the server, and the file path is saved in the user's `picture` field in the database.

---

## Request

### Headers
| Key            | Value                        | Description                          |
|----------------|------------------------------|--------------------------------------|
| `Authorization`| `Bearer <token>`             | Required. The Bearer token of the authenticated user. |
| `Content-Type` | `multipart/form-data`        | Required. The request must be sent as form data.       |

### Body (Form Data)
| Field    | Type   | Description                          | Required |
|----------|--------|--------------------------------------|----------|
| `picture`| File   | The image file to upload. Supported formats: `jpg`, `jpeg`, `png`, `gif`. Maximum file size: `10MB`. | Yes      |

---

## Response

### Success Response (200 OK)
```json
{
    "message": "Profile picture updated successfully!",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john.doe@example.com",
        "picture": "profile_pictures/filename.jpg", // Path to the uploaded picture
        "created_at": "2023-10-01T12:00:00.000000Z",
        "updated_at": "2023-10-05T15:30:00.000000Z"
    }
}






### Notes
- All endpoints that require authentication must include the `Authorization` header with a valid Bearer token.
- Replace `{email}`, `{userId}`, and `{driver_id}` with the actual values when making requests.

YOU CAN IGNORE ANY URL THAT HAS TESTING



