<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Receipient;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment()
    {
        $paymentRequest = Payment::with('program', 'program.type', 'program.status', 'status')->where('status_id', 5)->orderBy('id', 'desc')->get();

        $paymentProcessing = Payment::with('program', 'program.type', 'program.status', 'status')->where('status_id', 6)->orderBy('id', 'desc')->get();

        $paymentProceed = Payment::with('program', 'program.type', 'program.status', 'status')->where('status_id', 7)->orderBy('id', 'desc')->get();

        return response()->json([
            'paymentRequest' => $paymentRequest,
            'message'  => 'paymentRequest',

            'paymentProcessing' => $paymentProcessing,
            'message'  => 'paymentProcessing',

            'paymentProceed' => $paymentProceed,
            'message'  => 'paymentProceed',

            'code'     => 200,
        ]);
    }

    public function recipientList($id, Request $request)
    {

        $programData = Payment::with('program', 'program.type', 'program.frequency', 'program.recipients', 'program.recipients.individualRecipient')->find($id);

        $recipientList = Receipient::with('individualRecipient', 'program')->where('program_id', $id)->where('status_id', 3)
        ->orderBy('id', 'desc')->get();

        return response()->json([
            'programData' => $programData,
            'recipients' => $recipientList,
        ]);
    }
}
