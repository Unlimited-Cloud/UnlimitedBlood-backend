<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use Exception;
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

    public function leaderboard(Request $request): JsonResponse
    {
        $phoneNumber = $request->input('phoneNumber');

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


}
