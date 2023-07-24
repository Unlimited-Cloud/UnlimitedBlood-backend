<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DonorController
{
    public function login(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        $password = $request->input('password');
        try {
            $user = Donor::where('phoneNumber', $phoneNumber)->first();
            $totalDonations = DB::table('donations')->where('phoneNumber', $user->phoneNumber)->count();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            if (!Hash::check($password, $user->password)) {
                return response()->json(['error' => 'Wrong password'], 401);
            }

            $user->loginStatus = true;
            $user->save();

            return response()->json([
                'success' => true, 'fname' => $user['fname'], 'mname' => $user['mname'], 'lname' => $user['lname'],
                'bloodGroup' => $user['bloodGroup'], 'birthDate' => $user['birthDate'], 'gender' => $user['gender'],
                'email' => $user['email'], 'address' => $user['address'], 'totalDonations' => $totalDonations
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred', 'exception' => $e], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        try {
            $user = Donor::where('phoneNumber', $phoneNumber)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->loginStatus = false;
            $user->save();

            return response()->json(['success' => true]);
        } catch (Exception) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function editProfile(Request $request): JsonResponse
    {

        $phoneNumber = $request->input('phoneNumber');
        $fname = $request->input('fname');
        $mname = $request->input('mname');
        $lname = $request->input('lname');
        $bloodGroup = $request->input('bloodGroup');
        $birthDate = $request->input('birthDate');
        $email = $request->input('email');
        $address = $request->input('address');

        try {
            $user = Donor::where('phoneNumber', $phoneNumber)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $user->fname = $fname;
            $user->mname = $mname;
            $user->lname = $lname;
            $user->bloodGroup = $bloodGroup;
            $user->birthDate = $birthDate;
            $user->email = $email;
            $user->address = $address;
            $user->save();

            return response()->json(['success' => true]);

        } catch (Exception) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function leaderboard(): JsonResponse
    {

        try {
            $donationCounts = DB::table('donations')
                ->join('donors', 'donations.phoneNumber', '=', 'donors.phoneNumber')
                ->select('donors.fname', 'donors.mname', 'donors.lname', 'donations.phoneNumber',
                    DB::raw('COUNT(*) as count'), DB::raw('SUM(donations.quantity) as totalQuantity'))
                ->groupBy('donors.fname', 'donors.mname', 'donors.lname', 'donations.phoneNumber')
                ->get();

            $result = [];
            foreach ($donationCounts as $donation) {
                $count = $donation->count;
                $totalQuantity = $donation->totalQuantity;

                $result[] = [
                    'phoneNumber' => $donation->phoneNumber,
                    'fname' => $donation->fname,
                    'mname' => $donation->mname,
                    'lname' => $donation->lname,
                    'count' => $count,
                    'totalQuantity' => $totalQuantity,
                ];
            }

            usort($result, function ($a, $b) {
                return $b['count'] <=> $a['count'];
            });

            return response()->json($result);

        } catch (Exception $e) {

            return response()->json(['error' => $e], 500);
        }
    }

    public function sendRequest(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        $bloodGroup = $request->input('bloodGroup');
        $bloodType = $request->input('bloodType');
        $needByDate = $request->input('needByDate');
        $quantity = $request->input('quantity');
        $address = $request->input('address');

        try {
            DB::table('requests')->insert([
                'phoneNumber' => $phoneNumber,
                'bloodGroup' => $bloodGroup,
                'bloodType' => $bloodType,
                'needByDate' => $needByDate,
                'requestDate' => today(),
                'quantity' => $quantity,
                'address' => $address,
            ]);

            return response()->json(['success' => true]);

        } catch (Exception) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function getRequests(Request $request): JsonResponse
    {

        $phoneNumber = $request->input('phoneNumber');

        try {
            $requests = DB::table('requests')
                ->leftJoin('organizations', 'requests.fulfilled_by', '=', 'organizations.id')
                ->where('requests.phoneNumber', $phoneNumber)
                ->select('requests.requestDate', 'requests.bloodGroup', 'requests.bloodType',
                    'requests.needByDate', 'requests.quantity', 'requests.address',
                    'organizations.name as fulfilled_by')
                ->orderByDesc('requests.requestDate')
                ->get();

            return response()->json($requests);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function getDonations(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');

        try {
            $allDonations = DB::table('donations')
                ->leftJoin('organizations', 'donations.organizationId', '=', 'organizations.id')
                ->where('donations.phoneNumber', $phoneNumber)
                ->select('donations.donationDate', 'donations.bloodType',
                    'donations.quantity', 'donations.upperBP', 'donations.lowerBP',
                    'organizations.name as organizationId')
                ->orderByDesc('donations.donationDate')
                ->get();

            $donations = $allDonations;

            $totalDonations = $donations->count();

            $responseData = [
                'donations' => $allDonations,
                'totalDonations' => $totalDonations,

            ];

            return response()->json($responseData);

        } catch (Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function sendSms(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        $message = $request->input('message');

        try {
            $http = new Client();
            $payloads = [
                "app" => "ws",
                "u" => "lalit",
                "h" => "9e9e5b1984cae182f35f296f82b7d5b8",
                "op" => "pv",
                "to" => "977" . $phoneNumber,
                "msg" => "This your OTP from UnlimitedBlood: " . $message
            ];

            $response = $http->get('http://unlimitedsms.net/playsms/index.php?' . http_build_query($payloads));
            // $response = $http->get('http://sms.unlimitedremit.com/index.php'. http_build_query($payloads));
            $body = $response->getBody();
            $result = json_decode($body->getContents(), 1);

            DB::table('sms')->insert([
                'phoneNumber' => $phoneNumber,
                'otp' => $message,
                'created_at' => now()
            ]);

            return response()->json(['data' => $result]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = json_decode((string)$e->getResponse()->getBody(), 1);
                return response()->json(['status' => $e->getResponse()->getStatusCode(), 'error' => $response]);
            }
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }

    }

    public function verifyPhoneNumber(Request $request): JsonResponse
    {

        $phoneNumber = $request->input('phoneNumber');
        $message = $request->input('message');

        try {
            $code = DB::table('sms')
                ->where('sms.phoneNumber', $phoneNumber)
                ->orderByDesc('sms.created_at')->select('sms.otp')->first();

            if ($code && $code->otp == $message) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = json_decode((string)$e->getResponse()->getBody(), 1);
                return response()->json(['status' => $e->getResponse()->getStatusCode(), 'error' => $response]);
            }
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function register(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');
        $email = $request->input('email');
        $bloodGroup = $request->input('bloodGroup');
        $fname = $request->input('fname');
        $mname = $request->input('mname');
        $lname = $request->input('lname');
        $password = bcrypt($request->input('password'));
        $address = $request->input('address');
        $gender = $request->input('gender');
        $birthDate = $request->input('birthDate');
        $diabetes = $request->input('diabetes');

        try {

            DB::table('users')->insert([
                'phoneNumber' => $phoneNumber,
                'name' => $fname,
                'password' => $password,
                'created_at' => now()

            ]);
            $user = DB::table('users')->where('users.phoneNumber', $phoneNumber)->first();
            DB::table('donors')->insert([
                'phoneNumber' => $phoneNumber,
                'email' => $email,
                'bloodGroup' => $bloodGroup,
                'fname' => $fname,
                'mname' => $mname,
                'lname' => $lname,
                'gender' => $gender,
                'birthDate' => $birthDate,
                'password' => $password,
                'address' => $address,
                'diabetes' => $diabetes,
                'user_id' => $user->id,
                'phoneVerified' => true,
                'created_at' => now()
            ]);

            return response()->json(['success' => true]);

        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

}
