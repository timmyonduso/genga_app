<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotify;
class MailController extends Controller
{
    public function index()
    {
        $data = [
            'subject' => 'Timmy',
        'body' => 'Hello this is a test email'

        ];
        try {
            Mail::to('timmyonduso85@gmail.com')->send(new MailNotify($data));
            return response()->json(['Great check your mail box']);
        } catch (Exception $th) {
            return response()->json(['Great check your mail box']);
        }
    }
}
