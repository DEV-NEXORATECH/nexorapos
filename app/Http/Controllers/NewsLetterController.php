<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsLetter;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail; // Make sure this exists

class NewsLetterController extends Controller
{
    public function news(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            // Save email to DB
            $subscribe = new NewsLetter();
            $subscribe->email = $request->email;
            $subscribe->save();

            // Prepare greeting message
            $msg = "Welcome to our Blog! Thank you for subscribing.";
            $sub = "Blog Subscription Confirmation";

            // Send email to the user
            Mail::to($request->email)->send(new WelcomeEmail($msg, $sub));

            return redirect()->back()->with('success', 'Thanks for subscribing! Please check your email.');
        } catch (\Exception $e) {
            // Check for duplicate entry error
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('duplicate', 'You are already subscribed!');
            }

            // Other errors
            return redirect()->back()->with('failed', 'Something went wrong. Please try again.');
        }
    }
}
