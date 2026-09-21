<?php

namespace Database\Seeders;

use App\Models\CourtCase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CaseTrackerSeeder extends Seeder
{
    public function run(): void
    {
        $cases = [
            [
                'title' => 'AC (Minor) suing through a litigation guardian Mr. CJ vs Mr Solomon Jenala, Blantyre District Council (Chileka Health Centre), Attorney General and Malawi Human Rights Commission',
                'lawyer' => 'Ronwell Harawa',
                'court' => 'High Court of Malawi',
                'status' => 'concluded',
                'case_date' => '2022-06-24',
                'summary' => 'Nyale Institute and CHRR facilitated access to the High Court for a 14 year old survivor of sexual violence, suing the Ministry of Health and other parties for failing to provide her with a safe and legal abortion.',
            ],
            [
                'title' => 'The State (On the Application by HM (Guardian) on behalf of CM (Minor)) vs The Hospital Director of Queens Elizabeth Central Hospital and the Minister of Health',
                'case_number' => 'Judicial Review Case Number 03 of 2021',
                'court' => 'High Court of Malawi, Zomba District Registry',
                'status' => 'concluded',
                'case_date' => '2021-05-10',
                'summary' => 'Nyale Institute, through KK Attorneys and Women and Law in Southern Africa (WLSA), facilitated access to the High Court for an adolescent girl survivor of sexual violence who challenged the decisions of the hospital to provide her with legal termination of pregnancy.',
            ],
        ];

        foreach ($cases as $case) {
            CourtCase::firstOrCreate(
                ['slug' => Str::slug(Str::limit($case['title'], 80, '')) . '-' . substr(md5($case['title']), 0, 5)],
                $case + ['is_active' => true]
            );
        }
    }
}
