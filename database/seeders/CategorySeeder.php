<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tax Preparation', 'description' => 'Income tax returns, filings and tax planning.'],
            ['name' => 'Bookkeeping', 'description' => 'Day-to-day recording and reconciliation of transactions.'],
            ['name' => 'Auditing', 'description' => 'Internal and external audit and assurance work.'],
            ['name' => 'Payroll', 'description' => 'Payroll processing, payslips and statutory deductions.'],
            ['name' => 'Financial Reporting', 'description' => 'Preparation of financial statements and reports.'],
            ['name' => 'VAT & Compliance', 'description' => 'VAT returns, sales tax and regulatory compliance.'],
            ['name' => 'Management Accounting', 'description' => 'Budgeting, forecasting and cost analysis.'],
            ['name' => 'Forensic Accounting', 'description' => 'Fraud investigation and litigation support.'],
        ];

        foreach ($categories as $category) {
            Category::create([
                ...$category,
                'slug' => Str::slug($category['name']),
            ]);
        }
    }
}
