<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use App\Models\InstallmentProgram;
use App\Models\Program;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function programs()
    {
        $programs = Program::with('type', 'status')->orderBy('id', 'desc')->get();

        return response()->json([
            'programs' => $programs,
            'message'  => 'Programs',
            'code'     => 200,
        ]);
    }

    public function store(Request $request)
    {
        try {

            $rules = $this->getRules($request);
            $messages = $this->getMessages();

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'messages' => $validator->errors(),
                    'code' => 400,
                ]);
            }

            $program = new Program();
            $program->created_by_id = $request->created_by_id;
            $program->name = $request->name;
            $program->code = $request->code;
            $program->type_id = $request->type_id;
            $program->status_id = 1;
            $program->bank_panel = $request->bank_panel;

            if ($request->type_id == 2 || $request->type_id == 3 || $request->type_id == 4) {
                $program->disburse_amount = $request->disburse_amount;
            }

            if ($request->type_id == 2) {
                $program->frequency_id = $request->frequency_id;
                $program->payment_date = $request->payment_date;
                $program->total_month = $request->total_month;
            }
            $program->save();

            $programId = $program->id;

            if ($program->type_id == 3 || $program->type_id == 4) {
                foreach ($request->dynamicInputValue as $dynamicInput) {
                    $dynamicValue = new InstallmentProgram();
                    $dynamicValue->program_id = $programId;
                    $dynamicValue->name = $program->type_id == 3 ? '' : $dynamicInput['name'];
                    $dynamicValue->payment_date = $dynamicInput['payment_date'];
                    $dynamicValue->amount = $program->type_id == 3 ? $dynamicInput['value'] : null;
                    $dynamicValue->save();
                }
            }
            $user = $request->user();
            $user->log(Program::ACTIVITY_CREATED, "App\Models\Program");
    
            return response()->json([
                'message' => 'Program Created Successfully',
                'Program ID' => $programId,
                'code'    => 200
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the program',
                'code' => 500,
            ], 500);
        }
    }

    public function recommendation()
    {
        $recommendations = Program::with('type', 'status')->where('status_id', 1)->orderBy('id', 'desc')->get();

        return response()->json([
            'recommendations' => $recommendations,
            'message'  => 'recommendations',
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

            $programsToUpdate = Program::whereIn('id', $checkedIDs)->get();

            $programsToUpdate->each(function ($program) use ($recommend_by_id, $recommendDate) {
                $program->update([
                    'recommend_by_id' => $recommend_by_id,
                    'recommend_date'  => $recommendDate,
                    'status_id'       => Program::STATUS_RECOMMENDED,
                    'reject_reason'   => '-',
                ]);
            });

            $user = $request->user();
            $user->log(Program::ACTIVITY_RECOMMENDED, "App\Models\Program");

            return response()->json(['message' => 'Programs successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing programs'], 500);
        }
    }

    public function bulkRejectRecommendation(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
            $rejected_by_id = $request->input('userId');

            $rejectedDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $rejectedDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $reason_to_reject =  $request->text;

            $programsToUpdate = Program::whereIn('id', $checkedIDs)->get();

            $programsToUpdate->each(function ($program) use ($rejected_by_id, $rejectedDate, $reason_to_reject) {
                $program->update([
                    'rejected_by_id' => $rejected_by_id,
                    'rejected_date'  => $rejectedDate,
                    'status_id'       => Program::STATUS_REJECT,
                    'reason_to_reject'   => $reason_to_reject,
                ]);
            });

            $user = $request->user();
            $user->log(Program::ACTIVITY_REJECTED, "App\Models\Program");

            return response()->json(['message' => 'Programs successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing programs'], 500);
        }
    }

    public function singleRecommendation(Request $request)
    {
        try {
            $programId = $request->input('programId');
            $recommend_by_id = $request->input('userId');

            $recommendDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $recommendDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $program = Program::find($programId);

            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $program->update([
                'recommend_by_id' => $recommend_by_id,
                'recommend_date'  => $recommendDate,
                'status_id' => Program::STATUS_RECOMMENDED,
            ]);

            $user = $request->user();
            $user->log(Program::ACTIVITY_RECOMMENDED, "App\Models\Program");

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing program'], 500);
        }
    }

    public function singleRejectSubmit(Request $request)
    {
        try {
            $programId = $request->input('programId');
            $program = Program::find($programId);

            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $program->update([
                'status_id' => Program::STATUS_REJECT,
                'reason_to_reject' => $request->text
            ]);

            $user = $request->user();
            $user->log(Program::ACTIVITY_APPROVED, "App\Models\Program");

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error Approved program'], 500);
        }
    }

    public function singleApprove(Request $request)
    {
        try {
            $programId = $request->input('programId');
            $approved_by_id = $request->input('userId');

            $approvedDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $approvedDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));
            $program = Program::find($programId);

            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $program->update([
                'approved_by_id' => $approved_by_id,
                'approved_date'  => $approvedDate,
                'status_id' => Program::STATUS_APPROVE,
            ]);

            $user = $request->user();
            $user->log(Program::ACTIVITY_APPROVED, "App\Models\Program");

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error Approved program'], 500);
        }
    }

    public function singleReject(Request $request)
    {
        try {
            $programId = $request->input('programId');
            $program = Program::find($programId);

            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $program->update([
                'status_id' => Program::STATUS_REJECT,
                'reason_to_reject' => $request->text
            ]);

            $user = $request->user();
            $user->log(Program::ACTIVITY_APPROVED, "App\Models\Program");

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error Approved program'], 500);
        }
    }

    public function bulkApprove(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');

            $approved_by_id = $request->input('userId');

            $approvedDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $approvedDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));
    
            $programsToUpdate = Program::whereIn('id', $checkedIDs)->get();

            $programsToUpdate->each(function ($program) use ($approved_by_id,$approvedDate) {
                $program->update([
                    'approved_by_id' => $approved_by_id,
                    'approved_date'  => $approvedDate,
                    'status_id' => Program::STATUS_APPROVE,
                    'reject_reason' => '-'
                ]);
            });
    
            $user = $request->user();
            $user->log(Program::ACTIVITY_APPROVED, "App\Models\Program");

            return response()->json(['message' => 'Programs successfully approved'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing programs'], 500);
        }
    }

    public function bulkReject(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
            $rejected_by_id = $request->input('userId');

            $rejectedDate = new \DateTime('now', new \DateTimeZone('UTC'));
            $rejectedDate->setTimezone(new \DateTimeZone('Asia/Kuala_Lumpur'));

            $reason_to_reject =  $request->text;

            $programsToUpdate = Program::whereIn('id', $checkedIDs)->get();

            $programsToUpdate->each(function ($program) use ($rejected_by_id, $rejectedDate, $reason_to_reject) {
                $program->update([
                    'rejected_by_id' => $rejected_by_id,
                    'rejected_date'  => $rejectedDate,
                    'status_id'       => Program::STATUS_REJECT,
                    'reason_to_reject'   => $reason_to_reject,
                ]);
            });

            $user = $request->user();
            $user->log(Program::ACTIVITY_REJECTED, "App\Models\Program");

            return response()->json(['message' => 'Programs successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing programs'], 500);
        }
    }

    public function approval()
    {
        $approvals = Program::with('type', 'status')->where('status_id', 2)->orderBy('id', 'desc')->get();

        return response()->json([
            'approvals' => $approvals,
            'message'  => 'approvals',
            'code'     => 200,
        ]);
    }

    public function destroy($id, Request $request)
    {
        $program = Program::find($id);
        if($program) {
            $program->delete();

            $user = $request->user();
            $user->log(Program::ACTIVITY_DELETED, "App\Models\Program");

            return response()->json([
                'message' => 'Program Deleted Successfully',
                'code'    => 200
            ]);
        } else {
            return response()->json([
                'message' => "Program with id:$id does not exist"
            ]);
        }
    }

    public function edit($id)
    {
        $program = Program::find($id);

        return response()->json($program);
    }

    public function update($id, Request $request)
    {
        $program = Program::where('id', $id)->first();
        $program->name = $request->name; 
        $program->code = $request->code; 
        $program->type_id = $request->type_id; 
        $program->disburse_amount = $request->disburse_amount; 
        $program->status_id = 1; 
        $program->period = $request->period; 
        $program->bank_panel = $request->bank_panel;
        $program->frequency_id = $request->frequency_id;
        $program->payment_date = $request->payment_date; 
        $program->total_month = $request->total_month;
        $program->total_year = $request->total_year; 
        $program->save();
        
        $user = $request->user();
        $user->log(Program::ACTIVITY_UPDATED, "App\Models\Program");

        return response()->json([
            'message' => 'Program Updated Successfully',
            'code'    => 200
        ]);
    }

    public function bankPanels()
    {
        $bankPanels = BankPanel::with('bank')->get();

        return response()->json([
            'bankPanels' => $bankPanels,
            'message'  => 'Bank Panels',
            'code'     => 200,
        ]);
    }

    public function show($id)
    {
        $program = Program::with('type', 'bankPanel', 'bankPanel.bank', 'frequency', 'status', 'created_by', 'recommend_by', 'approved_by')->find($id);
    
        if (!$program) {
            return response()->json(['message' => 'Program not found'], 404);
        }

        $installmentPrograms = InstallmentProgram::where('program_id', $id)->get();

        return response()->json([
            'program' => $program,
            'installmentPrograms' => $installmentPrograms,
        ]);
    }

    public function getRules($request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code',
            'type_id' => 'required',
            'bank_panel' => 'required',
            'created_by_id' => 'exists:users,id',
        ];

        if ($request->type_id == 2) {
            $rules = array_merge($rules, [
                'disburse_amount' => 'required|numeric',
                'frequency_id' => 'required|exists:frequencies,id',
                'payment_date' => 'required|date',
            ]);
        }

        if ($request->type_id == 3) {
            $rules = array_merge($rules, [
                'disburse_amount' => 'required|numeric',
            ]);
        }

        if ($request->type_id == 4) {
            $rules = array_merge($rules, [
                'disburse_amount' => 'required|numeric',
            ]);
        }

        return $rules;
    }

    public function getMessages()
    {
        return [
            'created_by_id.exists' => 'The specified user does not exist.',
            'name.required' => 'Please insert program name.',
            'disburse_amount.required' => 'Please insert disburse amount.',
            'payment_date.required' => 'Please insert payment date.',
            'code.unique' => 'The program code must be unique.',
            'type_id.in' => 'Invalid program type.',
            'total_month.required_if' => 'The total month field is required for the selected program type.',
            'total_year.required_if' => 'The total year field is required for the selected program type.',
        ];
    }
}
