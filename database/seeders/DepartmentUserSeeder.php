<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DepartmentUserSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'ceo' => 'Office of the CEO (Campus Executive Officer)',
            'hrmo' => 'HRMO (Human Resource Management Office)',
            'accounting' => 'Accounting Office',
            'budget' => 'Budget Office',
            'property' => 'Property and Supply Office',
            'records' => 'Records Office',
            'planning' => 'Planning Office',
            'mis' => 'MIS Office (Management Information System / System Admin)',
            'registrar' => 'Office of the Campus Registrar',
            'admission' => 'Campus Admission Office',
            'publication' => 'Campus Publication Office',
            'library' => 'University Library',
            'cics' => 'CICS (College of Information and Computing Sciences)',
            'cte' => 'CTE (College of Teacher Education)',
            'chm' => 'CHM (College of Hospitality Management)',
            'coa' => 'COA (College of Agriculture)',
            'cafevalena' => 'Café Valena (CoffeeHub Café)',
            'csc' => 'Campus Student Council (CSC)'
        ];

        // Clean up previous wrong seeded employee accounts
        User::where('role', 'employee')->where('email', 'like', '%@csu.edu.ph')->delete();

        foreach ($departments as $code => $name) {
            $email = $code . '@csu.edu.ph';
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($code),
                'role' => 'employee',
            ]);
        }
    }
}
