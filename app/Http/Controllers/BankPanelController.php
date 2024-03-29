<?php

namespace App\Http\Controllers;

use App\Models\BankPanel;
use App\Models\RefBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BankPanelController extends Controller
{
    public function index(Request $request)
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

            $bankPanel = BankPanel::create([
                'organization_name'    => $request->organization_name,
                'bank_id'              => $request->bank_id,
                'account_number'       => $request->account_number,
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

            $bankPanel = BankPanel::where('id', $id)->first();

            if (!$bankPanel) {
                return response()->json([
                    'error' => 'Bank Panel not found',
                    'code' => 404,
                ], 404);
            }

            $bankPanel->update([
                'organization_name' => $request->organization_name,
                'bank_id'           => $request->bank_id,
                'account_number'    => $request->account_number,
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

    public function search(Request $request)
    {
        $query = $request->get('query');

        $results = BankPanel::where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('organization_name', 'like', "%{$query}%");
                        // ->orWhere('bank.name', 'like', "%{$query}%");
                        // ->orWhere('account_number', 'like', "%{$query}%");
        })->get();

        return response()->json($results);
    }

    public function getMessages()
    {
        return [
            'organization_name.required' => '* Organization name is required',
            'bank_id.required' => '* Bank type is required',
            'account_number.required' => '* Account number is required',
            'account_number.numeric' => '* Account number must be numeric',
            'account_number.digits' => '* Account number must be :digits digits',
            'account_number.unique' => '* Account number must be unique',
        ];
    }

    public function getRules(Request $request)
    {
        $accountNumberLength = $this->getAccountNumberLength($request->input('bank_id'));
        return [
            'organization_name'    => 'required|string',
            'bank_id'        => 'required|numeric',
            'account_number' => 'required|numeric|unique:bank_panels,account_number|digits:' . $accountNumberLength
        ];
    }

    public function getAccountNumberLength($bankId)
    {
        $bank = RefBank::find($bankId);

        return $bank ? $bank->account_number_length : 0;
    }
}
