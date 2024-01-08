<?php

namespace App\Http\Controllers;

use App\Mail\Program\Created;
use App\Models\BankPanel;
use App\Models\InstallmentProgram;
use App\Models\Payment;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
        DB::beginTransaction();
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
                $program->total_year = $request->total_year;
                $program->end_date = $request->end_date;
            }
            $program->save();

            $runningNumberRecord = DB::table('running_numbers')->first();

            // If a record exists, increment the running number; otherwise, initialize it to 1
            if ($runningNumberRecord) {
                $runningNumber = $runningNumberRecord->last_number + 1;
                DB::table('running_numbers')
                    ->update(['last_number' => $runningNumber]);
            } else {
                $runningNumber = 1;
                DB::table('running_numbers')->insert([
                    'last_number' => $runningNumber
                ]);
            }

            // Pad the running number with leading zeros to ensure it's always a 3-digit number
            $formattedRunningNumber = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);

            $programId = $program->id;

            // Generate the code using the padded running number
            $codePrefix = $this->getCodePrefix($program->name);  // Adjust as per your needs
            $timestamp = Carbon::now()->timestamp;
            $date = Carbon::createFromTimestamp($timestamp);
            $formattedDate = $date->format('ymd');
            $program->code = $codePrefix . $formattedDate . $formattedRunningNumber;

            // $codePrefix = $this->getCodePrefix($program->name);
            // $timestamp = Carbon::now()->timestamp;
            // $date = Carbon::createFromTimestamp($timestamp);
            // $formattedDate = $date->format('ymd');
            // $program->code = $codePrefix.$formattedDate.$runningNumber;
            $program->save();

            if ($program->type_id == 3) {
                foreach ($request->dynamicInputValue as $dynamicInput) {
                    $dynamicInputRules = [
                        'value' => 'required',
                        'payment_date' => 'required',
                    ];
    
                    $dynamicInputValidator = Validator::make($dynamicInput, $dynamicInputRules, $this->getMessages());
    
                    if ($dynamicInputValidator->fails()) {
                        return response()->json([
                            'error' => 'Validation Error',
                            'messages' => $dynamicInputValidator->errors(),
                            'code' => 400,
                        ]);
                    }
                    $dynamicValue = new InstallmentProgram();
                    $dynamicValue->program_id = $programId;
                    $dynamicValue->name = $program->type_id == 3 ? '' : $dynamicInput['name'];
                    $dynamicValue->payment_date = $dynamicInput['payment_date'];
                    $dynamicValue->amount = $program->type_id == 3 ? $dynamicInput['value'] : null;
                    $dynamicValue->save();
                }
            }

            if ($program->type_id == 4) {
                foreach ($request->dynamicInputValue as $dynamicInput) {
                    $dynamicInputRules = [
                        'schedularName' => 'required',
                        'payment_date' => 'required',
                    ];
    
                    $dynamicInputValidator = Validator::make($dynamicInput, $dynamicInputRules, $this->getMessages());
    
                    if ($dynamicInputValidator->fails()) {
                        return response()->json([
                            'error' => 'Validation Error',
                            'messages' => $dynamicInputValidator->errors(),
                            'code' => 400,
                        ]);
                    }
                    $dynamicValue = new InstallmentProgram();
                    $dynamicValue->program_id = $programId;
                    $dynamicValue->name = $program->type_id == 3 ? '' : $dynamicInput['schedularName'];
                    $dynamicValue->payment_date = $dynamicInput['payment_date'];
                    $dynamicValue->amount = $program->type_id == 3 ? $dynamicInput['value'] : null;
                    $dynamicValue->save();
                }
            }

            DB::commit();

            $user = $request->user();
            $user->log(Program::ACTIVITY_CREATED, "App\Models\Program");

            // dispatch(new SendCreatedEmail());
            // Mail::to('safwan@edaran.com')->send(new Created);
            // Mail::to('safwanyusop220@gmail.com')->send(new Created);

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

    public function getCodePrefix($name)
    {
        $words = explode(' ', $name);

        if (sizeof($words) > 1) {
            return implode(array_map(function ($word) {
                return strtoupper(substr($word, 0, 1));
            }, array_slice($words, 0, 2)));
        }

        return strtoupper(substr($name, 0, 2));
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
            
            $payment = new Payment();
            $payment->program_id = $program->id;
            $payment->status_id = Payment::STATUS_REQUEST;
            $payment->save();

            $user = $request->user();
            $user->log(Program::ACTIVITY_APPROVED, "App\Models\Program");

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error Approved program', 'details' => $e->getMessage()], 500);
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

                $payment = new Payment();
                $payment->program_id = $program->id;
                $payment->status_id = Payment::STATUS_REQUEST;
                $payment->save();
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
        $program = Program::with('installmentPrograms')->find($id);

        return response()->json($program);
    }

    public function update($id, Request $request)
    {
        try {
            $rules = $this->getUpdateRules($request, $id);
            $messages = $this->getUpdateMessages();

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'messages' => $validator->errors(),
                    'code' => 400,
                ]);
            }
            
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
            $program->end_date = $request->end_date;
            $program->status_id = Program::STATUS_SUBMITTED;
            $program->save();

            if ($program->type_id == 3) {
                $installmentPrograms = InstallmentProgram::where('program_id', $id)->get();
            
                foreach ($request->installment_programs as $index => $dynamicInput) {
                    $dynamicInputRules = [
                        'amount' => 'required',
                        'payment_date' => 'required',
                    ];
            
                    $dynamicInputValidator = Validator::make($dynamicInput, $dynamicInputRules, $this->getUpdateMessages());
            
                    if ($dynamicInputValidator->fails()) {
                        return response()->json([
                            'error' => 'Validation Error',
                            'messages' => $dynamicInputValidator->errors(),
                            'code' => 400,
                        ]);
                    }
            
                    if (isset($installmentPrograms[$index])) {
                        $installmentProgram = $installmentPrograms[$index];
                        $installmentProgram->amount = $dynamicInput['amount'];
                        $installmentProgram->payment_date = $dynamicInput['payment_date'];
                        $installmentProgram->save();
                    }
                }
            }

            if ($program->type_id == 4) {
                $installmentPrograms = InstallmentProgram::where('program_id', $id)->get();
            
                foreach ($request->installment_programs as $index => $dynamicInput) {
                    $dynamicInputRules = [
                        'name' => 'required',
                        'payment_date' => 'required',
                    ];
            
                    $dynamicInputValidator = Validator::make($dynamicInput, $dynamicInputRules, $this->getUpdateMessages());
            
                    if ($dynamicInputValidator->fails()) {
                        return response()->json([
                            'error' => 'Validation Error',
                            'messages' => $dynamicInputValidator->errors(),
                            'code' => 400,
                        ]);
                    }
            
                    if (isset($installmentPrograms[$index])) {
                        $installmentProgram = $installmentPrograms[$index];
                        $installmentProgram->name = $dynamicInput['name'];
                        $installmentProgram->payment_date = $dynamicInput['payment_date'];
                        $installmentProgram->save();
                    }
                }
            }



            $user = $request->user();
            $user->log(Program::ACTIVITY_UPDATED, "App\Models\Program");

            return response()->json([
                'message' => 'Program Updated Successfully',
                'code'    => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while creating the program',
                'code' => 500,
            ], 500);
        }
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

    public function getRecommendCount()
    {
        $recommend = Program::with('type', 'status')->where('status_id', 1)->orderBy('id', 'desc')->get();

        $totalRecommendations = $recommend->count();

        return response()->json([
            'total_recommendations' => $totalRecommendations,
            'message'  => 'recommendations',
            'code'     => 200,
        ]);
    }

    public function getUpdateRules($request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:programs,code,' . $id,
            'type_id' => 'required',
            'bank_panel' => 'required',
            'created_by_id' => 'exists:users,id',
        ];

        if ($request->type_id == 2) {
            $rules = array_merge($rules, [
                'disburse_amount' => 'required|numeric',
                'frequency_id' => 'required|exists:frequencies,id',
                'payment_date' => 'required|date',
                'total_month' => 'required_if:frequency_id,2',
                'total_year' => 'required_if:frequency_id,3',

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

    public function getUpdateMessages()
    {
        return [
            'created_by_id.exists' => 'The specified user does not exist.',
            'name.required' => '* Program name is required',
            'disburse_amount.required' => '* Disburse amount is required',
            'payment_date.required' => '* Payment date is required',
            'code.unique' => '* Program Code must be unique',
            'type_id.in' => '* Program Type is invalid',
            'total_month.required_if' => '* Total month is required',
            'total_year.required_if' => '* Total year is required',
            'value.required' => '* Amount is required',
        ];
    }

    public function getRules($request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            // 'code' => 'required|string|max:50|unique:programs,code',
            'type_id' => 'required',
            'bank_panel' => 'required',
            'created_by_id' => 'exists:users,id',
        ];

        if ($request->type_id == 2) {
            $rules = array_merge($rules, [
                'disburse_amount' => 'required|numeric',
                'frequency_id' => 'required|exists:frequencies,id',
                'payment_date' => 'required|date',
                'total_month' => 'required_if:frequency_id,2',
                'total_year' => 'required_if:frequency_id,3',
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
            'name.required' => '* Program name is required',
            'bank_panel.required' => '* Bank panel is required',
            'payment_date.required' => '* Payment date is required',
            'code.unique' => '* Program Code must be unique',
            'code.required' => '* Program Code is required',
            'type_id.in' => '* Program Type is invalid',
            'disburse_amount.required' => '* Disburse amount is required',
            'total_month.required_if' => '* Total month is required',
            'total_year.required_if' => '* Total year is required',
            'value.required' => '* Amount is required',
            'frequency_id.required' => '* Frequency is required',
            'frequency_id.exists' => '* Selected frequency id is invalid.'
        ];
    }
}
