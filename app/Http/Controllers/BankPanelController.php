<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            $rules = $this->getRules();
            $messages = $this->getMessages();

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'messages' => $validator->errors(),
                    'code' => 400,
                ]);
            }

            $bankPanel = BankPanel::create([
                'holder_name'    => $request->holder_name,
                'bank_id'        => $request->bank_id,
                'account_number' => $request->account_number,
            ]);

            $user = $request->user();
            $user->log(BankPanel::ACTIVITY_CREATED, "App\Models\BankPanel");

            return response()->json([
                'message' => 'Bank Panel Created Successfully',
                'data' => $bankPanel,
                'userdata' => $user,
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
                'message' => 'Bank panel Deleted Successfully.',
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

    public function update(Request $request, $id)
    {
        try {
            $rules = $this->getRules();
            $messages = $this->getMessages();

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation Error',
                    'messages' => $validator->errors(),
                    'code' => 400,
                ]);
            }

            $bankPanel = BankPanel::where('id', $id)->first();

            if (!$bankPanel) {
                return response()->json([
                    'error' => 'Bank Panel not found',
                    'code' => 404,
                ], 404);
            }

            $bankPanel->update([
                'holder_name' => $request->holder_name,
                'bank_id' => $request->bank_id,
                'account_number' => $request->account_number,
            ]);

            $user = $request->user();
            $user->log(BankPanel::ACTIVITY_UPDATED, "App\Models\BankPanel");

            return response()->json([
                'message' => 'Bank Panel Updated Successfully',
                'data' => $bankPanel,
                'userdata' => $user,
                'code' => 200,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while updating the bank panel',
                'code' => 500,
            ], 500);
        }
    }

    public function getMessages()
    {
        return [
            'holder_name.required' => 'Please insert the holder name.',
            'bank_id.required' => 'Please insert the bank ID.',
            'account_number.required' => 'Please insert the account number.',
            'account_number.numeric' => 'Account number must be numeric.',
        ];
    }

    public function getRules()
    {
        return [
            'holder_name'    => 'required|string',
            'bank_id'        => 'required|numeric',
            'account_number' => 'required|numeric',
        ];
    }
}
