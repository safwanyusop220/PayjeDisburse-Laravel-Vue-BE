<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use App\Models\InstallmentProgram;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        $program = new Program();
        $program->created_by_id   = $request->created_by_id;
        $program->name            = $request->name; 
        $program->code            = $request->code; 
        $program->type_id         = $request->type_id; 
        $program->disburse_amount = $request->disburse_amount; 
        $program->status_id       = 1; 
        $program->period          = $request->period; 
        $program->bank_panel      = $request->bank_panel;
        $program->frequency_id    = $request->frequency_id;
        $program->payment_date    = $request->payment_date; 
        $program->total_month     = $request->total_month;
        $program->total_year      = $request->total_year; 
        $program->save();

        $programId = $program->id;

        if($program->type_id == 3)
        {
            foreach ($request->dynamicInputValue as $dynamicInput) {
                $dynamicValue = new InstallmentProgram();
                $dynamicValue->program_id = $programId;
                $dynamicValue->name = '';
                $dynamicValue->payment_date = $dynamicInput['payment_date'];
                $dynamicValue->amount = $dynamicInput['value'];
                $dynamicValue->save();
            }
        }elseif($program->type_id == 4)
        {
            foreach ($request->dynamicInputValue as $dynamicInput) {
                $dynamicValue = new InstallmentProgram();
                $dynamicValue->program_id = $programId;
                $dynamicValue->name = $dynamicInput['name'];
                $dynamicValue->payment_date = $dynamicInput['payment_date'];
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

    public function endorseRecommendation(Request $request)
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

    public function approve(Request $request)
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
}
