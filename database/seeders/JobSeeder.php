<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'name');

        $jobs = [
            [
                'category' => 'Tax Preparation',
                'title' => 'Prepare and File 2025 Corporate Tax Return',
                'company_name' => 'Brightpath Logistics',
                'company_about' => 'A mid-sized freight company operating across three states with ~80 employees.',
                'short_description' => 'Prepare and e-file our annual corporate tax return for FY2025.',
                'description' => "We need an experienced tax accountant to prepare and file our corporate income tax return for the 2025 fiscal year.\n\nYou will review our trial balance, identify allowable deductions, prepare supporting schedules, and e-file with the relevant authorities. Prior experience with logistics or transport companies is a plus.",
                'required_skills' => ['Corporate Tax', 'Tax Filing', 'GAAP', 'Excel'],
                'budget_min' => 1200, 'budget_max' => 2500, 'delivery_days' => 14, 'days_to_deadline' => 30, 'status' => 'open',
            ],
            [
                'category' => 'Bookkeeping',
                'title' => 'Monthly Bookkeeping & Bank Reconciliation (QuickBooks)',
                'company_name' => 'Maple & Co. Interiors',
                'company_about' => 'Boutique interior design studio with a steady flow of project-based invoices.',
                'short_description' => 'Ongoing monthly bookkeeping and reconciliation in QuickBooks Online.',
                'description' => "Looking for a reliable bookkeeper to manage our monthly books in QuickBooks Online.\n\nScope: categorising transactions, reconciling two bank accounts and one credit card, managing accounts payable/receivable, and producing a month-end summary. Roughly 120 transactions per month.",
                'required_skills' => ['QuickBooks', 'Bank Reconciliation', 'Accounts Payable'],
                'budget_min' => 400, 'budget_max' => 900, 'delivery_days' => 7, 'days_to_deadline' => 12, 'status' => 'open',
            ],
            [
                'category' => 'Auditing',
                'title' => 'Internal Controls Review for SaaS Startup',
                'company_name' => 'Nimbus Analytics',
                'company_about' => 'Series-A SaaS company preparing for its first external audit.',
                'short_description' => 'Review and document internal financial controls ahead of our first audit.',
                'description' => "We are preparing for our first external audit and need help reviewing and documenting our internal controls.\n\nDeliverables: a controls matrix, identified gaps with remediation recommendations, and a short readiness report for our board.",
                'required_skills' => ['Auditing', 'Internal Controls', 'IFRS'],
                'budget_min' => 2000, 'budget_max' => 4500, 'delivery_days' => 21, 'days_to_deadline' => 45, 'status' => 'open',
            ],
            [
                'category' => 'Payroll',
                'title' => 'Set Up Payroll for 25-Person Team',
                'company_name' => 'Cedar Health Clinic',
                'company_about' => 'Growing private clinic that recently crossed 25 staff members.',
                'short_description' => 'Configure payroll, statutory deductions and payslip templates.',
                'description' => "We've outgrown our manual payroll process and need a professional to set up a clean, compliant payroll system.\n\nIncludes employee records, tax and statutory deductions, payslip templates, and a documented monthly run process our office manager can follow.",
                'required_skills' => ['Payroll', 'Compliance', 'Excel'],
                'budget_min' => 700, 'budget_max' => 1600, 'delivery_days' => 10, 'days_to_deadline' => 20, 'status' => 'open',
            ],
            [
                'category' => 'Financial Reporting',
                'title' => 'Year-End Financial Statements Preparation',
                'company_name' => 'Orchard Retail Group',
                'company_about' => 'Multi-store retailer needing IFRS-compliant year-end statements.',
                'short_description' => 'Prepare IFRS-compliant year-end financial statements.',
                'description' => "Prepare our year-end financial statements (balance sheet, P&L, cash flow, and notes) in line with IFRS.\n\nWe will provide the adjusted trial balance and supporting documents. Clear, presentation-ready output is essential as these go to our lenders.",
                'required_skills' => ['Financial Reporting', 'IFRS', 'Excel'],
                'budget_min' => 1500, 'budget_max' => 3200, 'delivery_days' => 15, 'days_to_deadline' => 28, 'status' => 'open',
            ],
            [
                'category' => 'VAT & Compliance',
                'title' => 'Quarterly VAT Return Review & Submission',
                'company_name' => 'Harbour Imports Ltd',
                'company_about' => 'Import/export business with cross-border VAT complexity.',
                'short_description' => 'Review and submit our quarterly VAT return with cross-border entries.',
                'description' => "We need a VAT specialist to review our quarterly return, validate cross-border transactions, and submit on our behalf.\n\nAttention to reverse-charge and zero-rated items is critical given our import/export activity.",
                'required_skills' => ['VAT', 'Compliance', 'Tax Filing'],
                'budget_min' => 500, 'budget_max' => 1100, 'delivery_days' => 6, 'days_to_deadline' => 10, 'status' => 'open',
            ],
            [
                'category' => 'Management Accounting',
                'title' => 'Build a 12-Month Cash Flow Forecast Model',
                'company_name' => 'Volt Mobility',
                'company_about' => 'Early-stage e-mobility startup planning its next funding round.',
                'short_description' => 'Create a driver-based 12-month cash flow forecast in Excel.',
                'description' => "We need a robust, driver-based 12-month cash flow forecast model in Excel to support fundraising.\n\nThe model should be clearly structured, easy to update, and include scenario toggles (base / best / worst case).",
                'required_skills' => ['Forecasting', 'Excel', 'Financial Modelling'],
                'budget_min' => 900, 'budget_max' => 2200, 'delivery_days' => 12, 'days_to_deadline' => 18, 'status' => 'open',
            ],
            [
                'category' => 'Forensic Accounting',
                'title' => 'Investigate Discrepancies in Vendor Payments',
                'company_name' => 'Stonebridge Construction',
                'company_about' => 'Construction firm that flagged unexplained variances in vendor accounts.',
                'short_description' => 'Trace and document discrepancies across 18 months of vendor payments.',
                'description' => "We've identified unexplained variances in our vendor payment records and need a forensic review.\n\nTrace transactions across 18 months, identify and document discrepancies, and prepare a findings report suitable for internal review.",
                'required_skills' => ['Forensic Accounting', 'Auditing', 'Data Analysis'],
                'budget_min' => 2500, 'budget_max' => 6000, 'delivery_days' => 25, 'days_to_deadline' => 40, 'status' => 'open',
            ],
            [
                'category' => 'Bookkeeping',
                'title' => 'Clean Up 6 Months of Backlogged Books',
                'company_name' => 'Field & Forage Cafe',
                'company_about' => 'Independent cafe that fell behind on bookkeeping during a busy period.',
                'short_description' => 'Catch up and reconcile six months of neglected bookkeeping.',
                'description' => "Our books are six months behind and need a full clean-up.\n\nReconcile accounts, fix mis-categorised transactions, and hand back a clean set of books with a short note on what was corrected.",
                'required_skills' => ['Xero', 'Bank Reconciliation', 'Bookkeeping'],
                'budget_min' => 600, 'budget_max' => 1400, 'delivery_days' => 9, 'days_to_deadline' => 15, 'status' => 'closed',
            ],
            [
                'category' => 'Tax Preparation',
                'title' => 'Personal Tax Returns for Two Company Directors',
                'company_name' => 'Lumen Design Studio',
                'company_about' => 'Design agency whose directors need personal returns filed.',
                'short_description' => 'Prepare and file personal tax returns for two directors.',
                'description' => "Prepare and file personal income tax returns for our two company directors, including dividend and salary income.\n\nWe can provide all income documents; we just need an accurate, on-time filing.",
                'required_skills' => ['Personal Tax', 'Tax Filing'],
                'budget_min' => 300, 'budget_max' => 700, 'delivery_days' => 8, 'days_to_deadline' => 22, 'status' => 'open',
            ],
            [
                'category' => 'Financial Reporting',
                'title' => 'Monthly Management Reporting Pack',
                'company_name' => 'Pinnacle Fitness Group',
                'company_about' => 'Chain of fitness studios wanting clearer monthly reporting for owners.',
                'short_description' => 'Design and produce a monthly management reporting pack.',
                'description' => "We want a clean, repeatable monthly management reporting pack: P&L by location, KPIs, and a one-page commentary.\n\nFirst engagement includes designing the template; ongoing months are a lighter lift.",
                'required_skills' => ['Management Accounting', 'Excel', 'Reporting'],
                'budget_min' => 800, 'budget_max' => 1800, 'delivery_days' => 11, 'days_to_deadline' => 16, 'status' => 'open',
            ],
            [
                'category' => 'Auditing',
                'title' => 'Grant Expenditure Audit for Nonprofit',
                'company_name' => 'Riverside Community Trust',
                'company_about' => 'Nonprofit required to audit how a restricted grant was spent.',
                'short_description' => 'Audit restricted grant expenditure and prepare a compliance report.',
                'description' => "We received a restricted grant and our funder requires an independent audit of how it was spent.\n\nReview supporting documents, confirm eligible expenditure, and produce a compliance report in the funder's required format.",
                'required_skills' => ['Auditing', 'Compliance', 'Nonprofit Accounting'],
                'budget_min' => 1200, 'budget_max' => 2800, 'delivery_days' => 18, 'days_to_deadline' => 35, 'status' => 'open',
            ],
        ];

        foreach ($jobs as $data) {
            Job::create([
                'category_id' => $categories[$data['category']],
                'title' => $data['title'],
                'company_name' => $data['company_name'],
                'company_about' => $data['company_about'],
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'required_skills' => $data['required_skills'],
                'budget_min' => $data['budget_min'],
                'budget_max' => $data['budget_max'],
                'delivery_days' => $data['delivery_days'],
                'deadline' => Carbon::now()->addDays($data['days_to_deadline']),
                'attachments' => [],
                'status' => $data['status'],
            ]);
        }
    }
}
