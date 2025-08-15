<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    private $bookingsPath = 'bookings';
    private $uploadsPath = 'uploads';

    /**
     * Store a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tourPackage' => 'required|string|max:255',
            'travelDate' => 'required|date',
            'visaFile' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'documentFile' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate unique booking ID
            $bookingId = 'BK-' . strtoupper(Str::random(8));
            
            // Store uploaded files
            $visaPath = $request->file('visaFile')->store($this->uploadsPath . '/visas', 'public');
            $documentPath = $request->file('documentFile')->store($this->uploadsPath . '/documents', 'public');

            // Prepare booking data
            $bookingData = [
                'id' => $bookingId,
                'fullName' => $request->input('fullName'),
                'email' => $request->input('email'),
                'tourPackage' => $request->input('tourPackage'),
                'travelDate' => $request->input('travelDate'),
                'visaFile' => $visaPath,
                'documentFile' => $documentPath,
                'createdAt' => Carbon::now()->toISOString(),
                'status' => 'pending'
            ];

            // Save booking data to JSON file
            $this->saveBookingToFile($bookingId, $bookingData);

            return response()->json([
                'message' => 'Booking submitted successfully!',
                'bookingId' => $bookingId
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all bookings.
     */
    public function index(): JsonResponse
    {
        try {
            $bookings = $this->getAllBookings();
            return response()->json([
                'message' => 'Bookings retrieved successfully',
                'data' => $bookings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve bookings',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific booking by ID.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $booking = $this->getBookingById($id);
            
            if (!$booking) {
                return response()->json([
                    'message' => 'Booking not found'
                ], 404);
            }

            return response()->json([
                'message' => 'Booking retrieved successfully',
                'data' => $booking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,confirmed,cancelled,completed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = $this->getBookingById($id);
            
            if (!$booking) {
                return response()->json([
                    'message' => 'Booking not found'
                ], 404);
            }

            $booking['status'] = $request->input('status');
            $booking['updatedAt'] = Carbon::now()->toISOString();

            $this->saveBookingToFile($id, $booking);

            return response()->json([
                'message' => 'Booking status updated successfully',
                'data' => $booking
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update booking status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a booking.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $booking = $this->getBookingById($id);
            
            if (!$booking) {
                return response()->json([
                    'message' => 'Booking not found'
                ], 404);
            }

            // Delete associated files
            if (isset($booking['visaFile']) && Storage::disk('public')->exists($booking['visaFile'])) {
                Storage::disk('public')->delete($booking['visaFile']);
            }
            
            if (isset($booking['documentFile']) && Storage::disk('public')->exists($booking['documentFile'])) {
                Storage::disk('public')->delete($booking['documentFile']);
            }

            // Delete booking file
            Storage::delete($this->bookingsPath . '/' . $id . '.json');

            return response()->json([
                'message' => 'Booking deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save booking data to file.
     */
    private function saveBookingToFile(string $bookingId, array $data): void
    {
        $filePath = $this->bookingsPath . '/' . $bookingId . '.json';
        Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Get booking by ID from file.
     */
    private function getBookingById(string $id): ?array
    {
        $filePath = $this->bookingsPath . '/' . $id . '.json';
        
        if (!Storage::exists($filePath)) {
            return null;
        }

        $content = Storage::get($filePath);
        return json_decode($content, true);
    }

    /**
     * Get all bookings from files.
     */
    private function getAllBookings(): array
    {
        $bookings = [];
        $files = Storage::files($this->bookingsPath);

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $content = Storage::get($file);
                $booking = json_decode($content, true);
                if ($booking) {
                    $bookings[] = $booking;
                }
            }
        }

        // Sort by creation date (newest first)
        usort($bookings, function ($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });

        return $bookings;
    }
}
