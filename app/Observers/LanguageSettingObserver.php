<?php

namespace App\Observers;

use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Models\SuperAdmin\Feature;
use App\Models\SuperAdmin\FrontFaq;
use App\Models\SuperAdmin\FrontMenu;
use App\Models\SuperAdmin\SeoDetail;
use App\Models\SuperAdmin\FooterMenu;
use App\Models\SuperAdmin\FrontClients;
use App\Models\SuperAdmin\FrontFeature;
use App\Models\SuperAdmin\Testimonials;
use App\Models\SuperAdmin\TrFrontDetail;
use App\Observers\SuperAdmin\FooterMenuObserver;

class LanguageSettingObserver
{

    public function saving(LanguageSetting $model)
    {
        cache()->forget('language_setting');
        cache()->forget('language_setting_' . $model->language_code);

        return $model;
    }

    public function updated(LanguageSetting $model)
    {
        cache()->forget('language_setting');
        cache()->forget('language_setting_' . $model->language_code);

        return $model;
    }

    public function deleted(LanguageSetting $model)
    {
        cache()->forget('language_setting');
        cache()->forget('language_setting_' . $model->language_code);

        return $model;
    }

    // WORKSUITESAAS
    public function saved(LanguageSetting $languageSetting)
    {

        if ($languageSetting->status == 'enabled') {

            if (TrFrontDetail::where('language_setting_id', $languageSetting->id)->first()) {
                return true;
            }

            $this->detail($languageSetting->id);
            $this->features($languageSetting->id);
            $this->footer($languageSetting->id);
            $this->seoDetail($languageSetting->id);
            $this->frontClients($languageSetting->id);
            $this->testimonials($languageSetting->id);
            $this->frontFaq($languageSetting->id);
            $this->frontFeature($languageSetting->id);
            $this->frontMenu($languageSetting->id);
        }
    }

    public function detail($languageId)
    {
        $trFrontDetail = new TrFrontDetail();
        $trFrontDetail->language_setting_id = $languageId;

        $trFrontDetail->task_management_title = 'Task Management';
        $trFrontDetail->task_management_detail = 'Manage your projects and talent in one system for empowered teams, satisfied clients, and increased profitability.';

        $trFrontDetail->manage_bills_title = 'Manage All Bills';
        $trFrontDetail->manage_bills_detail = 'Automate billing and revenue recognition to streamline the contract-to-cash cycle.';

        $trFrontDetail->favourite_apps_title = 'Integrate with Favorite Apps';
        $trFrontDetail->favourite_apps_detail = 'Our app integrates with other third-party apps for added advantage.';

        $trFrontDetail->cta_title = 'Easier Business Management';
        $trFrontDetail->cta_detail = 'Our experts will show you how our app can streamline your team’s work.';

        $trFrontDetail->client_title = 'Trusted by the World’s Best Teams';
        $trFrontDetail->client_detail = 'Over 700 people use our product.';

        $trFrontDetail->testimonial_title = 'Loved by Businesses and Individuals Worldwide';
        $trFrontDetail->faq_title = 'FAQs';
        $trFrontDetail->footer_copyright_text = 'Copyright © 2020. All Rights Reserved';

        $trFrontDetail->header_title = 'HR, CRM, and Project Management System';
        $trFrontDetail->header_description = 'The simplest and most powerful way to collaborate with your team.';

        $trFrontDetail->feature_title = 'Team Communication for the 21st Century';
        $trFrontDetail->price_title = 'Affordable Pricing';
        $trFrontDetail->price_description = 'Worksuite for Teams is a single workspace for your small- to medium-sized company or team.';

        $trFrontDetail->save();

    }

    public function features($languageId)
    {
        $features = [
            [
                'language_setting_id' => $languageId,
                'title' => 'Meet Your Business Needs',
                'description' => '<p>Manage your projects and your talent in a single system, resulting in empowered teams, satisfied clients, and increased profitability.</p><ul class="list1 border-top pt-5 mt-5">
                            <li class="mb-3">
                                Keep a track of all your projects in most simple way.
                            </li>
                            <li class="mb-3">
                                Assign tasks to project members and track the status.
                            </li>
                            <li class="mb-3">
                                Add members to your projects and keep them in sync  with the progress.
                            </li>
                        </ul>',
                'type' => 'image',
            ],
            [
                'language_setting_id' => $languageId,
                'title' => 'Analyse Your Workflow',
                'description' => "<p>Reports section to analyse what's working and what's not for your business</p><ul class=\"list1 border-top pt-5 mt-5\">
                            <li class=\"mb-3\">
                                It Shows how much you earned and how much you spent.
                            </li>
                            <li class=\"mb-3\">
                                Ticket report shows you Open vs Closed tickets.
                            </li>
                            <li class=\"mb-3\">
                                It creates task report to track completed vs pending tasks.
                            </li>
                        </ul>",
                'type' => 'image',
            ],
            [
                'language_setting_id' => $languageId,
                'title' => 'Manage your support tickets efficiently',
                'description' => '<p>Whether someone\'s internet is not working, someone is facing issue with housekeeping or need something regarding their work they can raise a ticket for all their problems.</p><ul class="list1 border-top pt-5 mt-5"><li class="mb-3">Admin can assign the tickets to respective department agents.</li></ul>',
                'type' => 'image',
            ],
        ];

        Feature::insert($features);

        $features = [
            [
                'title' => 'Responsive',
                'language_setting_id' => $languageId,
                'description' => 'Your website works on any device: desktop, tablet or mobile.',
                'icon' => 'fas fa-desktop',
                'type' => 'icon'
            ],
            [
                'title' => 'Customizable',
                'language_setting_id' => $languageId,
                'description' => 'You can easily read, edit, and write your own code, or change everything.',
                'icon' => 'fas fa-wrench',
                'type' => 'icon'
            ],
            [
                'title' => 'UI Elements',
                'language_setting_id' => $languageId,
                'description' => 'There is a bunch of useful and necessary elements for developing your website.',
                'icon' => 'fas fa-cubes',
                'type' => 'icon'
            ],
            [
                'title' => 'Clean Code',
                'language_setting_id' => $languageId,
                'description' => 'You can find our code well organized, commented and readable.',
                'icon' => 'fas fa-code',
                'type' => 'icon'],
            [
                'title' => 'Documented',
                'language_setting_id' => $languageId,
                'description' => 'As you can see in the source code, we provided a comprehensive documentation.',
                'icon' => 'far fa-file-alt',
                'type' => 'icon'],
            [
                'title' => 'Free Updates',
                'language_setting_id' => $languageId,
                'description' => "When you purchase this template, you'll freely receive future updates.",
                'icon' => 'fas fa-download',
                'type' => 'icon'],
            [
                'title' => 'Track Projects',
                'language_setting_id' => $languageId,
                'description' => 'Keep a track of all your projects in the most simple way.',
                'icon' => 'fas fa-desktop',
                'type' => 'task'
            ],
            [
                'title' => 'Add Members',
                'language_setting_id' => $languageId,
                'description' => 'Add members to your projects and keep them in sync with the progress.',
                'icon' => 'fas fa-users',
                'type' => 'task'
            ],
            [
                'title' => 'Assign Tasks',
                'language_setting_id' => $languageId,
                'description' => 'Your website is fully responsive, it will work on any device, desktop, tablet and mobile.',
                'icon' => 'fas fa-list',
                'type' => 'task'
            ],
            [
                'title' => 'Estimates',
                'language_setting_id' => $languageId,
                'description' => 'Create estimates how much project can cost and send to your clients.',
                'icon' => 'fas fa-calculator',
                'type' => 'bills'
            ],
            [
                'title' => 'Invoices',
                'language_setting_id' => $languageId,
                'description' => 'Simple and professional invoices can be download in form of PDF.',
                'icon' => 'far fa-file-alt',
                'type' => 'bills'
            ],
            [
                'title' => 'Payments',
                'language_setting_id' => $languageId,
                'description' => 'Track payments done by clients in the payment section.',
                'icon' => 'fas fa-money-bill-alt',
                'type' => 'bills',
            ],
            [
                'title' => 'Tickets',
                'language_setting_id' => $languageId,
                'description' => 'When someone is facing a problem, they can raise a ticket for their problems. Admin can assign the tickets to respective department agents.',
                'icon' => 'fas fa-ticket-alt',
                'type' => 'team',
            ],
            [
                'title' => 'Leaves',
                'language_setting_id' => $languageId,
                'description' => 'Employees can apply for the multiple leaves from their panel. Admin can approve or reject the leave applications.',
                'icon' => 'fas fa-ban',
                'type' => 'team',
            ],
            [
                'title' => 'Attendance',
                'language_setting_id' => $languageId,
                'description' => 'Attendance module allows employees to clock-in and clock-out, right from their dashboard. Admin can track the attendance of the team.',
                'icon' => 'far fa-check-circle',
                'type' => 'team',
            ],
        ];

        Feature::insert($features);

        // Application Section
        $features = [
            ['title' => 'OneSignal', 'type' => 'apps', 'language_setting_id' => $languageId],
            ['title' => 'Slack', 'type' => 'apps', 'language_setting_id' => $languageId],
            ['title' => 'Paypal', 'type' => 'apps', 'language_setting_id' => $languageId],
            ['title' => 'Pusher', 'type' => 'apps', 'language_setting_id' => $languageId],
        ];


        Feature::insert($features);

    }

    public function frontClients($languageId)
    {
        $clients = [
            ['title' => 'Client 1', 'language_setting_id' => $languageId],
            ['title' => 'Client 2', 'language_setting_id' => $languageId],
            ['title' => 'Client 3', 'language_setting_id' => $languageId],
            ['title' => 'Client 4', 'language_setting_id' => $languageId]
        ];

        FrontClients::insert($clients);

    }

    public function testimonials($languageId)
    {
        $testimonials = [
            [
                'name' => 'theon salvatore',
                'language_setting_id' => $languageId,
                'comment' => 'The application is user-friendly and has made my work easier.',
                'rating' => 5
            ],
            [
                'name' => 'jenna gilbert',
                'language_setting_id' => $languageId,
                'comment' => 'The application is efficient and has improved my productivity.',
                'rating' => 4
            ],
            [
                'name' => 'Redh gilbert',
                'language_setting_id' => $languageId,
                'comment' => 'The application has a good design and is easy to navigate.',
                'rating' => 3
            ],
            [
                'name' => 'whatson angela',
                'language_setting_id' => $languageId,
                'comment' => 'The application has made my life much easier by automating my tasks.',
                'rating' => 2
            ],
            [
                'name' => 'Megan Lee',
                'language_setting_id' => $languageId,
                'comment' => 'I was skeptical at first, but after using the application for a few weeks, I\'m sold! It\'s incredibly intuitive and user-friendly.',
                'rating' => 5
            ],
            [
                'name' => 'Jacob Thompson',
                'language_setting_id' => $languageId,
                'comment' => 'I\'ve tried a lot of productivity tools, but this one stands out. It has all the features I need and none of the clutter.',
                'rating' => 4
            ],
            [
                'name' => 'Maria Rodriguez',
                'language_setting_id' => $languageId,
                'comment' => 'As a small business owner, this application has been a game-changer. It saves me so much time and keeps me organized.',
                'rating' => 5
            ],
            [
                'name' => 'Samantha Patel',
                'language_setting_id' => $languageId,
                'comment' => 'I appreciate the attention to detail in the design of this application. It\'s clear that the developers put a lot of thought into the user experience.',
                'rating' => 4
            ],
            [
                'name' => 'Ethan Kim',
                'language_setting_id' => $languageId,
                'comment' => 'Overall, I\'m happy with the application. It\'s not perfect, but it gets the job done.',
                'rating' => 3
            ]
        ];

        Testimonials::insert($testimonials);

    }

    public function frontFaq($languageId)
    {
        // Front FAQ

        $client = [
            [
                'question' => 'Can i see demo?',
                'language_setting_id' => $languageId,
                'answer' => '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px;">Yes, definitely. We would be happy to demonstrate you Worksuite through a web conference at your convenience. Please submit a query on our contact us page or drop a mail to our mail id worksuite@froiden.com.</span>'
            ],
            [
                'question' => 'How can i update app?',
                'language_setting_id' => $languageId,
                'answer' => '<span style="color: rgb(68, 68, 68); font-family: Lato, sans-serif; font-size: 16px;">Yes, definitely. We would be happy to demonstrate you Worksuite through a web conference at your convenience. Please submit a query on our contact us page or drop a mail to our mail id worksuite@froiden.com.</span>'
            ],

        ];

        FrontFaq::insert($client);
    }

    public function frontFeature($languageId)
    {
        $frontDetail = TrFrontDetail::where('language_setting_id', $languageId)->first();

        if (!$frontDetail) {
            return true;
        }


        $features = Feature::all();

        $allTaskFeature = $features->filter(function ($value, $key) {
            return $value->type == 'task';
        });

        $allBillsFeature = $features->filter(function ($value, $key) {
            return $value->type == 'bills';
        });

        $allTeamatesFeature = $features->filter(function ($value, $key) {
            return $value->type == 'team';
        });


        $frontFeature = new FrontFeature();
        $frontFeature->title = $frontDetail->task_management_title;
        $frontFeature->description = $frontDetail->task_management_detail;
        $frontFeature->language_setting_id = $frontDetail->language_setting_id;
        $frontFeature->save();

        foreach ($allTaskFeature as $taskFeature) {
            $taskFeature->front_feature_id = $frontFeature->id;
            $taskFeature->save();
        }

        $frontFeature = new FrontFeature();
        $frontFeature->title = $frontDetail->manage_bills_title;
        $frontFeature->description = $frontDetail->manage_bills_detail;
        $frontFeature->language_setting_id = $frontDetail->language_setting_id;
        $frontFeature->save();

        foreach ($allBillsFeature as $billFeature) {
            $billFeature->front_feature_id = $frontFeature->id;
            $billFeature->save();
        }

        $frontFeature = new FrontFeature();
        $frontFeature->title = $frontDetail->teamates_title;
        $frontFeature->description = $frontDetail->teamates_detail;
        $frontFeature->language_setting_id = $frontDetail->language_setting_id;
        $frontFeature->save();

        foreach ($allTeamatesFeature as $teamatesFeature) {
            $teamatesFeature->front_feature_id = $frontFeature->id;
            $teamatesFeature->save();
        }
    }

    public function footer($languageId)
    {

        $enLang = LanguageSetting::where('language_code', 'en')->first();
        $footerMenu = FooterMenu::where('language_setting_id', $enLang->id)->get();

        foreach ($footerMenu as $menu) {
            (new FooterMenuObserver())->createFooterMenu($menu, $languageId);
        }

        if(count($footerMenu) > 0){
            return true;
        }

        $menu = [
            [
                'name' => 'Terms of use',
                'slug' => str_slug('Terms of use'),
                'language_setting_id' => $languageId,
                'description' => "<div><b><span style=\"font-size: 14px;\"><span style=\"font-size: 14px;\">TERMS OF USE FOR WORKSUITE.BIZ</span></span></b></div><div><br></div><div>The use of any product, service or feature (the \"Materials\") available through the internet web sites accessible at Worksuite.com (the \"Web Site\") by any user of the Web Site (\"You\" or \"Your\" hereafter) shall be governed by the following terms of use:</div><div>This Web Site is provided by Worksuite, a partnership awaiting registration with Government of India, and shall be used for informational purposes only. By using the Web Site or downloading Materials from the Web Site, You hereby agree to abide by the terms and conditions set forth in this Terms of Use. In the event of You not agreeing to these terms and conditions, You are requested by Worksuite not to use the Web Site or download Materials from the Web Site. This Web Site, including all Materials present (excluding any applicable third party materials), is the property of Worksuite and is copyrighted and protected by worldwide copyright laws and treaty provisions. You hereby agree to comply with all copyright laws worldwide in Your use of this Web Site and to prevent any unauthorized copying of the Materials. Worksuite does not grant any express or implied rights under any patents, trademarks, copyrights or trade secret information.</div><div>Worksuite has business relationships with many customers, suppliers, governments, and others. For convenience and simplicity, words like joint venture, partnership, and partner are used to indicate business relationships involving common activities and interests, and those words may not indicate precise legal relationships.</div><div><br></div><div><b><span style=\"font-size: 14px;\">LIMITED LICENSE:</span></b></div><div><br></div><div>Subject to the terms and conditions set forth in these Terms of Use, Worksuite grants You a non-exclusive, non-transferable, limited right to access, use and display this Web Site and the Materials thereon. You agree not to interrupt or attempt to interrupt the operation of the Web Site in any manner. Unless otherwise specified, the Web Site is for Your personal and non-commercial use. You shall not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from this Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">THIRD PARTY CONTENT</span></b></div><div>The Web Site makes information of third parties available, including articles, analyst reports, news reports, and company information, including any regulatory authority, content licensed under Content Licensed under Creative Commons Attribution License, and other data from external sources (the \"Third Party Content\"). You acknowledge and agree that the Third Party Content is not created or endorsed by Worksuite. The provision of Third Party Content is for general informational purposes only and does not constitute a recommendation or solicitation to purchase or sell any securities or shares or to make any other type of investment or investment decision. In addition, the Third Party Content is not intended to provide tax, legal or investment advice. You acknowledge that the Third Party Content provided to You is obtained from sources believed to be reliable, but that no guarantees are made by Worksuite or the providers of the Third Party Content as to its accuracy, completeness, timeliness. You agree not to hold Worksuite, any business offering products or services through the Web Site or any provider of Third Party Content liable for any investment decision or other transaction You may make based on Your reliance on or use of such data, or any liability that may arise due to delays or interruptions in the delivery of the Third Party Content for any reason</div><div>By using any Third Party Content, You may leave this Web Site and be directed to an external website, or to a website maintained by an entity other than Worksuite. If You decide to visit any such site, You do so at Your own risk and it is Your responsibility to take all protective measures to guard against viruses or any other destructive elements. Worksuite makes no warranty or representation regarding and does not endorse, any linked web sites or the information appearing thereon or any of the products or services described thereon. Links do not imply that Worksuite or this Web Site sponsors, endorses, is affiliated or associated with, or is legally authorized to use any trademark, trade name, logo or copyright symbol displayed in or accessible through the links, or that any linked site is authorized to use any trademark, trade name, logo or copyright symbol of Worksuite or any of its affiliates or subsidiaries. You hereby expressly acknowledge and agree that the linked sites are not under the control of Worksuite and Worksuite is not responsible for the contents of any linked site or any link contained in a linked site, or any changes or updates to such sites. Worksuite is not responsible for webcasting or any other form of transmission received from any linked site. Worksuite is providing these links to You only as a convenience, and the inclusion of any link shall not be construed to imply endorsement by Worksuite in any manner of the website.</div><div><br></div><div><br></div><div><b><span style=\"font-size: 14px;\">NO WARRANTIES</span></b></div><div>THIS WEB SITE, THE INFORMATION AND MATERIALS ON THE SITE, AND ANY SOFTWARE MADE AVAILABLE ON THE WEB SITE, ARE PROVIDED \"AS IS\" WITHOUT ANY REPRESENTATION OR WARRANTY, EXPRESS OR IMPLIED, OF ANY KIND, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY, NON INFRINGEMENT, OR FITNESS FOR ANY PARTICULAR PURPOSE. THERE IS NO WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, REGARDING THIRD PARTY CONTENT. INSPITE OF FROIDEN BEST ENDEAVOURS, THERE IS NO WARRANTY ON BEHALF OF FROIDEN THAT THIS WEB SITE WILL BE FREE OF ANY COMPUTER VIRUSES. SOME JURISDICTIONS DO NOT ALLOW FOR THE EXCLUSION OF IMPLIED WARRANTIES, SO THE ABOVE EXCLUSIONS MAY NOT APPLY TO YOU.</div><div>LIMITATION OF DAMAGES:</div><div>IN NO EVENT SHALL FROIDEN OR ANY OF ITS SUBSIDIARIES OR AFFILIATES BE LIABLE TO ANY ENTITY FOR ANY DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL OR OTHER DAMAGES (INCLUDING, WITHOUT LIMITATION, ANY LOST PROFITS, BUSINESS INTERRUPTION, LOSS OF INFORMATION OR PROGRAMS OR OTHER DATA ON YOUR INFORMATION HANDLING SYSTEM) THAT ARE RELATED TO THE USE OF, OR THE INABILITY TO USE, THE CONTENT, MATERIALS, AND FUNCTIONS OF THIS WEB SITE OR ANY LINKED WEB SITE, EVEN IF FROIDEN IS EXPRESSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</div><div><br></div><div><b><span style=\"font-size: 14px;\">DISCLAIMER:</span></b></div><div>THE WEB SITE MAY CONTAIN INACCURACIES AND TYPOGRAPHICAL AND CLERICAL ERRORS. FROIDEN EXPRESSLY DISCLAIMS ANY OBLIGATION(S) TO UPDATE THIS WEBSITE OR ANY OF THE MATERIALS ON THIS WEBSITE. FROIDEN DOES NOT WARRANT THE ACCURACY OR COMPLETENESS OF THE MATERIALS OR THE RELIABILITY OF ANY ADVICE, OPINION, STATEMENT OR OTHER INFORMATION DISPLAYED OR DISTRIBUTED THROUGH THE WEB SITE. YOU ACKNOWLEDGE THAT ANY RELIANCE ON ANY SUCH OPINION, ADVICE, STATEMENT, MEMORANDUM, OR INFORMATION SHALL BE AT YOUR SOLE RISK. FROIDEN RESERVES THE RIGHT, IN ITS SOLE DISCRETION, TO CORRECT ANY ERRORS OR OMISSIONS IN ANY PORTION OF THE WEB SITE. FROIDEN MAY MAKE ANY OTHER CHANGES TO THE WEB SITE, THE MATERIALS AND THE PRODUCTS, PROGRAMS, SERVICES OR PRICES (IF ANY) DESCRIBED IN THE WEB SITE AT ANY TIME WITHOUT NOTICE. THIS WEB SITE IS FOR INFORMATIONAL PURPOSES ONLY AND SHOULD NOT BE CONSTRUED AS TECHNICAL ADVICE OF ANY MANNER.</div><div>UNLAWFUL AND/OR PROHIBITED USE OF THE WEB SITE</div><div>As a condition of Your use of the Web Site, You shall not use the Web Site for any purpose(s) that is unlawful or prohibited by the Terms of Use. You shall not use the Web Site in any manner that could damage, disable, overburden, or impair any Worksuite server, or the network(s) connected to any Worksuite server, or interfere with any other party's use and enjoyment of any services associated with the Web Site. You shall not attempt to gain unauthorized access to any section of the Web Site, other accounts, computer systems or networks connected to any Worksuite server or to any of the services associated with the Web Site, through hacking, password mining or any other means. You shall not obtain or attempt to obtain any Materials or information through any means not intentionally made available through the Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">INDEMNITY:</span></b></div><div>You agree to indemnify and hold harmless Worksuite, its subsidiaries and affiliates from any claim, cost, expense, judgment or other loss relating to Your use of this Web Site in any manner, including without limitation of the foregoing, any action You take which is in violation of the terms and conditions of these Terms of Use and against any applicable law.</div><div><br></div><div><b><span style=\"font-size: 14px;\">CHANGES:</span></b></div><div>Worksuite reserves the rights, at its sole discretion, to change, modify, add or remove any portion of these Terms of Use in whole or in part, at any time. Changes in these Terms of Use will be effective when notice of such change is posted. Your continued use of the Web Site after any changes to these Terms of Use are posted will be considered acceptance of those changes. Worksuite may terminate, change, suspend or discontinue any aspect of the Web Site, including the availability of any feature(s) of the Web Site, at any time. Worksuite may also impose limits on certain features and services or restrict Your access to certain sections or all of the Web Site without notice or liability. You hereby acknowledge and agree that Worksuite may terminate the authorization, rights, and license given above at any point of time at its own sole discretion, and upon such termination; You shall immediately destroy all Materials.</div><div><br></div><div><br></div><div><b><span style=\"font-size: 14px;\">INTERNATIONAL USERS AND CHOICE OF LAW:</span></b></div><div>This Site is controlled, operated, and administered by Worksuite from within India. Worksuite makes no representation that Materials on this Web Site are appropriate or available for use at any other location(s) outside India. Any access to this Web Site from territories where their contents are illegal is prohibited. You may not use the Web Site or export the Materials in violation of any applicable export laws and regulations. If You access this Web Site from a location outside India, You are responsible for compliance with all local laws.</div><div>These Terms of Use shall be governed by the laws of India,Terms of Use for worksuite.biz</div><div>The use of any product, service or feature (the \"Materials\") available through the internet web sites accessible at Worksuite.com (the \"Web Site\") by any user of the Web Site (\"You\" or \"Your\" hereafter) shall be governed by the following terms of use:</div><div>This Web Site is provided by Worksuite, a partnership awaiting registration with Government of India, and shall be used for informational purposes only. By using the Web Site or downloading Materials from the Web Site, You hereby agree to abide by the terms and conditions set forth in this Terms of Use. In the event of You not agreeing to these terms and conditions, You are requested by Worksuite not to use the Web Site or download Materials from the Web Site. This Web Site, including all Materials present (excluding any applicable third party materials), is the property of Worksuite and is copyrighted and protected by worldwide copyright laws and treaty provisions. You hereby agree to comply with all copyright laws worldwide in Your use of this Web Site and to prevent any unauthorized copying of the Materials. Worksuite does not grant any express or implied rights under any patents, trademarks, copyrights or trade secret information.</div><div>Worksuite has business relationships with many customers, suppliers, governments, and others. For convenience and simplicity, words like joint venture, partnership, and partner are used to indicate business relationships involving common activities and interests, and those words may not indicate precise legal relationships.</div><div><br></div><div><b><span style=\"font-size: 14px;\">LIMITED LICENSE:</span></b></div><div>Subject to the terms and conditions set forth in these Terms of Use, Worksuite grants You a non-exclusive, non-transferable, limited right to access, use and display this Web Site and the Materials thereon. You agree not to interrupt or attempt to interrupt the operation of the Web Site in any manner. Unless otherwise specified, the Web Site is for Your personal and non-commercial use. You shall not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from this Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">THIRD-PARTY CONTENT</span></b></div><div>The Web Site makes information of third parties available, including articles, analyst reports, news reports, and company information, including any regulatory authority, content licensed under Content Licensed under Creative Commons Attribution License, and other data from external sources (the \"Third Party Content\"). You acknowledge and agree that the Third Party Content is not created or endorsed by Worksuite. The provision of Third Party Content is for general informational purposes only and does not constitute a recommendation or solicitation to purchase or sell any securities or shares or to make any other type of investment or investment decision. In addition, the Third Party Content is not intended to provide tax, legal or investment advice. You acknowledge that the Third Party Content provided to You is obtained from sources believed to be reliable, but that no guarantees are made by Worksuite or the providers of the Third Party Content as to its accuracy, completeness, timeliness. You agree not to hold Worksuite, any business offering products or services through the Web Site or any provider of Third Party Content liable for any investment decision or other transaction You may make based on Your reliance on or use of such data, or any liability that may arise due to delays or interruptions in the delivery of the Third Party Content for any reason</div><div>By using any Third Party Content, You may leave this Web Site and be directed to an external website, or to a website maintained by an entity other than Worksuite. If You decide to visit any such site, You do so at Your own risk and it is Your responsibility to take all protective measures to guard against viruses or any other destructive elements. Worksuite makes no warranty or representation regarding, and does not endorse, any linked web sites or the information appearing thereon or any of the products or services described thereon. Links do not imply that Worksuite or this Web Site sponsors, endorses, is affiliated or associated with, or is legally authorized to use any trademark, trade name, logo or copyright symbol displayed in or accessible through the links, or that any linked site is authorized to use any trademark, trade name, logo or copyright symbol of Worksuite or any of its affiliates or subsidiaries. You hereby expressly acknowledge and agree that the linked sites are not under the control of Worksuite and Worksuite is not responsible for the contents of any linked site or any link contained in a linked site, or any changes or updates to such sites. Worksuite is not responsible for webcasting or any other form of transmission received from any linked site. Worksuite is providing these links to You only as a convenience, and the inclusion of any link shall not be construed to imply endorsement by Worksuite in any manner of the website.</div><div><br></div><div><b><span style=\"font-size: 14px;\">NO WARRANTIES</span></b></div><div>THIS WEB SITE, THE INFORMATION AND MATERIALS ON THE SITE, AND ANY SOFTWARE MADE AVAILABLE ON THE WEB SITE, ARE PROVIDED \"AS IS\" WITHOUT ANY REPRESENTATION OR WARRANTY, EXPRESS OR IMPLIED, OF ANY KIND, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY, NON INFRINGEMENT, OR FITNESS FOR ANY PARTICULAR PURPOSE. THERE IS NO WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, REGARDING THIRD PARTY CONTENT. INSPITE OF FROIDEN BEST ENDEAVOURS, THERE IS NO WARRANTY ON BEHALF OF FROIDEN THAT THIS WEB SITE WILL BE FREE OF ANY COMPUTER VIRUSES. SOME JURISDICTIONS DO NOT ALLOW FOR THE EXCLUSION OF IMPLIED WARRANTIES, SO THE ABOVE EXCLUSIONS MAY NOT APPLY TO YOU.</div><div>LIMITATION OF DAMAGES:</div><div>IN NO EVENT SHALL FROIDEN OR ANY OF ITS SUBSIDIARIES OR AFFILIATES BE LIABLE TO ANY ENTITY FOR ANY DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL OR OTHER DAMAGES (INCLUDING, WITHOUT LIMITATION, ANY LOST PROFITS, BUSINESS INTERRUPTION, LOSS OF INFORMATION OR PROGRAMS OR OTHER DATA ON YOUR INFORMATION HANDLING SYSTEM) THAT ARE RELATED TO THE USE OF, OR THE INABILITY TO USE, THE CONTENT, MATERIALS, AND FUNCTIONS OF THIS WEB SITE OR ANY LINKED WEB SITE, EVEN IF FROIDEN IS EXPRESSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</div><div><br></div><div><b><span style=\"font-size: 14px;\">DISCLAIMER:</span></b></div><div><span style=\"font-size: 12px;\">THE WEB SITE MAY CONTAIN INACCURACIES AND TYPOGRAPHICAL AND CLERICAL ERRORS. FROIDEN EXPRESSLY DISCLAIMS ANY OBLIGATION(S) TO UPDATE THIS WEBSITE OR ANY OF THE MATERIALS ON THIS WEBSITE. FROIDEN DOES NOT WARRANT THE ACCURACY OR COMPLETENESS OF THE MATERIALS OR THE RELIABILITY OF ANY ADVICE, OPINION, STATEMENT OR OTHER INFORMATION DISPLAYED OR DISTRIBUTED THROUGH THE WEB SITE. YOU ACKNOWLEDGE THAT ANY RELIANCE ON ANY SUCH OPINION, ADVICE, STATEMENT, MEMORANDUM, OR INFORMATION SHALL BE AT YOUR SOLE RISK. FROIDEN RESERVES THE RIGHT, IN ITS SOLE DISCRETION, TO CORRECT ANY ERRORS OR OMISSIONS IN ANY PORTION OF THE WEB SITE. FROIDEN MAY MAKE ANY OTHER CHANGES TO THE WEB SITE, THE MATERIALS AND THE PRODUCTS, PROGRAMS, SERVICES OR PRICES (IF ANY) DESCRIBED IN THE WEB SITE AT ANY TIME WITHOUT NOTICE. THIS WEB SITE IS FOR INFORMATIONAL PURPOSES ONLY AND SHOULD NOT BE CONSTRUED AS TECHNICAL ADVICE OF ANY MANNER.</span></div><div><span style=\"font-size: 12px;\">UNLAWFUL AND/OR PROHIBITED USE OF THE WEB SITE</span></div><div>As a condition of Your use of the Web Site, You shall not use the Web Site for any purpose(s) that is unlawful or prohibited by the Terms of Use. You shall not use the Web Site in any manner that could damage, disable, overburden, or impair any Worksuite server, or the network(s) connected to any Worksuite server, or interfere with any other party's use and enjoyment of any services associated with the Web Site. You shall not attempt to gain unauthorized access to any section of the Web Site, other accounts, computer systems or networks connected to any Worksuite server or to any of the services associated with the Web Site, through hacking, password mining or any other means. You shall not obtain or attempt to obtain any materials or information through any means not intentionally made available through the Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">INDEMNITY:</span></b></div><div>You agree to indemnify and hold harmless Worksuite, its subsidiaries and affiliates from any claim, cost, expense, judgment or other loss relating to Your use of this Web Site in any manner, including without limitation of the foregoing, any action You take which is in violation of the terms and conditions of these Terms of Use and against any applicable law.</div><div><br></div><div><b><span style=\"font-size: 14px;\">CHANGES:</span></b></div><div>Worksuite reserves the rights, at its sole discretion, to change, modify, add or remove any portion of these Terms of Use in whole or in part, at any time. Changes in these Terms of Use will be effective when notice of such change is posted. Your continued use of the Web Site after any changes to these Terms of Use are posted will be considered acceptance of those changes. Worksuite may terminate, change, suspend or discontinue any aspect of the Web Site, including the availability of any feature(s) of the Web Site, at any time. Worksuite may also impose limits on certain features and services or restrict Your access to certain sections or all of the Web Site without notice or liability. You hereby acknowledge and agree that Worksuite may terminate the authorization, rights, and license given above at any point of time at its own sole discretion, and upon such termination; You shall immediately destroy all Materials.</div><div><br></div><div><b><span style=\"font-size: 14px;\">INTERNATIONAL USERS AND CHOICE OF LAW:</span></b></div><div>This Site is controlled, operated, and administered by Worksuite from within India. Worksuite makes no representation that Materials on this Web Site are appropriate or available for use at any other location(s) outside India. Any access to this Web Site from territories where their contents are illegal is prohibited. You may not use the Web Site or export the Materials in violation of any applicable export laws and regulations. If You access this Web Site from a location outside India, You are responsible for compliance with all local laws.</div><div>These Terms of Use shall be governed by the laws of India, without giving effect to its conflict of laws provisions. You agree that the appropriate court(s) in Bangalore, India, will have the exclusive jurisdiction to resolve all disputes arising under these Terms of Use and You hereby consent to personal jurisdiction in such forum.</div><div>These Terms of Use constitute the entire agreement between Worksuite and You with respect to Your use of the Web Site. Any claim You may have with respect to Your use of the Web Site must be commenced within one (1) year of the cause of action. If any provision(s) of this Terms of Use is held by a court of competent jurisdiction to be contrary to law then such provision(s) shall be severed from this Terms of Use and the other remaining provisions of this Terms of Use shall remain in full force and effect. without giving effect to its conflict of laws provisions. You agree that the appropriate court(s) in Bangalore, India, will have the exclusive jurisdiction to resolve all disputes arising under these Terms of Use and You hereby consent to personal jurisdiction in such forum.</div><div>These Terms of Use constitute the entire agreement between Worksuite and You with respect to Your use of the Web Site. Any claim You may have with respect to Your use of the Web Site must be commenced within one (1) year of the cause of action. If any provision(s) of this Terms of Use is held by a court of competent jurisdiction to be contrary to law then such provision(s) shall be severed from this Terms of Use and the other remaining provisions of this Terms of Use shall remain in full force and effect.</div>"
            ],
            [
                'name' => 'Privacy Policy',
                'slug' => str_slug('Privacy Policy'),
                'language_setting_id' => $languageId,
                'description' => "<div><b><span style=\"font-size: 14px;\"><span style=\"font-size: 14px;\">TERMS OF USE FOR WORKSUITE.BIZ</span></span></b></div><div><br></div><div>The use of any product, service or feature (the \"Materials\") available through the internet web sites accessible at Worksuite.com (the \"Web Site\") by any user of the Web Site (\"You\" or \"Your\" hereafter) shall be governed by the following terms of use:</div><div>This Web Site is provided by Worksuite, a partnership awaiting registration with Government of India, and shall be used for informational purposes only. By using the Web Site or downloading Materials from the Web Site, You hereby agree to abide by the terms and conditions set forth in this Terms of Use. In the event of You not agreeing to these terms and conditions, You are requested by Worksuite not to use the Web Site or download Materials from the Web Site. This Web Site, including all Materials present (excluding any applicable third party materials), is the property of Worksuite and is copyrighted and protected by worldwide copyright laws and treaty provisions. You hereby agree to comply with all copyright laws worldwide in Your use of this Web Site and to prevent any unauthorized copying of the Materials. Worksuite does not grant any express or implied rights under any patents, trademarks, copyrights or trade secret information.</div><div>Worksuite has business relationships with many customers, suppliers, governments, and others. For convenience and simplicity, words like joint venture, partnership, and partner are used to indicate business relationships involving common activities and interests, and those words may not indicate precise legal relationships.</div><div><br></div><div><b><span style=\"font-size: 14px;\">LIMITED LICENSE:</span></b></div><div><br></div><div>Subject to the terms and conditions set forth in these Terms of Use, Worksuite grants You a non-exclusive, non-transferable, limited right to access, use and display this Web Site and the Materials thereon. You agree not to interrupt or attempt to interrupt the operation of the Web Site in any manner. Unless otherwise specified, the Web Site is for Your personal and non-commercial use. You shall not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from this Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">THIRD PARTY CONTENT</span></b></div><div>The Web Site makes information of third parties available, including articles, analyst reports, news reports, and company information, including any regulatory authority, content licensed under Content Licensed under Creative Commons Attribution License, and other data from external sources (the \"Third Party Content\"). You acknowledge and agree that the Third Party Content is not created or endorsed by Worksuite. The provision of Third Party Content is for general informational purposes only and does not constitute a recommendation or solicitation to purchase or sell any securities or shares or to make any other type of investment or investment decision. In addition, the Third Party Content is not intended to provide tax, legal or investment advice. You acknowledge that the Third Party Content provided to You is obtained from sources believed to be reliable, but that no guarantees are made by Worksuite or the providers of the Third Party Content as to its accuracy, completeness, timeliness. You agree not to hold Worksuite, any business offering products or services through the Web Site or any provider of Third Party Content liable for any investment decision or other transaction You may make based on Your reliance on or use of such data, or any liability that may arise due to delays or interruptions in the delivery of the Third Party Content for any reason</div><div>By using any Third Party Content, You may leave this Web Site and be directed to an external website, or to a website maintained by an entity other than Worksuite. If You decide to visit any such site, You do so at Your own risk and it is Your responsibility to take all protective measures to guard against viruses or any other destructive elements. Worksuite makes no warranty or representation regarding and does not endorse, any linked web sites or the information appearing thereon or any of the products or services described thereon. Links do not imply that Worksuite or this Web Site sponsors, endorses, is affiliated or associated with, or is legally authorized to use any trademark, trade name, logo or copyright symbol displayed in or accessible through the links, or that any linked site is authorized to use any trademark, trade name, logo or copyright symbol of Worksuite or any of its affiliates or subsidiaries. You hereby expressly acknowledge and agree that the linked sites are not under the control of Worksuite and Worksuite is not responsible for the contents of any linked site or any link contained in a linked site, or any changes or updates to such sites. Worksuite is not responsible for webcasting or any other form of transmission received from any linked site. Worksuite is providing these links to You only as a convenience, and the inclusion of any link shall not be construed to imply endorsement by Worksuite in any manner of the website.</div><div><br></div><div><br></div><div><b><span style=\"font-size: 14px;\">NO WARRANTIES</span></b></div><div>THIS WEB SITE, THE INFORMATION AND MATERIALS ON THE SITE, AND ANY SOFTWARE MADE AVAILABLE ON THE WEB SITE, ARE PROVIDED \"AS IS\" WITHOUT ANY REPRESENTATION OR WARRANTY, EXPRESS OR IMPLIED, OF ANY KIND, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY, NON INFRINGEMENT, OR FITNESS FOR ANY PARTICULAR PURPOSE. THERE IS NO WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, REGARDING THIRD PARTY CONTENT. INSPITE OF FROIDEN BEST ENDEAVOURS, THERE IS NO WARRANTY ON BEHALF OF FROIDEN THAT THIS WEB SITE WILL BE FREE OF ANY COMPUTER VIRUSES. SOME JURISDICTIONS DO NOT ALLOW FOR THE EXCLUSION OF IMPLIED WARRANTIES, SO THE ABOVE EXCLUSIONS MAY NOT APPLY TO YOU.</div><div>LIMITATION OF DAMAGES:</div><div>IN NO EVENT SHALL FROIDEN OR ANY OF ITS SUBSIDIARIES OR AFFILIATES BE LIABLE TO ANY ENTITY FOR ANY DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL OR OTHER DAMAGES (INCLUDING, WITHOUT LIMITATION, ANY LOST PROFITS, BUSINESS INTERRUPTION, LOSS OF INFORMATION OR PROGRAMS OR OTHER DATA ON YOUR INFORMATION HANDLING SYSTEM) THAT ARE RELATED TO THE USE OF, OR THE INABILITY TO USE, THE CONTENT, MATERIALS, AND FUNCTIONS OF THIS WEB SITE OR ANY LINKED WEB SITE, EVEN IF FROIDEN IS EXPRESSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</div><div><br></div><div><b><span style=\"font-size: 14px;\">DISCLAIMER:</span></b></div><div>THE WEB SITE MAY CONTAIN INACCURACIES AND TYPOGRAPHICAL AND CLERICAL ERRORS. FROIDEN EXPRESSLY DISCLAIMS ANY OBLIGATION(S) TO UPDATE THIS WEBSITE OR ANY OF THE MATERIALS ON THIS WEBSITE. FROIDEN DOES NOT WARRANT THE ACCURACY OR COMPLETENESS OF THE MATERIALS OR THE RELIABILITY OF ANY ADVICE, OPINION, STATEMENT OR OTHER INFORMATION DISPLAYED OR DISTRIBUTED THROUGH THE WEB SITE. YOU ACKNOWLEDGE THAT ANY RELIANCE ON ANY SUCH OPINION, ADVICE, STATEMENT, MEMORANDUM, OR INFORMATION SHALL BE AT YOUR SOLE RISK. FROIDEN RESERVES THE RIGHT, IN ITS SOLE DISCRETION, TO CORRECT ANY ERRORS OR OMISSIONS IN ANY PORTION OF THE WEB SITE. FROIDEN MAY MAKE ANY OTHER CHANGES TO THE WEB SITE, THE MATERIALS AND THE PRODUCTS, PROGRAMS, SERVICES OR PRICES (IF ANY) DESCRIBED IN THE WEB SITE AT ANY TIME WITHOUT NOTICE. THIS WEB SITE IS FOR INFORMATIONAL PURPOSES ONLY AND SHOULD NOT BE CONSTRUED AS TECHNICAL ADVICE OF ANY MANNER.</div><div>UNLAWFUL AND/OR PROHIBITED USE OF THE WEB SITE</div><div>As a condition of Your use of the Web Site, You shall not use the Web Site for any purpose(s) that is unlawful or prohibited by the Terms of Use. You shall not use the Web Site in any manner that could damage, disable, overburden, or impair any Worksuite server, or the network(s) connected to any Worksuite server, or interfere with any other party's use and enjoyment of any services associated with the Web Site. You shall not attempt to gain unauthorized access to any section of the Web Site, other accounts, computer systems or networks connected to any Worksuite server or to any of the services associated with the Web Site, through hacking, password mining or any other means. You shall not obtain or attempt to obtain any Materials or information through any means not intentionally made available through the Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">INDEMNITY:</span></b></div><div>You agree to indemnify and hold harmless Worksuite, its subsidiaries and affiliates from any claim, cost, expense, judgment or other loss relating to Your use of this Web Site in any manner, including without limitation of the foregoing, any action You take which is in violation of the terms and conditions of these Terms of Use and against any applicable law.</div><div><br></div><div><b><span style=\"font-size: 14px;\">CHANGES:</span></b></div><div>Worksuite reserves the rights, at its sole discretion, to change, modify, add or remove any portion of these Terms of Use in whole or in part, at any time. Changes in these Terms of Use will be effective when notice of such change is posted. Your continued use of the Web Site after any changes to these Terms of Use are posted will be considered acceptance of those changes. Worksuite may terminate, change, suspend or discontinue any aspect of the Web Site, including the availability of any feature(s) of the Web Site, at any time. Worksuite may also impose limits on certain features and services or restrict Your access to certain sections or all of the Web Site without notice or liability. You hereby acknowledge and agree that Worksuite may terminate the authorization, rights, and license given above at any point of time at its own sole discretion, and upon such termination; You shall immediately destroy all Materials.</div><div><br></div><div><br></div><div><b><span style=\"font-size: 14px;\">INTERNATIONAL USERS AND CHOICE OF LAW:</span></b></div><div>This Site is controlled, operated, and administered by Worksuite from within India. Worksuite makes no representation that Materials on this Web Site are appropriate or available for use at any other location(s) outside India. Any access to this Web Site from territories where their contents are illegal is prohibited. You may not use the Web Site or export the Materials in violation of any applicable export laws and regulations. If You access this Web Site from a location outside India, You are responsible for compliance with all local laws.</div><div>These Terms of Use shall be governed by the laws of India,Terms of Use for worksuite.biz</div><div>The use of any product, service or feature (the \"Materials\") available through the internet web sites accessible at Worksuite.com (the \"Web Site\") by any user of the Web Site (\"You\" or \"Your\" hereafter) shall be governed by the following terms of use:</div><div>This Web Site is provided by Worksuite, a partnership awaiting registration with Government of India, and shall be used for informational purposes only. By using the Web Site or downloading Materials from the Web Site, You hereby agree to abide by the terms and conditions set forth in this Terms of Use. In the event of You not agreeing to these terms and conditions, You are requested by Worksuite not to use the Web Site or download Materials from the Web Site. This Web Site, including all Materials present (excluding any applicable third party materials), is the property of Worksuite and is copyrighted and protected by worldwide copyright laws and treaty provisions. You hereby agree to comply with all copyright laws worldwide in Your use of this Web Site and to prevent any unauthorized copying of the Materials. Worksuite does not grant any express or implied rights under any patents, trademarks, copyrights or trade secret information.</div><div>Worksuite has business relationships with many customers, suppliers, governments, and others. For convenience and simplicity, words like joint venture, partnership, and partner are used to indicate business relationships involving common activities and interests, and those words may not indicate precise legal relationships.</div><div><br></div><div><b><span style=\"font-size: 14px;\">LIMITED LICENSE:</span></b></div><div>Subject to the terms and conditions set forth in these Terms of Use, Worksuite grants You a non-exclusive, non-transferable, limited right to access, use and display this Web Site and the Materials thereon. You agree not to interrupt or attempt to interrupt the operation of the Web Site in any manner. Unless otherwise specified, the Web Site is for Your personal and non-commercial use. You shall not modify, copy, distribute, transmit, display, perform, reproduce, publish, license, create derivative works from, transfer, or sell any information, software, products or services obtained from this Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">THIRD-PARTY CONTENT</span></b></div><div>The Web Site makes information of third parties available, including articles, analyst reports, news reports, and company information, including any regulatory authority, content licensed under Content Licensed under Creative Commons Attribution License, and other data from external sources (the \"Third Party Content\"). You acknowledge and agree that the Third Party Content is not created or endorsed by Worksuite. The provision of Third Party Content is for general informational purposes only and does not constitute a recommendation or solicitation to purchase or sell any securities or shares or to make any other type of investment or investment decision. In addition, the Third Party Content is not intended to provide tax, legal or investment advice. You acknowledge that the Third Party Content provided to You is obtained from sources believed to be reliable, but that no guarantees are made by Worksuite or the providers of the Third Party Content as to its accuracy, completeness, timeliness. You agree not to hold Worksuite, any business offering products or services through the Web Site or any provider of Third Party Content liable for any investment decision or other transaction You may make based on Your reliance on or use of such data, or any liability that may arise due to delays or interruptions in the delivery of the Third Party Content for any reason</div><div>By using any Third Party Content, You may leave this Web Site and be directed to an external website, or to a website maintained by an entity other than Worksuite. If You decide to visit any such site, You do so at Your own risk and it is Your responsibility to take all protective measures to guard against viruses or any other destructive elements. Worksuite makes no warranty or representation regarding, and does not endorse, any linked web sites or the information appearing thereon or any of the products or services described thereon. Links do not imply that Worksuite or this Web Site sponsors, endorses, is affiliated or associated with, or is legally authorized to use any trademark, trade name, logo or copyright symbol displayed in or accessible through the links, or that any linked site is authorized to use any trademark, trade name, logo or copyright symbol of Worksuite or any of its affiliates or subsidiaries. You hereby expressly acknowledge and agree that the linked sites are not under the control of Worksuite and Worksuite is not responsible for the contents of any linked site or any link contained in a linked site, or any changes or updates to such sites. Worksuite is not responsible for webcasting or any other form of transmission received from any linked site. Worksuite is providing these links to You only as a convenience, and the inclusion of any link shall not be construed to imply endorsement by Worksuite in any manner of the website.</div><div><br></div><div><b><span style=\"font-size: 14px;\">NO WARRANTIES</span></b></div><div>THIS WEB SITE, THE INFORMATION AND MATERIALS ON THE SITE, AND ANY SOFTWARE MADE AVAILABLE ON THE WEB SITE, ARE PROVIDED \"AS IS\" WITHOUT ANY REPRESENTATION OR WARRANTY, EXPRESS OR IMPLIED, OF ANY KIND, INCLUDING, BUT NOT LIMITED TO, WARRANTIES OF MERCHANTABILITY, NON INFRINGEMENT, OR FITNESS FOR ANY PARTICULAR PURPOSE. THERE IS NO WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, REGARDING THIRD PARTY CONTENT. INSPITE OF FROIDEN BEST ENDEAVOURS, THERE IS NO WARRANTY ON BEHALF OF FROIDEN THAT THIS WEB SITE WILL BE FREE OF ANY COMPUTER VIRUSES. SOME JURISDICTIONS DO NOT ALLOW FOR THE EXCLUSION OF IMPLIED WARRANTIES, SO THE ABOVE EXCLUSIONS MAY NOT APPLY TO YOU.</div><div>LIMITATION OF DAMAGES:</div><div>IN NO EVENT SHALL FROIDEN OR ANY OF ITS SUBSIDIARIES OR AFFILIATES BE LIABLE TO ANY ENTITY FOR ANY DIRECT, INDIRECT, SPECIAL, CONSEQUENTIAL OR OTHER DAMAGES (INCLUDING, WITHOUT LIMITATION, ANY LOST PROFITS, BUSINESS INTERRUPTION, LOSS OF INFORMATION OR PROGRAMS OR OTHER DATA ON YOUR INFORMATION HANDLING SYSTEM) THAT ARE RELATED TO THE USE OF, OR THE INABILITY TO USE, THE CONTENT, MATERIALS, AND FUNCTIONS OF THIS WEB SITE OR ANY LINKED WEB SITE, EVEN IF FROIDEN IS EXPRESSLY ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</div><div><br></div><div><b><span style=\"font-size: 14px;\">DISCLAIMER:</span></b></div><div><span style=\"font-size: 12px;\">THE WEB SITE MAY CONTAIN INACCURACIES AND TYPOGRAPHICAL AND CLERICAL ERRORS. FROIDEN EXPRESSLY DISCLAIMS ANY OBLIGATION(S) TO UPDATE THIS WEBSITE OR ANY OF THE MATERIALS ON THIS WEBSITE. FROIDEN DOES NOT WARRANT THE ACCURACY OR COMPLETENESS OF THE MATERIALS OR THE RELIABILITY OF ANY ADVICE, OPINION, STATEMENT OR OTHER INFORMATION DISPLAYED OR DISTRIBUTED THROUGH THE WEB SITE. YOU ACKNOWLEDGE THAT ANY RELIANCE ON ANY SUCH OPINION, ADVICE, STATEMENT, MEMORANDUM, OR INFORMATION SHALL BE AT YOUR SOLE RISK. FROIDEN RESERVES THE RIGHT, IN ITS SOLE DISCRETION, TO CORRECT ANY ERRORS OR OMISSIONS IN ANY PORTION OF THE WEB SITE. FROIDEN MAY MAKE ANY OTHER CHANGES TO THE WEB SITE, THE MATERIALS AND THE PRODUCTS, PROGRAMS, SERVICES OR PRICES (IF ANY) DESCRIBED IN THE WEB SITE AT ANY TIME WITHOUT NOTICE. THIS WEB SITE IS FOR INFORMATIONAL PURPOSES ONLY AND SHOULD NOT BE CONSTRUED AS TECHNICAL ADVICE OF ANY MANNER.</span></div><div><span style=\"font-size: 12px;\">UNLAWFUL AND/OR PROHIBITED USE OF THE WEB SITE</span></div><div>As a condition of Your use of the Web Site, You shall not use the Web Site for any purpose(s) that is unlawful or prohibited by the Terms of Use. You shall not use the Web Site in any manner that could damage, disable, overburden, or impair any Worksuite server, or the network(s) connected to any Worksuite server, or interfere with any other party's use and enjoyment of any services associated with the Web Site. You shall not attempt to gain unauthorized access to any section of the Web Site, other accounts, computer systems or networks connected to any Worksuite server or to any of the services associated with the Web Site, through hacking, password mining or any other means. You shall not obtain or attempt to obtain any materials or information through any means not intentionally made available through the Web Site.</div><div><br></div><div><b><span style=\"font-size: 14px;\">INDEMNITY:</span></b></div><div>You agree to indemnify and hold harmless Worksuite, its subsidiaries and affiliates from any claim, cost, expense, judgment or other loss relating to Your use of this Web Site in any manner, including without limitation of the foregoing, any action You take which is in violation of the terms and conditions of these Terms of Use and against any applicable law.</div><div><br></div><div><b><span style=\"font-size: 14px;\">CHANGES:</span></b></div><div>Worksuite reserves the rights, at its sole discretion, to change, modify, add or remove any portion of these Terms of Use in whole or in part, at any time. Changes in these Terms of Use will be effective when notice of such change is posted. Your continued use of the Web Site after any changes to these Terms of Use are posted will be considered acceptance of those changes. Worksuite may terminate, change, suspend or discontinue any aspect of the Web Site, including the availability of any feature(s) of the Web Site, at any time. Worksuite may also impose limits on certain features and services or restrict Your access to certain sections or all of the Web Site without notice or liability. You hereby acknowledge and agree that Worksuite may terminate the authorization, rights, and license given above at any point of time at its own sole discretion, and upon such termination; You shall immediately destroy all Materials.</div><div><br></div><div><b><span style=\"font-size: 14px;\">INTERNATIONAL USERS AND CHOICE OF LAW:</span></b></div><div>This Site is controlled, operated, and administered by Worksuite from within India. Worksuite makes no representation that Materials on this Web Site are appropriate or available for use at any other location(s) outside India. Any access to this Web Site from territories where their contents are illegal is prohibited. You may not use the Web Site or export the Materials in violation of any applicable export laws and regulations. If You access this Web Site from a location outside India, You are responsible for compliance with all local laws.</div><div>These Terms of Use shall be governed by the laws of India, without giving effect to its conflict of laws provisions. You agree that the appropriate court(s) in Bangalore, India, will have the exclusive jurisdiction to resolve all disputes arising under these Terms of Use and You hereby consent to personal jurisdiction in such forum.</div><div>These Terms of Use constitute the entire agreement between Worksuite and You with respect to Your use of the Web Site. Any claim You may have with respect to Your use of the Web Site must be commenced within one (1) year of the cause of action. If any provision(s) of this Terms of Use is held by a court of competent jurisdiction to be contrary to law then such provision(s) shall be severed from this Terms of Use and the other remaining provisions of this Terms of Use shall remain in full force and effect. without giving effect to its conflict of laws provisions. You agree that the appropriate court(s) in Bangalore, India, will have the exclusive jurisdiction to resolve all disputes arising under these Terms of Use and You hereby consent to personal jurisdiction in such forum.</div><div>These Terms of Use constitute the entire agreement between Worksuite and You with respect to Your use of the Web Site. Any claim You may have with respect to Your use of the Web Site must be commenced within one (1) year of the cause of action. If any provision(s) of this Terms of Use is held by a court of competent jurisdiction to be contrary to law then such provision(s) shall be severed from this Terms of Use and the other remaining provisions of this Terms of Use shall remain in full force and effect.</div>"
            ]
        ];



        FooterMenu::insert($menu);

    }

    public function seoDetail($languageId)
    {
        $seoAuthor = GlobalSetting::first()->global_app_name ?? 'Worksuite';
        $defaultSeoDetails = [
            'seo_author' => $seoAuthor,
            'seo_keywords' => 'best crm,hr management software, web hr software, hr software online, free hr software, hr software for sme, hr management software for small business, cloud hr software, online hr management software',
            'seo_description' => $seoAuthor . ' saas is easy to use CRM software that is designed for B2B. It include  everything you need to run your businesses. like manage customers, projects, invoices, estimates, timelogs, contract and much more.'
        ];

        $pages = [
            ['page_name' => 'home', 'seo_title' => 'Home'],
            ['page_name' => 'feature', 'seo_title' => 'Features'],
            ['page_name' => 'pricing', 'seo_title' => 'Pricing'],
            ['page_name' => 'contact', 'seo_title' => 'Contact'],
        ];

        $data = [];

        foreach ($pages as $page) {
            $data[] = array_merge([
                'language_setting_id' => $languageId,
                'page_name' => $page['page_name'],
                'seo_title' => $page['seo_title']
            ], $defaultSeoDetails);
        }

        SeoDetail::insert($data);

        $footerPages = FooterMenu::where('language_setting_id', $languageId)->get();

        $dataFooter = [];
        foreach ($footerPages as $footerPage) {
            $dataFooter[] = array_merge([
                'language_setting_id' => $languageId,
                'page_name' => $footerPage['slug'],
                'seo_title' => $footerPage['name']
            ], $defaultSeoDetails);
        }

        SeoDetail::insert($dataFooter);

    }

    public function frontMenu($languageId)
    {
        $frontMenu = new FrontMenu();
        $frontMenu->home = 'Home';
        $frontMenu->price = 'Pricing';
        $frontMenu->contact = 'Contact';
        $frontMenu->feature = 'Features';
        $frontMenu->get_start = 'Get Started';
        $frontMenu->login = 'Login';
        $frontMenu->contact_submit = 'Submit Enquiry';
        $frontMenu->language_setting_id = $languageId;
        $frontMenu->save();
    }

}
