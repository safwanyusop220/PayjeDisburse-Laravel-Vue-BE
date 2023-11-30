<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankPanelController extends Controller
{
    public function index()
    {
        $bankPanels = BankPanel::with('bank')->orderBy('id', 'desc')->get();

        return response()->json([
            'bankPanels' => $bankPanels,
            'message'  => 'bankPanels',
            'code'     => 200,
        ]);
    }

    public function store(Request $request)
{
    try {
        $bankPanel = new BankPanel();
        $bankPanel->holder_name    = $request->holder_name; 
        $bankPanel->bank_id        = $request->bank_id; 
        $bankPanel->account_number = $request->account_number; 
        $bankPanel->save();
        
        $user = $request->user();
        $user->log(BankPanel::ACTIVITY_CREATED , "App\Models\BankPanel");

        return response()->json([
            'message' => 'Bank Panel Created Successfully',
            'data' => $bankPanel,
            'testdata' => $user,
            'code' => 200,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'An error occurred while creating the bank panel',
            'code' => 500,
        ], 500);
    }
}

    public function destroy($id, Request $request)
    {
        $bankPanel = BankPanel::find($id);
        if($bankPanel) {
            $bankPanel->delete();

            $user = $request->user();
            $user->log(BankPanel::ACTIVITY_DELETED, "App\Models\BankPanel");

            return response()->json([
                'message' => 'Bank panel Deleted Successfully',
                'code'    => 200
            ]);
        } else {
            return response()->json([
                'message' => "Bank panel with id:$id does not exist"
            ]);
        }
    }

    public function edit($id, Request $request)
    {
        $bankPanel = BankPanel::find($id);

        $user = $request->user();
        $user->log(BankPanel::ACTIVITY_UPDATED, "App\Models\BankPanel");
        return response()->json($bankPanel);
    }

    public function update($id, Request $request)
    {
        $bankPanel = BankPanel::where('id', $id)->first();
        $bankPanel->holder_name = $request->holder_name; 
        $bankPanel->bank_id = $request->bank_id; 
        $bankPanel->account_number = $request->account_number; 
        $bankPanel->save();
        return response()->json([
            'message' => 'Bank panel Updated Successfully',
            'code'    => 200
        ]);
    }
}
