<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\role_permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login( Request $request) {
        //Validation of the request.
        $validator = Validator::make($request->all(), 
        [
            'email' => 'required|email',
            'password' => 'required|string',
        ],
        [
            'email.required' => 'A email is required',
            'email.email' => 'Email must be a valid email address',
            'password.required' => 'Password is required',
        ]);

        // If the request fails the validation so return an error.
        if ($validator->fails()) {
            return response()->json( [ 'error' => $validator->errors() ],  Response::HTTP_OK);
        }

        // Check if the user exists.
        $user_exists = User::where('email', htmlspecialchars( $request->input('email') ) )->first();
        if ( ! $user_exists ) {
            return response()->json( [ 'error' => 'The user does not exist.' ],  Response::HTTP_OK);
        }

        // Check if the user exists.
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user and generate a token.
        if ( ! $token = JWTAuth::attempt( $credentials ) ) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_OK);
        }

        // Return the token and user details
        return response()->json([
            'token' => $token,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Return the user to te login
     */
    public function unauthenticated() {
        return redirect('/login');
    }

    /**
     * Registration of a new user.
     */
    public function register ( Request $request ) {
        // Validation of the request.
        $validator = Validator::make(request()->all(), 
            [
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'nullable|integer',
                'email' => 'required|string|email|max:255|unique:users',
            ],
            [
                'name.required' => 'A name is required',
                'password.required' => 'A password is required',
                'email.required' => 'A email is required',
                'email.email' => 'Email must be a valid email address',
                'email.unique' => 'Email already exists',
            ]
        );

        // Check if the data is valid.
        if ( $validator->fails() ) {
            return response()->json( [ 'error' => $validator->errors()->all() ],  Response::HTTP_OK);
        }

        // Check if the user exists.
        $user_exists = User::where('email', htmlspecialchars( $request->input('email') ) )->first();
        if ( $user_exists ) {
            return response()->json( [ 'error' => 'The user already exists, please try by using a different email' ],  Response::HTTP_OK);
        }

        // checking user role in the request.
        $user_role = htmlspecialchars( $request->input('role_id' ) );
        $user_role = $user_role == 0 ? 2 : $user_role; // Default role is 2 // Role ID 2 = Customer.

        // Create the user if it does not exist.
        if ( ! $user_exists ) {
            $user_created = User::create([
                'name' => htmlspecialchars( $request->input('name') ),
                'email' => htmlspecialchars( $request->input('email') ),
                'password' => Hash::make( htmlspecialchars( $request->input('password') ) ),
                'role_id' => $user_role,
            ]);

            // If the user is not created, return an error.
            if ( ! $user_created ) {
                return response()->json( [ 'error' => 'The user cant be created based on a server error' ],  Response::HTTP_INTERNAL_SERVER_ERROR );
            }

            // return the token.
            return response()->json( $user_created, Response::HTTP_CREATED );
        }
    }

    /**
     * Logout the user.
     */
    public function logout( Request $request ) {
        auth()->logout();

        // Destroy the token.
        try {
            $current_token = JWTAuth::getToken();

            // What to destroy?.
            if ( $current_token ) {
                JWTAuth::invalidate( $current_token );
            } else {
                return response()->json( [ 'error' => 'Token not found' ],  Response::HTTP_UNAUTHORIZED );
            }

        } catch ( TokenInvalidException $e ) {
            return response()->json( [ 'error' => 'Token is invalid' ],  Response::HTTP_UNAUTHORIZED );
        }

        // Return a success message.
        return response()->json( [ 'message' => 'Successfully logged out' ],  Response::HTTP_OK );
    }

    /**
     * Refresh the token.
     */
    public function refresh_token( Request $request ) {
        // Refresh the token.
        try {
            // Current token.
            $current_token = JWTAuth::getToken();

            // In case the token is not found.
            if ( ! $current_token ) {
                return response()->json( [ 'error' => 'Token not found' ],  Response::HTTP_BAD_REQUEST );
            }

            // Getting a new token based on the current token.
            $new_token = JWTAuth::refresh( $current_token );
            // return the new token.
            return response()->json( [ 'token' => $this->get_token_structured( $new_token ) ],  Response::HTTP_OK );
        } catch ( TokenInvalidException $e ) {
            return response()->json( [ 'error' => 'Token is invalid' ],  Response::HTTP_UNAUTHORIZED );
        }
    }

    /**
     * Get data from the current user logged in.
     */
    public function get_user() {
        return response()->json( auth()->user(), Response::HTTP_OK );
    }

    public function get_user_permissions() {
        $user_info = auth()->user();

        // Check if the user is authenticated.
        if ( ! $user_info ) {
            return false;
        }
        
        // Check if the role exist.
        $check_role = Role::where('id', $user_info->role_id)->first();

        if ( ! $check_role ) {
            return false;
        }

        // Get roles permissions.
        $permissions = role_permission::where('role_id', $user_info->role_id)->get();
        $user_permissions = $permissions->toArray();

        if ( ! empty( $user_permissions ) ) {
            $user_permissions = array_column( $user_permissions, 'permission_id' );

            // return the permissions.
            return response()->json( $user_permissions, Response::HTTP_OK );
        } else {
            // return an empty array.
            return response()->json( [], Response::HTTP_OK );
        }

        return true;
    }

    /**
     * Get the token with a custom structure.
     */
    public function get_token_structured( $token ) {
        if ( ! $token ) {
            return response()->json( [ 'error' => 'Token not found' ],  Response::HTTP_BAD_REQUEST );
        }

        // Return the token with a custom structure.
        return response()->json(
            [
                'status_login' => 'success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 12 * 3600, // 12 hour to expire.
            ]
        );
    }
}
