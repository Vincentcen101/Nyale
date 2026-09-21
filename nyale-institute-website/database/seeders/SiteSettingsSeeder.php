<?php

namespace Database\Seeders;

use App\Models\AboutSetting;
use App\Models\ContactSetting;
use App\Models\PartnerLogo;
use App\Models\SEOSetting;
use App\Models\SocialMediaSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $about = [
            [
                'section' => 'who_we_are',
                'title' => 'Who We Are',
                'content' => 'Nyale Institute for Sexual and Reproductive Health Governance is a Malawian institution advancing sexual and reproductive justice through law, evidence, advocacy and community action. Over the years we have built deep experience in strategic litigation, policy engagement, research and community empowerment on sexual and reproductive health and rights (SRHR).',
                'order' => 1,
            ],
            [
                'section' => 'our_story',
                'title' => 'Our Story',
                'content' => "Nyale Institute was founded in 2013. \"Nyale\" is vernacular for 'lamp', signifying a source of enlightenment. Since 2013, Nyale Institute has grown into a reliable institution providing counsel on sexual and reproductive justice, accumulating significant experience, partnerships, knowledge products and programme achievements along the way.",
                'order' => 2,
            ],
            [
                'section' => 'vision',
                'title' => 'Our Vision',
                'content' => 'A society where every person fully enjoys their sexual and reproductive health and rights.',
                'order' => 3,
            ],
            [
                'section' => 'mission',
                'title' => 'Our Mission',
                'content' => 'To advance sexual and reproductive health governance in Malawi through strategic litigation, policy and legal advocacy, research and evidence generation, and community and youth empowerment.',
                'order' => 4,
            ],
            [
                'section' => 'core_values',
                'title' => 'Our Core Values',
                'content' => "Social Justice\nCommon Good\nIntegrity\nAccountability\nEvidence-Based Action",
                'order' => 5,
            ],
            [
                'section' => 'leadership',
                'title' => 'Leadership',
                'content' => 'Nyale Institute is led by an experienced Executive Director and governed by a dedicated Board of Directors who bring decades of combined experience in law, public health, human rights and development.',
                'order' => 6,
            ],
        ];

        foreach ($about as $section) {
            AboutSetting::updateOrCreate(['section' => $section['section']], $section + ['is_active' => true]);
        }

        $contact = [
            ['type' => 'address', 'label' => 'Address', 'value' => "Chipatala Avenue\nNext to Blantyre Youth Centre\nP.O. Box 30860, Chichiri\nBlantyre 3, Malawi", 'order' => 1],
            ['type' => 'phone', 'label' => 'Phone', 'value' => '+265 212 953 373', 'order' => 2],
            ['type' => 'email', 'label' => 'Email', 'value' => 'info@nyaleinstitute.org.mw', 'order' => 3],
        ];

        foreach ($contact as $c) {
            ContactSetting::updateOrCreate(['type' => $c['type']], $c + ['is_active' => true]);
        }

        $socials = [
            ['platform' => 'facebook', 'url' => 'https://facebook.com/nyaleinstitute'],
            ['platform' => 'instagram', 'url' => 'https://instagram.com/nyaleinstitute'],
            ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/nyaleinstitute'],
            ['platform' => 'twitter', 'url' => 'https://x.com/nyaleinstitute'],
            ['platform' => 'youtube', 'url' => 'https://youtube.com/@nyaleinstitute'],
        ];

        foreach ($socials as $i => $s) {
            SocialMediaSetting::updateOrCreate(['platform' => $s['platform']], $s + ['is_active' => true, 'order' => $i]);
        }

        SEOSetting::updateOrCreate(['page' => 'global'], [
            'meta_title' => 'Nyale Institute — Advancing Sexual & Reproductive Health Governance',
            'meta_description' => 'Nyale Institute is a Malawian institution advancing sexual and reproductive justice through law, evidence, advocacy and community action.',
        ]);

        $partners = ['Marie Stopes International', 'Center for Reproductive Rights', 'ForEquality', 'RAES', 'Amplify Change'];
        foreach ($partners as $i => $name) {
            PartnerLogo::updateOrCreate(['name' => $name], ['order' => $i, 'is_active' => true]);
        }
    }
}
