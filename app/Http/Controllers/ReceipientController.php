<?php

namespace App\Http\Controllers;

use App\Models\individualRecipient;
use App\Models\individualSchedularRecipient;
use App\Models\InstallmentProgram;
use App\Models\Program;
use App\Models\Receipient;
use App\Models\RefBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $user = Auth::user();

        Log::info($user);

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
                    'reason_to_reject' => '-'
                ]);
            });
    
            $user = $request->user();
            $user->log(Receipient::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function bulkRejectRecommendation(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');

            $receipientToUpdate = Receipient::whereIn('id', $checkedIDs)->get();
            $reason_to_reject =  $request->text;
            $receipientToUpdate->each(function ($receipient) use ($reason_to_reject) {
                $receipient->update([
                    'status_id' => Receipient::STATUS_REJECT,
                    'reason_to_reject' => $reason_to_reject
                ]);
            });
    
            $user = $request->user();
            $user->log(Receipient::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function singleRecommendation(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');

            $receipient = Receipient::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'status_id' => Receipient::STATUS_RECOMMENDED,
                'reason_to_reject' => '-'
            ]);

            $user = $request->user();
            $user->log(Receipient::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing Receipient'], 500);
        }
    }

    public function singleRejectSubmitted(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');
            $receipient = Receipient::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'status_id' => Receipient::STATUS_REJECT,
                'reason_to_reject' =>  $request->text
            ]);

            $user = $request->user();
            $user->log(Receipient::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing Receipient'], 500);
        }
    }

    public function singleApprove(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');

            $receipient = Receipient::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'status_id' => Receipient::STATUS_APPROVE,
                'reason_to_reject' => '-'
            ]);

            $user = $request->user();
            $user->log(Receipient::ACTIVITY_APPROVED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully approve'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error approve Receipient'], 500);
        }
    }

    public function singleRejectApproval(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');

            $receipient = Receipient::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'status_id' => Receipient::STATUS_REJECT,
                'reason_to_reject' => $request->text
            ]);

            $user = $request->user();
            $user->log(Receipient::ACTIVITY_APPROVED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully approve'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error approve Receipient'], 500);
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
                    'reason_to_reject' => '-'
                ]);
            });
    
            $user = $request->user();
            $user->log(Receipient::ACTIVITY_APPROVED, "App\Models\Receipient");
            
            return response()->json(['message' => 'Receipient successfully approved'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function bulkRejectApproval(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');

            $receipientToUpdate = Receipient::whereIn('id', $checkedIDs)->get();
            $reason_to_reject =  $request->text;
            $receipientToUpdate->each(function ($receipient) use ($reason_to_reject) {
                $receipient->update([
                    'status_id' => Receipient::STATUS_REJECT,
                    'reason_to_reject' => $reason_to_reject
                ]);
            });
    
            $user = $request->user();
            $user->log(Receipient::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
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
        $receipientID = $receipient->id;

        if ($request->program_type_id == 1) {
            $individualRecipient = new IndividualRecipient();
            $individualRecipient->recipient_id = $receipientID;
            $individualRecipient->program_id = $request->program_id;
            $individualRecipient->disburse_amount = $request->disburse_amount;
            $individualRecipient->frequency_id = $request->frequency_id;
            $individualRecipient->payment_date = $request->payment_date;
    
            if ($request->frequency_id == 2) {
                $individualRecipient->total_month = $request->total_month;
            } elseif ($request->frequency_id == 3) {
                $individualRecipient->total_year = $request->total_year;
            } elseif ($request->frequency_id == 4) {
                $individualRecipient->save();
    
                $programId = $individualRecipient->program_id;
                foreach ($request->dynamicInputValue as $dynamicInput) {
                    $dynamicValue = new IndividualSchedularRecipient();
                    $dynamicValue->recipient_id = $receipientID;
                    $dynamicValue->program_id = $programId;
                    $dynamicValue->payment_date = $dynamicInput['payment_date'];
                    $dynamicValue->save();
                }
    
                return response()->json([
                    'message' => 'Recipient Created Successfully',
                    'code'    => 200
                ]);
            }
    
            $individualRecipient->save();
        }

        $user = $request->user();
        $user->log(Receipient::ACTIVITY_CREATED, "App\Models\Receipient");

        return response()->json([
            'recipient' =>  $receipient,
            'message' => 'Receipient Created Successfully',
            'code'    => 200
        ]);
    }

    public function program($id)
    {
        $program = Program::with('type', 'bankPanel', 'bankPanel.bank', 'frequency', 'installmentPrograms')->find($id);
    
        if (!$program) {
            return response()->json(['message' => 'Program not found'], 404);
        }

        $installmentPrograms = InstallmentProgram::where('program_id', $id)->get();

        return response()->json([
            'program' => $program,
            'message' => 'Program',
            'code' => 200,
        ]);
    }

    public function destroy($id, Request $request)
    {
        $receipient = Receipient::find($id);
        if($receipient) {
            $receipient->delete();

            $user = $request->user();
            $user->log(Receipient::ACTIVITY_DELETED, "App\Models\Receipient");

            return response()->json([
                'message' => 'Receipient Deleted Successfully',
                'code'    => 200
            ]);
        } else {
            return response()->json([
                'message' => "Receipient with id:$id does not exist"
            ]);
        }
    }

    public function show($id)
    {
        $receipient = Receipient::with('program', 'program.type', 'program.frequency', 'program.installmentPrograms', 'individualRecipient', 'individualRecipient.frequency', 'individualRecipient.recipient', 'individualRecipient.schedular')->find($id);
    
        if (!$receipient) {

        return response()->json(['message' => 'Program not found'], 404);
        }

        // $installmentPrograms = InstallmentProgram::where('program_id', $id)->get();

        return response()->json([
            'receipient' => $receipient,
            // 'installmentPrograms' => $installmentPrograms,
        ]);
    }
}