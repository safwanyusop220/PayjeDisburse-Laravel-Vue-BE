<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use App\Models\InstallmentProgram;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $program = new Program();
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
    
            $programsToUpdate = Program::whereIn('id', $checkedIDs)->get();
    
            $programsToUpdate->each(function ($program) {
                $program->update([
                    'status_id' => Program::STATUS_RECOMMENDED,
                    'reject_reason' => '-'
                ]);
            });
    
            return response()->json(['message' => 'Programs successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing programs'], 500);
        }
    }

    public function singleRecommendation(Request $request)
    {
        try {
            $programId = $request->input('programId');

            $program = Program::find($programId);

            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $program->update([
                'status_id' => Program::STATUS_RECOMMENDED,
                'reject_reason' => '-'
            ]);

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing program'], 500);
        }
    }

    public function singleApprove(Request $request)
    {
        try {
            $programId = $request->input('programId');

            $program = Program::find($programId);

            if (!$program) {
                return response()->json(['error' => 'Program not found'], 404);
            }

            $program->update([
                'status_id' => Program::STATUS_APPROVE,
                'reject_reason' => '-'
            ]);

            return response()->json(['message' => 'Program successfully endorsed'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error endorsing program'], 500);
        }
    }

    public function approve(Request $request)
    {
        try {
            $checkedIDs = $request->input('checkedIDs');
    
            $programsToUpdate = Program::whereIn('id', $checkedIDs)->get();
    
            $programsToUpdate->each(function ($program) {
                $program->update([
                    'status_id' => Program::STATUS_APPROVE,
                    'reject_reason' => '-'
                ]);
            });
    
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

    public function destroy($id)
    {
        $program = Program::find($id);
        if($program) {
            $program->delete();
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
    $program = Program::with('type', 'bankPanel', 'bankPanel.bank', 'frequency')->find($id);
    
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
