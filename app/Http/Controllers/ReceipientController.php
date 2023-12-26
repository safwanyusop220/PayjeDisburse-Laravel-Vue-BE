<?php

namespace App\Http\Controllers;

use App\Models\individualRecipient;
use App\Models\individualSchedularRecipient;
use App\Models\InstallmentProgram;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Receipient;
use App\Models\RefBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
        $rules = $this->getUpdateRules($id);
        $messages = $this->getUpdateMessages();

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->validate();

        $recipientData = [
            'name' => $request->name,
            'identification_number' => $request->identification_number,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'bank_id' => $request->bank_id,
            'account_number' => $request->account_number,
            'program_id' => $request->program_id,
            'status_id' => Receipient::STATUS_SUBMITTED,
        ];

        $recipient = Receipient::find($id);
        $recipient->update($recipientData);

        DB::commit();

            return response()->json([
                'message' => 'Recipient Created Successfully',
                'code' => 200,
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while creating the recipient',
                'message' => $e->errors(),
                'code' => 400,
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while creating the recipient',
                'code' => 500,
            ], 500);
        }
    }
    

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $rules = $this->getRules();
            $messages = $this->getMessages();
    
            $validator = Validator::make($request->all(), $rules, $messages);

            $validator->validate();
    
            $recipientData = [
                'name' => $request->name,
                'identification_number' => $request->identification_number,
                'address' => $request->address,
                'postcode' => $request->postcode,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'bank_id' => $request->bank_id,
                'account_number' => $request->account_number,
                'program_id' => $request->program_id,
                'status_id' => 1,
            ];
    
            $recipient = Receipient::create($recipientData);
            $recipientID = $recipient->id;

            if ($recipient->program->type->id != 1) {
                $user = $request->user();
                $user->log(Receipient::ACTIVITY_CREATED, "App\Models\Receipient");

                DB::commit();
        
                return response()->json([
                    'recipient' =>  $recipient,
                    'message' => 'Receipient Created Successfully',
                    'code' => 200,
                ]);
            }
    
            $individualRecipientData = [
                'recipient_id' => $recipientID,
                'program_id' => $request->program_id,
                'disburse_amount' => $request->disburse_amount,
                'frequency_id' => $request->frequency_id,
                'payment_date' => $request->payment_date,
                'payment_date' => $request->payment_date,
            ];
            
            if ($request->frequency_id == 2) {
                $individualRecipientData['total_month'] = $request->total_month;
                $individualRecipientData['end_date'] = $request->end_date;
            } elseif ($request->frequency_id == 3) {
                $individualRecipientData['total_year'] = $request->total_year;
                $individualRecipientData['end_date'] = $request->end_date;
            }
            
            $individualValidator = Validator::make($individualRecipientData, $this->getIndividualRecipientRules(), $this->getMessages());
            $individualValidator->validate();
            
            if ($request->frequency_id != 4) {
                $individualRecipient = IndividualRecipient::create($individualRecipientData);

                DB::commit();

                return response()->json([
                    'message' => 'Recipient Created Successfully',
                    'code' => 200,
                ]);
            }

            $individualRecipient = IndividualRecipient::create($individualRecipientData);

            $programId = $individualRecipient->program_id;

            foreach ($request->dynamicInputValue as $dynamicInput) {
                $dynamicInputRules = [
                    'payment_date' => 'required|date',
                ];

                $dynamicInputValidator = Validator::make($dynamicInput, $dynamicInputRules, $this->getMessages());

                $dynamicInputValidator->validate();

                IndividualSchedularRecipient::create([
                    'recipient_id' => $recipientID,
                    'program_id' => $programId,
                    'payment_date' => $dynamicInput['payment_date'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Recipient Created Successfully',
                'code' => 200,
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while creating the recipient',
                'message' => $e->errors(),
                'code' => 400,
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred while creating the recipient',
                'code' => 500,
            ], 500);
        }
    }

    public function edit($id, Request $request)
    {
        $recipient = Receipient::with('program', 'program.type', 'program.frequency',)->find($id);

        $user = $request->user();
        $user->log(Receipient::ACTIVITY_UPDATED, "App\Models\BankPanel");
        return response()->json($recipient);
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
        $receipient = Receipient::with('program', 'program.type', 'program.frequency', 'program.installmentPrograms', 'individualRecipient', 'individualRecipient.frequency', 'individualRecipient.recipient', 'individualRecipient.recipient.schedular')->find($id);
    
        if (!$receipient) {

        return response()->json(['message' => 'Program not found'], 404);
        }

        return response()->json([
            'receipient' => $receipient,
        ]);
    }

    public function getUpdateRules($id)
    {
        return [
            'name' => 'required|string',
            'identification_number' => 'required|numeric|digits:12|unique:receipients,identification_number,'. $id,
            'address' => 'required|string',
            'postcode' => 'required|numeric',
            'phone_number' => 'required|regex:/^\d+$/',
            'email' => 'required|email|unique:receipients,email,'. $id,
            'bank_id' => 'required',
            'account_number' => 'required|numeric',
            'program_id' => 'required',
        ];
    }

    public function getUpdateMessages()
    {
        return [
            'name.required' => 'Please insert the recipient name.',
            'identification_number.required' => 'Please insert the identification number.',
            'identification_number.numeric' => 'Identification number must be numeric only.',
            'identification_number.digits' => 'Identification number must be 12 digits.',
            'address.required' => 'Please insert the recipient address.',
            'postcode.required' => 'Please insert the postcode number.',
            'postcode.numeric' => 'Postcode number must be numeric.',
            'phone_number.required' => 'Please insert the phone number.',
            'phone_number.regex' => 'Phone number must be a valid numeric format.',
            'email.required' => 'Please insert the email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'bank_id.required' => 'Bank name is required.',
            'account_number.required' => 'Please insert the account number.',
            'account_number.numeric' => 'Account number must be numeric.',
            'program_id.required' => 'Program name is required.',
        ];
    }

    public function getRules()
    {
        return [
            'name' => 'required|string',
            'identification_number' => 'required|numeric|digits:12|unique:receipients,identification_number',
            'address' => 'required|string',
            'postcode' => 'required|numeric',
            'phone_number' => 'required|regex:/^\d+$/',
            'email' => 'required|email|unique:receipients,email',
            'bank_id' => 'required',
            'account_number' => 'required|numeric',
            'program_id' => 'required',
        ];
    }
    public function getMessages()
    {
        return [
            'name.required' => 'Please insert the recipient name.',
            'identification_number.required' => 'Please insert the identification number.',
            'identification_number.numeric' => 'Identification number must be numeric only.',
            'identification_number.digits' => 'Identification number must be 12 digits.',
            'address.required' => 'Please insert the recipient address.',
            'postcode.required' => 'Please insert the postcode number.',
            'postcode.numeric' => 'Postcode number must be numeric.',
            'phone_number.required' => 'Please insert the phone number.',
            'phone_number.regex' => 'Phone number must be a valid numeric format.',
            'email.required' => 'Please insert the email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'bank_id.required' => 'Bank name is required.',
            'account_number.required' => 'Please insert the account number.',
            'account_number.numeric' => 'Account number must be numeric.',
            'program_id.required' => 'Program name is required.',
        ];
    }

    public function getIndividualRecipientRules()
    {
        return [
            'recipient_id' => 'required|numeric',
            'program_id' => 'required|numeric',
            'disburse_amount' => 'required|numeric',
            'frequency_id' => 'required|numeric',
            'payment_date' => $this->getPaymentDateRule(),
            'total_month' => 'required_if:frequency_id,2|numeric',
            'total_year' => 'required_if:frequency_id,3|numeric',
        ];
    }

    protected function getPaymentDateRule()
    {
        return in_array(request()->input('frequency_id'), [1, 2, 3]) ? 'required|date' : 'sometimes';
    }
}