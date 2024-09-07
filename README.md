Endpoints
1. Login
URL: /login
Method: POST
Description: Authenticates a user and returns a token.
2. Register
URL: /register
Method: POST
Description: Registers a new user and returns a token.
3. Logout
URL: /logout
Method: POST
Description: Logs out the user and invalidates the token.
Request Headers:
Authorization: Bearer <token>
4. Refresh Token
URL: /refresh
Method: POST
Description: Refreshes the authentication token.
Request Headers:
Authorization: Bearer <token>
5. User Profile
URL: /user-profile
Method: GET
Description: Retrieves the profile information of the authenticated user.
Request Headers:
Authorization: Bearer <token>
6. Google Authentication Redirect
URL: /auth/google/redirect
Method: GET
Description: Redirects the user to Google’s OAuth 2.0 server for authentication.
7. Google Authentication Callback
URL: /auth/google/callback
Method: GET
Description: Handles the callback from Google’s OAuth server and logs in the user.
8. Update User Profile
URL: /editUserProfile/{email}
Method: PUT
Description: Updates the user’s profile information based on their email.
Request Parameters:
email (path parameter): Email of the user to be updated.
9. Delete User Profile
URL: /deleteUserProfile/{email}
Method: DELETE
Description: Deletes the user’s profile based on their email.
Request Parameters:
email (path parameter): Email of the user to be deleted.
