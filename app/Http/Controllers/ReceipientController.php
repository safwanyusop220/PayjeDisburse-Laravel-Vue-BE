<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Receipient;
use App\Models\RefBank;
use Illuminate\Http\Request;

class ReceipientController extends Controller
{
    public function index()
    {
        $receipients = Receipient::with('program', 'program.type', 'status')->orderBy('id', 'desc')->get();

        return response()->json([
            'receipients' => $receipients,
            'message'  => 'receipients',
            'code'     => 200,
        ]);
    }

    public function programs()
    {
        $programs = Program::with('type', 'status')->where('status_id', 3)->orderBy('id', 'desc')->get();

        return response()->json([
            'programs' => $programs,
            'message'  => 'Programs',
            'code'     => 200,
        ]);
    }

    public function banks()
    {
        $banks = RefBank::all();

        return response()->json([
            'banks' => $banks,
            'message'  => 'banks',
            'code'     => 200,
        ]);
    }
    
    public function recommendation()
    {
        $recommendations = Receipient::with('program', 'program.type', 'status')->where('status_id', '1')->orderBy('id', 'desc')->get();

        return response()->json([
            'recommendations' => $recommendations,
            'message'  => 'recommendations',
            'code'     => 200,
        ]);
    }

    public function endorse(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');

            $receipientToUpdate = Receipient::whereIn('id', $checkedIDs)->get();
    
            $receipientToUpdate->each(function ($receipient) {
                $receipient->update([
                    'status_id' => Receipient::STATUS_RECOMMENDED,
                    'reject_reason' => '-'
                ]);
            });
    
            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function approval()
    {
        $approvals = Receipient::with('program', 'program.type', 'status')->where('status_id', '2')->orderBy('id', 'desc')->get();

        return response()->json([
            'approvals' => $approvals,
            'message'  => 'approvals',
            'code'     => 200,
        ]);
    }

    public function approve(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
    
            $receipientToUpdate = Receipient::whereIn('id', $checkedIDs)->get();
    
            $receipientToUpdate->each(function ($receipient) {
                $receipient->update([
                    'status_id' => Receipient::STATUS_APPROVE,
                    'reject_reason' => '-'
                ]);
            });
    
            return response()->json(['message' => 'Receipient successfully approved'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function store(Request $request)
    {
        $receipient = new Receipient();
        $receipient->name = $request->name;
        $receipient->identification_number = $request->identification_number;
        $receipient->address = $request->address;
        $receipient->postcode = $request->postcode; 
        $receipient->phone_number = $request->phone_number;
        $receipient->email = $request->email;
        $receipient->bank_id = $request->bank_id;
        $receipient->account_number = $request->account_number;
        $receipient->program_id = $request->program_id;
        $receipient->status_id = 1;
        $receipient->save();
        return response()->json([
            'message' => 'Receipient Created Successfully',
            'code'    => 200
        ]);
    }
}
