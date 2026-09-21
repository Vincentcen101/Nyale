<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\ImpactStat;
use App\Models\ImpactStory;
use App\Models\KnowledgeResource;
use App\Models\Post;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\WorkArea;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $createdBy = $admin?->id;

        $workAreas = [
            [
                'title' => 'Strategic Litigation',
                'icon' => 'scale',
                'summary' => 'Using the courts to defend and expand access to sexual and reproductive health and rights.',
                'body' => 'Nyale Institute pursues strategic litigation to challenge unlawful denial of sexual and reproductive health services, secure landmark rulings, and set precedent that protects the rights of women, girls and other vulnerable groups across Malawi.',
            ],
            [
                'title' => 'Policy and Legal Advocacy',
                'icon' => 'document-text',
                'summary' => 'Engaging government and policymakers to strengthen SRHR laws, policies and guidelines.',
                'body' => 'We work directly with government institutions, parliamentary committees and policymakers to advocate for laws and policies that guarantee sexual and reproductive health and rights, and to ensure existing frameworks are properly implemented.',
            ],
            [
                'title' => 'Research and Evidence',
                'icon' => 'chart-bar',
                'summary' => 'Generating credible, locally grounded evidence to inform advocacy, litigation and policy.',
                'body' => 'Our research programme produces studies, issue papers and evidence briefs that document the state of sexual and reproductive health governance in Malawi, informing our own programming and that of partners and government.',
            ],
            [
                'title' => 'Community Empowerment',
                'icon' => 'users',
                'summary' => 'Equipping communities with knowledge and tools to claim and defend their SRHR.',
                'body' => 'We work with communities across Malawi to build awareness of sexual and reproductive health rights, strengthen local structures for accountability, and support communities to access services with dignity.',
            ],
            [
                'title' => 'Youth Engagement',
                'icon' => 'academic-cap',
                'summary' => 'Building the next generation of young SRHR champions and leaders.',
                'body' => 'Nyale Institute runs youth-focused programmes that build knowledge, leadership and agency among young people to advocate for their own sexual and reproductive health and rights.',
            ],
            [
                'title' => 'Communications and Public Advocacy',
                'icon' => 'megaphone',
                'summary' => 'Shaping public discourse and awareness on sexual and reproductive health governance.',
                'body' => 'Through media engagement, public campaigns and strategic communications, we raise awareness on SRHR issues and direct public attention to the evidence, policy positions and campaigns that Nyale Institute leads or supports.',
            ],
        ];

        foreach ($workAreas as $i => $w) {
            WorkArea::updateOrCreate(
                ['slug' => Str::slug($w['title'])],
                $w + ['order' => $i, 'is_active' => true, 'created_by' => $createdBy]
            );
        }

        $stats = [
            ['label' => 'Years of Impact', 'value' => '12+', 'icon' => 'calendar'],
            ['label' => 'Strategic Litigation Cases', 'value' => '50+', 'icon' => 'scale'],
            ['label' => 'Community Members Reached', 'value' => '10,000+', 'icon' => 'users'],
            ['label' => 'Policy & Advocacy Engagements', 'value' => '25+', 'icon' => 'document-text'],
        ];
        foreach ($stats as $i => $s) {
            ImpactStat::updateOrCreate(['label' => $s['label']], $s + ['order' => $i, 'is_active' => true]);
        }

        $stories = [
            [
                'title' => 'AC (Minor) v Solomon Jenala, Blantyre District Council, Attorney General and Malawi Human Rights Commission',
                'category' => 'litigation_outcome',
                'summary' => 'Nyale Institute supported litigation on behalf of a minor survivor of sexual violence against the Blantyre District Council (Chileka Health Centre), the Attorney General and the Malawi Human Rights Commission.',
                'body' => 'This case forms part of Nyale Institute\'s strategic litigation portfolio addressing access to justice for survivors of sexual violence and gaps in the provision of sexual and reproductive health services at public health facilities.',
                'published_at' => '2022-06-24',
                'is_featured' => true,
            ],
            [
                'title' => 'The State (on the application of HM, Guardian, on behalf of CM) v The Hospital Director of Queen Elizabeth Central Hospital and the Minister of Health',
                'category' => 'litigation_outcome',
                'summary' => 'Nyale Institute, through KK Attorneys and Women and Law in Southern Africa (WLSA), facilitated access to the High Court for an adolescent survivor of sexual violence challenging denial of legal termination of pregnancy.',
                'body' => 'Judicial Review Case Number 03 of 2021 (High Court of Malawi, Zomba District Registry) challenged the hospital\'s decision not to provide a legal termination of pregnancy to an adolescent survivor of sexual violence, highlighting the grey areas around access to safe abortion in Malawi.',
                'published_at' => '2021-05-10',
                'is_featured' => true,
            ],
        ];
        foreach ($stories as $s) {
            ImpactStory::updateOrCreate(
                ['slug' => Str::slug($s['title']) . '-' . substr(md5($s['title']), 0, 6)],
                $s + ['is_active' => true, 'created_by' => $createdBy]
            );
        }

        $resources = [
            ['title' => 'Strategic Litigation Handbook for SRHR Practitioners', 'type' => 'advocacy_manual', 'description' => 'A practical guide for lawyers and paralegals pursuing sexual and reproductive health and rights cases in Malawian courts.'],
            ['title' => 'State of SRHR Governance in Malawi — Issues Paper', 'type' => 'issues_paper', 'description' => 'An overview of the legal, policy and institutional landscape shaping sexual and reproductive health governance in Malawi.'],
            ['title' => 'Access to Safe Abortion: Policy Brief', 'type' => 'policy_brief', 'description' => 'Key recommendations for policymakers on closing gaps in access to safe and legal abortion services.'],
            ['title' => 'Nyale Institute Annual Programme Report', 'type' => 'programme_report', 'description' => 'A summary of Nyale Institute\'s programme activities, reach and outcomes.'],
        ];
        foreach ($resources as $i => $r) {
            KnowledgeResource::updateOrCreate(
                ['title' => $r['title']],
                $r + ['is_active' => true, 'published_at' => now()->subMonths($i + 1), 'created_by' => $createdBy]
            );
        }

        Campaign::updateOrCreate(
            ['slug' => 'empowering-futures-advancing-srhr'],
            [
                'title' => 'Empowering Futures: Advancing SRHR',
                'summary' => 'A national campaign championing sexual and reproductive health and rights for young people and vulnerable communities across Malawi.',
                'body' => 'Empowering Futures brings together evidence, advocacy and community voices to push for stronger protection and access to sexual and reproductive health services for all Malawians.',
                'status' => 'active',
                'cta_label' => 'Join the Campaign',
                'cta_url' => '/get-involved',
                'is_active' => true,
                'published_at' => now()->subMonth(),
                'created_by' => $createdBy,
            ]
        );

        $posts = [
            [
                'title' => 'Nyale Institute Booklet',
                'category' => 'news',
                'excerpt' => 'As part of the project to advance sexual and reproductive health and rights, Nyale Institute has published a new institutional booklet.',
                'body' => 'The Nyale Institute Booklet documents our programmes, achievements and strategic direction, and is available to partners, donors and the public on request.',
                'published_at' => now()->subWeeks(2),
            ],
            [
                'title' => 'Legal and Safe Abortion for the Vulnerable Girl: A Call for Social Justice',
                'category' => 'blog',
                'excerpt' => 'Abortion is provided to save life according to approved Ministry of Health guidelines. In Malawi, health providers are empowered to provide safe services within the law — yet access remains uneven.',
                'body' => "Abortion is provided to save life according to the approved Ministry of Health guidelines. In Malawi, health providers are empowered to provide safe abortion services within the bounds of the law, yet many vulnerable girls and women continue to face barriers to access.\n\nNyale Institute continues to advocate for clear guidance, trained providers and accountable institutions so that every survivor of sexual violence can access safe, legal and dignified care.",
                'published_at' => now()->subMonths(3),
            ],
        ];
        foreach ($posts as $p) {
            Post::updateOrCreate(
                ['slug' => Str::slug($p['title'])],
                $p + ['is_active' => true, 'created_by' => $createdBy]
            );
        }

        $board = [
            'Davie Stephen Kavinya',
            'Grace Mtambo',
            'Chikondi Phiri',
            'Esther Banda',
            'Ronwell Harawa',
        ];
        foreach ($board as $i => $name) {
            TeamMember::updateOrCreate(
                ['name' => $name, 'type' => 'board'],
                ['role' => $i === 0 ? 'Board Chair' : 'Board Member', 'order' => $i, 'is_active' => true, 'created_by' => $createdBy]
            );
        }

        $staff = [
            ['name' => 'Executive Director', 'role' => 'Executive Director'],
            ['name' => 'Programmes Manager', 'role' => 'Head of Programmes'],
            ['name' => 'Legal Officer', 'role' => 'Strategic Litigation Lead'],
        ];
        foreach ($staff as $i => $s) {
            TeamMember::updateOrCreate(
                ['name' => $s['name'], 'type' => 'staff'],
                $s + ['order' => $i, 'is_active' => true, 'created_by' => $createdBy]
            );
        }
    }
}
