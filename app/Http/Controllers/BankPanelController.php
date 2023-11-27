<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use Illuminate\Http\Request;

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
        $bankPanel = new BankPanel();
        $bankPanel->holder_name = $request->holder_name; 
        $bankPanel->bank_id = $request->bank_id; 
        $bankPanel->account_number = $request->account_number; 
        $bankPanel->save();
        return response()->json([
            'message' => 'Bank Panel Created Successfully',
            'code'    => 200
        ]);
    }

    public function destroy($id)
    {
        $bankPanel = BankPanel::find($id);
        if($bankPanel) {
            $bankPanel->delete();
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

    public function edit($id)
    {
        $bankPanel = BankPanel::find($id);

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
