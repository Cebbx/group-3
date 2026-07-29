<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\TripTicket;
use App\Models\VehicleRequest;
use App\Models\WithdrawalSlip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate tables first to ensure sequential starting numbers (REQ-00001, TT-00001, WS-00001)
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        VehicleRequest::query()->truncate();
        TripTicket::query()->truncate();
        WithdrawalSlip::query()->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Get default employee user (id = 1)
        $employeeUser = User::where('role', 'employee')->first();
        $userId = $employeeUser ? $employeeUser->id : 1;

        // Get drivers
        $drivers = Driver::all();
        if ($drivers->isEmpty()) {
            echo "No drivers found in the database. Please run DriverSeeder first.\n";
            return;
        }

        $vehicles = [
            'FORTUNER - SBA1749',
            'HIACE VAN - SBA3790',
            'PTIA JEEP - SDV868',
            'MULTICAB - NAJI987',
        ];

        $departments = ['COT', 'CBA', 'CAS', 'CTED', 'CHM', 'CCJE'];

        $destinations = [
            'Tagaytay City Hall',
            'Manila Central Post Office',
            'Baguio Teachers Camp',
            'Batangas Port Terminal',
            'Clark Freeport Zone, Pampanga',
            'Subic Bay Exhibition Center',
            'Cavite Provincial Capitol',
            'Laguna Sports Complex',
            'Quezon Memorial Circle',
            'Makati Financial District',
        ];

        $purposes = [
            'Attend Regional Seminar and Workshop',
            'Conduct University Research Data Gathering',
            'Participate in State Colleges Athletic Association Meet',
            'Official Meeting with CHED Officials',
            'Community Service and Outreach Program',
            'Office Supplies and Equipment Pickup',
            'Student Educational Field Trip and Campus Tour',
            'Inter-school Academic Competition',
        ];

        $employeeNames = [
            'Maria Santos', 'John Doe', 'Sarah Concepcion', 'Michael Angelo', 
            'Patricia Lim', 'David Guerrero', 'Jessica Alba', 'Robert de Castro',
            'Angela Reyes', 'Christopher Cruz'
        ];

        $passengersPool = [
            ['Maria Santos', 'Juan Dela Cruz', 'Anna Gonzales'],
            ['John Doe', 'Jane Smith', 'Peter Parker'],
            ['Sarah Concepcion', 'Jose Rizal', 'Andres Bonifacio'],
            ['Michael Angelo', 'Leonardo da Vinci', 'Raphael Sanzio'],
            ['Patricia Lim', 'Kevin Tan', 'Grace Poe'],
            ['David Guerrero', 'Manny Pacquiao', 'Lea Salonga'],
        ];

        // Reset all driver statuses first
        Driver::query()->update(['status' => 'available']);

        echo "Generating 30 test records...\n";

        for ($i = 1; $i <= 30; $i++) {
            $reqNum = 'REQ-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            // Distribute statuses:
            // 1-6: Pending
            // 7-12: Approved (Trip Ticket Pending)
            // 13-18: On Trip (Trip Ticket Active)
            // 19-24: Completed (Trip Ticket Completed)
            // 25-30: Rejected (No Trip Ticket)
            if ($i <= 6) {
                $status = 'pending';
                $date = Carbon::now()->addDays(rand(1, 5))->format('Y-m-d');
            } elseif ($i <= 12) {
                $status = 'approved';
                $date = Carbon::now()->addDays(rand(1, 5))->format('Y-m-d');
            } elseif ($i <= 18) {
                $status = 'on_trip';
                $date = Carbon::now()->format('Y-m-d');
            } elseif ($i <= 24) {
                $status = 'completed';
                $date = Carbon::now()->subDays(rand(1, 10))->format('Y-m-d');
            } else {
                $status = 'rejected';
                $date = Carbon::now()->addDays(rand(1, 5))->format('Y-m-d');
            }

            $vehicle = $vehicles[array_rand($vehicles)];
            $dept = $departments[array_rand($departments)];
            $dest = $destinations[array_rand($destinations)];
            $purpose = $purposes[array_rand($purposes)];
            $empName = $employeeNames[array_rand($employeeNames)];
            $passengers = $passengersPool[array_rand($passengersPool)];

            // Create Vehicle Request
            $request = VehicleRequest::create([
                'request_number' => $reqNum,
                'user_id' => $userId,
                'vehicle' => $vehicle,
                'employee_name' => $empName,
                'department' => $dept,
                'destination' => $dest,
                'purpose' => $purpose,
                'description' => 'Automatically generated test record #' . $i . ' for system simulation.',
                'date' => $date,
                'time' => '08:00:00',
                'return_date' => $date,
                'return_time' => '17:00:00',
                'number_of_passengers' => count($passengers),
                'passenger_names' => $passengers,
                'status' => $status,
            ]);

            // Create corresponding Trip Ticket if approved, on_trip, or completed
            if (in_array($status, ['approved', 'on_trip', 'completed'])) {
                $ttNum = 'TT-' . str_pad(TripTicket::count() + 1, 5, '0', STR_PAD_LEFT);
                $driver = $drivers->random();
                
                if ($status === 'approved') {
                    $ttStatus = 'pending';
                } elseif ($status === 'on_trip') {
                    $ttStatus = 'active';
                    // Mark driver as busy/on_trip
                    $driver->update(['status' => 'on_trip']);
                } else {
                    $ttStatus = 'completed';
                }

                $ticket = TripTicket::create([
                    'ticket_number' => $ttNum,
                    'vehicle_request_id' => $request->id,
                    'driver_id' => $driver->id,
                    'vehicle' => $vehicle,
                    'status' => $ttStatus,
                ]);

                // Create a Withdrawal Slip for completed or on_trip tickets
                if (in_array($status, ['on_trip', 'completed'])) {
                    $wsNum = 'WS-' . str_pad(WithdrawalSlip::count() + 1, 5, '0', STR_PAD_LEFT);
                    WithdrawalSlip::create([
                        'slip_number' => $wsNum,
                        'trip_ticket_id' => $ticket->id,
                        'purpose' => 'Fuel Refill & Toll Fees',
                        'requested_items' => "1. Refuel 30 Liters Diesel\n2. Tollway RFID Load",
                        'status' => $status === 'completed' ? 'approved' : 'pending',
                    ]);
                }
            }
        }

        echo "Successfully seeded 30 test records!\n";
    }
}
