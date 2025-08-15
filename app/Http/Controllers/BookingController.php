<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tourPackage' => 'required|string|max:255',
            'travelDate' => 'required|date',
            'visaFile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'documentFile' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
        ]);

        // ✅ Store uploaded files
        $visaPath = $request->file('visaFile')->store('uploads/visas', 'public');
        $documentPath = $request->file('documentFile')->store('uploads/documents', 'public');

        // ✅ Save to database
        Booking::create([
            'fullName' => $validated['fullName'],
            'email' => $validated['email'],
            'tourPackage' => $validated['tourPackage'],
            'travelDate' => $validated['travelDate'],
            'visaFile' => $visaPath,
            'documentFile' => $documentPath,
        ]);

        return response()->json(['message' => 'Booking submitted successfully.'], 201);
    }
}
