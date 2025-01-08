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
- **URL**: `/api/trips/store`
- **Method**: `POST`
- **Description**: Create a new trip.
- **Request Headers**:
  - `Authorization` (string, required): Bearer token.
- **Request Parameters**:
  - `destination` (string, required): The destination of the trip.
  - `start_time` (string, required): The start time of the trip.
  - `end_time` (string, required): The end time of the trip.
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

##### Get Trips by User
- **URL**: `/api/auth/trips/user/{userId}`
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
 
### Notes
- All endpoints that require authentication must include the `Authorization` header with a valid Bearer token.
- Replace `{email}`, `{userId}`, and `{driver_id}` with the actual values when making requests.

YOU CAN IGNORE ANY URL THAT HAS TESTING
