<?php

namespace App\Http\Controllers;

use App\Models\IndividualRecipient;
use App\Models\IndividualSchedularRecipient;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Receipient;
use App\Models\RecipientProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipientProgramController extends Controller
{
    public function store(Request $request)
    {
        $recipientProgram = RecipientProgram::create([
            'recipient_id' => $request->recipient_id,
            'created_by_id' => $request->created_by_id,
            'program_id'   => $request->program_id,
            'status_id'    => 1,
        ]);
        $individualRecipientData = [];

        if ($recipientProgram->program->type->id == 1) {
            $individualRecipientData = [
                'recipient_id' =>$request->recipient_id,
                'program_id' => $request->program_id,
                'disburse_amount' => $request->disburse_amount,
                'frequency_id' => $request->frequency_id,
                'payment_date' => $request->payment_date,
            ];

            if ($request->frequency_id == 2) {
                $individualRecipientData['total_month'] = $request->total_month;
                $individualRecipientData['end_date'] = $request->end_date;
            } 
            
            elseif ($request->frequency_id == 3) {
                $individualRecipientData['total_year'] = $request->total_year;
                $individualRecipientData['end_date'] = $request->end_date;
            }
        }

        if (!empty($individualRecipientData)) {
            $individualRecipient = IndividualRecipient::create($individualRecipientData);

            if($request->frequency_id == 4)
            {
                $programId = $individualRecipient->program_id;

                foreach ($request->dynamicInputValue as $dynamicInput) {
                    $dynamicInputRules = [
                        'payment_date' => 'required|date',
                    ];

                    IndividualSchedularRecipient::create([
                        'recipient_id' => $request->recipient_id,
                        'program_id' => $programId,
                        'payment_date' => $dynamicInput['payment_date'],
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Recipient program created successfully',
            'data' => $recipientProgram,
            'code' => 200,
        ]);
    }

    public function recipient($id, Request $request)
    {
        $recipient = Receipient::with([
            'bank', 
            'recipientProgram' => function ($query) {
                $query->orderBy('id', 'desc'); 
            },
            'recipientProgram.program', 
            'recipientProgram.status', 
            'recipientProgram.program.type'
        ])->find($id);
        return response()->json($recipient);
    }

    public function programs($id)
    {
        $programs = Program::with('type', 'status')->whereNotIn('id', function($query) use ($id) {
            $query->select('program_id')
                ->from('recipient_programs')
                ->where('recipient_id', $id);
            })->orderBy('id', 'desc')->get();
        return response()->json([
            'programs' => $programs,
            'message'  => 'Programs',
            'code'     => 200,
        ]);
    }

    public function recommendation()
    {
        $recommendations = RecipientProgram::with('recipient', 'program', 'program.type', 'status')->where('status_id', '1')->orderBy('id', 'desc')->get();

        return response()->json([
            'recommendations' => $recommendations,
            'message'  => 'recommendations',
            'code'     => 200,
        ]);
    }

    public function approval()
    {
        $approvals = RecipientProgram::with('recipient', 'program', 'program.type', 'status')->where('status_id', '2')->orderBy('id', 'desc')->get();

        return response()->json([
            'approvals' => $approvals,
            'message'  => 'approvals',
            'code'     => 200,
        ]);
    }

    public function bulkApproveRecommendation(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
            $recommend_by_id = $request->input('userId');

            $recommendDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $recommendDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $receipientToUpdate = RecipientProgram::whereIn('id', $checkedIDs)->get();
    
            $receipientToUpdate->each(function ($receipient) use ($recommend_by_id, $recommendDate) {
                $receipient->update([
                    'recommend_by_id' => $recommend_by_id,
                    'recommend_date'  => $recommendDate,
                    'status_id' => RecipientProgram::STATUS_RECOMMENDED,
                    'reason_to_reject' => '-'
                ]);
            });
    
            $user = $request->user();
            $user->log(RecipientProgram::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function bulkRejectRecommendation(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
            $rejected_by_id = $request->input('userId');

            $rejected_date = new \DateTime('now', new \DateTimeZone('UTC'));
            $rejected_date->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $receipientToUpdate = RecipientProgram::whereIn('id', $checkedIDs)->get();
            $reason_to_reject =  $request->text;
            $receipientToUpdate->each(function ($receipient) use ($reason_to_reject, $rejected_date, $rejected_by_id) {
                $receipient->update([
                    'rejected_by_id' => $rejected_by_id,
                    'rejected_date'  => $rejected_date,
                    'status_id' => RecipientProgram::STATUS_REJECT,
                    'reason_to_reject' => $reason_to_reject
                ]);
            });
    
            $user = $request->user();
            $user->log(RecipientProgram::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function show($id)
    {
        $recipientProgram = RecipientProgram::with('recipient', 'recipient.individualRecipient', 'recipient.individualRecipient.frequency', 'recipient.individualRecipient.recipient.schedular', 'program', 'program.installmentPrograms', 'program.type', 'program.frequency', 'status', 'created_by', 'recommend_by', 'approved_by', 'rejected_by')->find($id);
    
        if (!$recipientProgram) {

        return response()->json(['message' => 'Program not found'], 404);
        }

        return response()->json([
            'recipientProgram' => $recipientProgram,
        ]);
    }

    public function singleRecommendation(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');
            $recommend_by_id = $request->input('userId');

            $recommendDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $recommendDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $receipient = RecipientProgram::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'recommend_by_id' => $recommend_by_id,
                'recommend_date'  => $recommendDate,
                'status_id' => RecipientProgram::STATUS_RECOMMENDED,
                'reason_to_reject' => '-'
            ]);

            $user = $request->user();
            $user->log(RecipientProgram::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing Receipient'], 500);
        }
    }

    public function singleRejectSubmitted(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');
            $rejected_by_id = $request->input('userId');

            $rejected_date = new \DateTime('now', new \DateTimeZone('UTC'));
            $rejected_date->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $receipient = RecipientProgram::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'rejected_by_id' => $rejected_by_id,
                'rejected_date'  => $rejected_date,
                'status_id' => RecipientProgram::STATUS_REJECT,
                'reason_to_reject' =>  $request->text
            ]);

            $user = $request->user();
            $user->log(RecipientProgram::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing Receipient'], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
            $approved_by_id = $request->input('userId');

            $approvedDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $approvedDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));
    
            $receipientToUpdate = RecipientProgram::whereIn('id', $checkedIDs)->get();
    
            $receipientToUpdate->each(function ($receipient) use ($approved_by_id,$approvedDate){
                $receipient->update([
                    'approved_by_id' => $approved_by_id,
                    'approved_date'  => $approvedDate,
                    'status_id' => Receipient::STATUS_APPROVE,
                    'reason_to_reject' => '-'
                ]);
                $programId = $receipient->program_id;
                $payment = Payment::where('program_id', $programId)->first();

                if ($payment) {
                    $payment->increment('total_recipient');
                } else {
                }
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
            $rejected_by_id = $request->input('userId');

            $rejected_date = new \DateTime('now', new \DateTimeZone('UTC'));
            $rejected_date->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $receipientToUpdate = RecipientProgram::whereIn('id', $checkedIDs)->get();
            $reason_to_reject =  $request->text;
            $receipientToUpdate->each(function ($receipient) use ($reason_to_reject, $rejected_by_id, $rejected_date) {
                $receipient->update([
                    'rejected_by_id' => $rejected_by_id,
                    'rejected_date'  => $rejected_date,
                    'status_id' => RecipientProgram::STATUS_REJECT,
                    'reason_to_reject' => $reason_to_reject
                ]);
            });
    
            $user = $request->user();
            $user->log(RecipientProgram::ACTIVITY_RECOMMENDED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing receipient'], 500);
        }
    }

    public function singleApprove(Request $request)
    {
        try {
            $receipientId = $request->input('receipientId');
            $approved_by_id = $request->input('userId');

            $approvedDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $approvedDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));
            $receipient = RecipientProgram::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'approved_by_id' => $approved_by_id,
                'approved_date'  => $approvedDate,
                'status_id' => RecipientProgram::STATUS_APPROVE,
                'reason_to_reject' => '-'
            ]);

            $programId = $receipient->program_id;
            $payment = Payment::where('program_id', $programId)->first();

            if ($payment) {
                $payment->increment('total_recipient');
            } else {
            }

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
            $rejected_by_id = $request->input('userId');

            $rejected_date = new \DateTime('now', new \DateTimeZone('UTC'));
            $rejected_date->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $receipient = RecipientProgram::find($receipientId);

            if (!$receipient) {
                return response()->json(['error' => 'Receipient not found'], 404);
            }

            $receipient->update([
                'rejected_by_id' => $rejected_by_id,
                'rejected_date'  => $rejected_date,
                'status_id' => RecipientProgram::STATUS_REJECT,
                'reason_to_reject' => $request->text
            ]);

            $user = $request->user();
            $user->log(RecipientProgram::ACTIVITY_APPROVED, "App\Models\Receipient");

            return response()->json(['message' => 'Receipient successfully approve'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error approve Receipient'], 500);
        }
    }
}
