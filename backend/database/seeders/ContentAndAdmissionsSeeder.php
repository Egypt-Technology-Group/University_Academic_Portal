<?php

namespace Database\Seeders;

use App\Models\DownloadDocument;
use App\Modules\Cms\Models\Announcement;
use App\Modules\Cms\Models\NewsArticle;
use App\Modules\Cms\Models\NewsCategory;
use App\Modules\Events\Models\Event;
use App\Modules\AcademicServices\Models\ExamSchedule;
use App\Modules\AcademicServices\Models\OfficialStatement;
use App\Modules\AcademicServices\Models\StudentServiceRequest;
use App\Modules\AcademicStructure\Models\Program;
use App\Modules\Admissions\Models\AdmissionCycle;
use App\Modules\Admissions\Models\Application;
use App\Modules\Admissions\Models\ApplicationDocument;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContentAndAdmissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. News Categories
        $newsCategoriesData = [
            [
                'name' => [
                    'en' => 'Academic & Scientific Research',
                    'ar' => 'الأخبار الأكاديمية والبحث العلمي',
                ],
                'slug' => 'academic-and-research',
            ],
            [
                'name' => [
                    'en' => 'Campus Life & Student Activities',
                    'ar' => 'الحياة الجامعية والأنشطة الطلابية',
                ],
                'slug' => 'campus-life-activities',
            ],
            [
                'name' => [
                    'en' => 'Global Partnerships & Accreditations',
                    'ar' => 'الشراكات الدولية والاعتمادات',
                ],
                'slug' => 'global-partnerships',
            ],
            [
                'name' => [
                    'en' => 'Innovations & Student Achievements',
                    'ar' => 'الابتكارات وإنجازات الطلاب',
                ],
                'slug' => 'innovations-and-achievements',
            ],
        ];

        $categories = [];
        foreach ($newsCategoriesData as $catData) {
            $categories[$catData['slug']] = NewsCategory::create($catData);
        }

        // 2. News Articles
        $newsArticlesData = [
            [
                'category_slug' => 'innovations-and-achievements',
                'title' => [
                    'en' => 'University Inception of the Next-Generation AI & Robotics Innovation Hub',
                    'ar' => 'افتتاح مجمع الابتكار للذكاء الاصطناعي وهندسة الروبوتات بالجامعة',
                ],
                'slug' => 'university-inaugurates-ai-robotics-hub',
                'excerpt' => [
                    'en' => 'Equipped with supercomputing GPU clusters and autonomous robotic arms, the newly inaugurated center bridges academic research with high-tech industries.',
                    'ar' => 'مجهز بأحدث عناقيد المعالجة الرسومية الفائقة والأذرع الروبوتية الذاتية، يربط المجمع الجديد بين الأبحاث الأكاديمية وقطاعات التكنولوجيا المتقدمة.',
                ],
                'body' => [
                    'en' => "The University proudly inaugurated its flagship Innovation Hub for Artificial Intelligence and Autonomous Robotics. The facility boasts over 40 dedicated workstation pods, high-performance GPU server nodes for training generative deep learning models, and advanced fabrication testbeds.\n\nDuring the keynote address, the University President emphasized: 'Our mission is to empower our student innovators and faculty researchers to solve real-world problems in smart transportation, medical diagnostics, and automated industries.'\n\nThe hub has established initial research partnerships with five leading international technology corporations.",
                    'ar' => "احتفلت الجامعة بافتتاح مجمع الابتكار المتقدم للذكاء الاصطناعي وهندسة الروبوتات الذاتية، والذي يضم أكثر من 40 محطة عمل متطورة وخوادم معالجة رسومية عالية الأداء لتدريب نماذج التعلم العميق والذكاء الاصطناعي التوليدي.\n\nوأكد رئيس الجامعة في كلمته الافتتاحية: 'تتمثل رسالتنا في تمكين طلابنا وباحثينا من تطوير حلول تقنية رائدة تسهم في مجالات النقل الذكي، والتشخيص الطبي، والأتمتة الصناعية وفق أعلى المعايير العالمية.'\n\nوقد شهد حفل الافتتاح توقيع مذكرات تفاهم أولية مع خمس من كبريات الشركات التكنولوجية العالمية.",
                ],
                'featured_image' => 'images/news/ai-hub-opening.jpg',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(2),
                'views_count' => 1420,
            ],
            [
                'category_slug' => 'innovations-and-achievements',
                'title' => [
                    'en' => 'Computer Science Students Win First Place in Regional Smart Cities Hackathon 2025',
                    'ar' => 'طلاب كلية علوم الحاسب يحصدون المركز الأول في هاكاثون المدن الذكية الإقليمي 2025',
                ],
                'slug' => 'cs-students-win-first-place-smart-cities-hackathon',
                'excerpt' => [
                    'en' => 'The winning team developed an intelligent IoT-driven traffic optimization and emergency vehicle dispatch platform tested on real-time city telemetry.',
                    'ar' => 'طور الفريق الفائز منصة ذكية تعتمد على إنترنت الأشياء لتحسين السيولة المرورية وتسهيل مرور سيارات الطوارئ عبر تحليل البيانات اللحظية.',
                ],
                'body' => [
                    'en' => "A multi-disciplinary team of 5 students from the Faculty of Computer Science & AI claimed the top prize among 60 participating universities across the MENA region.\n\nThe project titled 'SmartFlow' integrates edge AI sensors with dynamic traffic light scheduling to minimize urban transit latency and reduce carbon emissions by up to 22%. The team received a development grant to incubate the startup locally.",
                    'ar' => "حقق فريق طلابي متميز يضم 5 من طلاب كلية علوم الحاسب والذكاء الاصطناعي المركز الأول بين أكثر من 60 جامعة مشاركة من منطقة الشرق الأوسط وشمال أفريقيا.\n\nالمشروع الفائز 'SmartFlow' يدمج حساسات الذكاء الاصطناعي الطرفية مع خوارزميات جدولة الإشارات المرورية لتقليل زمن الانتظار وخفض الانبعاثات الكربونية بنسبة تصل إلى 22%. وحصل الفريق على منحة احتضان لتأسيس شركتهم الناشئة.",
                ],
                'featured_image' => 'images/news/hackathon-win.jpg',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(5),
                'views_count' => 980,
            ],
            [
                'category_slug' => 'academic-and-research',
                'title' => [
                    'en' => 'Faculty of Pharmacy Publishes Breakthrough Research on Nanocarriers for Targeted Oncology',
                    'ar' => 'فريق بحثي بكلية الصيدلة ينشر دراسة رائدة حول حوامل النانو لعلاج الأورام الموجه',
                ],
                'slug' => 'pharmacy-faculty-publishes-nanocarriers-oncology-research',
                'excerpt' => [
                    'en' => 'Published in an international Q1 journal, the research presents biodegradable lipid nanocapsules that selectively deliver chemotherapy to tumor cells with minimal toxicity.',
                    'ar' => 'نُشرت في إحدى أرقى الدوريات العلمية المصنفة Q1، وتقدم كبسولات نانوية دهنية قابلة للتحلل الحيوي لتوصيل العلاج الكيماوي بدقة للخلايا السرطانية.',
                ],
                'body' => [
                    'en' => "A joint research group led by Prof. Dr. Layla Hamdi and Dr. Huda Shawky at the Faculty of Pharmacy has synthesized a novel biocompatible nanocarrier system that increases bioavailability and targets malignant cells directly.\n\nLaboratory evaluations demonstrated a 65% reduction in off-target toxicity compared to conventional formulations. The Dean commended the research team for advancing the frontiers of precision medicine.",
                    'ar' => "نجح فريق بحثي مشترك بقيادة أ.د. ليلى حمدي ود. هدى شوقي بكلية الصيدلة في تخليق نظام نانوي جديد متوافق حيوياً يزيد من الفعالية العلاجية ويوجه الدواء مباشرة للخلايا الخبيثة.\n\nوأظهرت النتائج المخبرية انخفاضاً بنسبة 65% في الآثار الجانبية والسمية مقارنة بالتركيبات التقليدية. وأشادت إدارة الكلية بالجهود الاستثنائية للفريق البحثي في خدمة الطب الدقيق.",
                ],
                'featured_image' => 'images/news/pharmacy-research.jpg',
                'is_featured' => true,
                'published_at' => Carbon::now()->subDays(10),
                'views_count' => 1840,
            ],
            [
                'category_slug' => 'global-partnerships',
                'title' => [
                    'en' => 'Strategic Dual-Degree Partnership Signed with Top European Technical Universities',
                    'ar' => 'توقيع اتفاقية شراكة استراتيجية للدرجات العلمية المزدوجة مع كبرى الجامعات التقنية الأوروبية',
                ],
                'slug' => 'strategic-dual-degree-partnership-european-universities',
                'excerpt' => [
                    'en' => 'Engineering and Computing students can now earn a globally recognized double bachelor degree with semester exchange programs in Germany and the UK.',
                    'ar' => 'تتيح الاتفاقية لطلاب الهندسة والحاسبات الحصول على شهادة بكالوريوس مزدوجة معتمدة دولياً مع إمكانية قضاء فصول دراسية في ألمانيا والمملكة المتحدة.',
                ],
                'body' => [
                    'en' => "The University entered a comprehensive academic consortium agreement enabling dual-degree paths for engineering, cybersecurity, and data science majors.\n\nUnder this agreement, eligible undergraduate students who complete specified coursework will spend one year studying abroad, obtaining dual diplomas upon graduation along with access to global corporate internship programs.",
                    'ar' => "وقعت الجامعة اتفاقية تعاون أكاديمي شاملة لتقديم برامج درجات علمية مزدوجة في تخصصات الهندسة، والأمن السيبراني، وعلوم البيانات.\n\nوبموجب الاتفاقية، يتاح للطلاب المتميزين قضاء عام دراسي كامل في الخارج، والحصول على شهادتين جامعيتين عند التخرج، فضلاً عن فرص تدريب عملي في كبرى المؤسسات الصناعية الأوروبية.",
                ],
                'featured_image' => 'images/news/dual-degree-signing.jpg',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(14),
                'views_count' => 2100,
            ],
            [
                'category_slug' => 'campus-life-activities',
                'title' => [
                    'en' => 'Annual Career Fair 2025 Connects 3,500+ Students with 85 Multi-National Companies',
                    'ar' => 'ملتقى التوظيف السنوي 2025 يستقبل أكثر من 3500 طالب بمشاركة 85 شركة عالمية ومحلية',
                ],
                'slug' => 'annual-career-fair-2025-success',
                'excerpt' => [
                    'en' => 'The 2-day fair offered instant interviews, CV clinics, executive leadership panels, and over 600 confirmed internships for upcoming graduates.',
                    'ar' => 'شهد الملتقى على مدار يومين إجراء مقابلات توظيف فورية وورش تدريبية وتوفير أكثر من 600 فرصة تدريب وتوظيف للخريجين والطلاب.',
                ],
                'body' => [
                    'en' => "The University's Career Development Center hosted its most extensive Career Fair to date, spanning corporate exhibitors in fintech, software development, civil infrastructure, clinical healthcare, and FMCG.\n\nOver 600 job offers and paid summer internships were presented on-site. The event also featured career coaching sessions and portfolio reviews by industry leaders.",
                    'ar' => "نظم مركز التطوير المهني بالجامعة أضخم ملتقى توظيفي سنوي بمشاركة كبريات الشركات في مجالات التكنولوجيا المالية، وهندسة البرمجيات، والإنشاءات، والرعاية الصحية، والتجارة الدولية.\n\nوأسفر الملتقى عن تقديم أكثر من 600 فرصة عمل وتدريب صيفي مدفوع الأجر، إضافة إلى تنظيم ورش عمل متخصصة لكتابة السير الذاتية واجتياز المقابلات الشخصية.",
                ],
                'featured_image' => 'images/news/career-fair.jpg',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(18),
                'views_count' => 3120,
            ],
            [
                'category_slug' => 'academic-and-research',
                'title' => [
                    'en' => 'Business School Organizes International Forum on Future Economy & Green Fintech',
                    'ar' => 'كلية إدارة الأعمال تنظم المنتدى الدولي لمستقبل الاقتصاد والتكنولوجيا المالية الخضراء',
                ],
                'slug' => 'business-school-future-economy-forum',
                'excerpt' => [
                    'en' => 'Prominent financial executives and policymakers gathered to discuss sustainable banking frameworks, carbon credits, and digital currency innovations.',
                    'ar' => 'نخبة من قيادات القطاع المصرفي والخبراء الدوليين يناقشون آليات التمويل المستدام، وأسواق أرصدة الكربون، والعملات الرقمية للبنوك المركزية.',
                ],
                'body' => [
                    'en' => "The Faculty of Business Administration convened the 'Future of Green Commerce & Digital Banking Forum', addressing the rapid transformation in ESG-compliant investments.\n\nKeynote speaker Prof. Dr. Noha Soliman highlighted the urgency of embedding data analytics and algorithmic audit trails in contemporary banking programs to prepare graduates for evolving regulatory demands.",
                    'ar' => "عقدت كلية إدارة الأعمال فعاليات منتدى 'مستقبل التجارة الخضراء والخدمات المصرفية الرقمية'، لمناقشة التحولات المتسارعة في الاستثمارات المتوافقة مع معايير الاستدامة والحوكمة ESG.\n\nوشددت أ.د. نهى سليمان عميدة الكلية على أهمية دمج تحليلات البيانات المتقدمة وتطبيقات الفينتك في المناهج الدراسية لإعداد خريجين قادرين على مواكبة متطلبات أسواق المال الحديثة.",
                ],
                'featured_image' => 'images/news/business-forum.jpg',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(22),
                'views_count' => 740,
            ],
            [
                'category_slug' => 'innovations-and-achievements',
                'title' => [
                    'en' => 'Engineering Senior Expo Showcases 120 Sustainable Capstone Inventions',
                    'ar' => 'معرض مشروعات التخرج الهندسية يستعرض 120 ابتكاراً في مجالات التنمية المستدامة',
                ],
                'slug' => 'engineering-senior-expo-sustainable-capstones',
                'excerpt' => [
                    'en' => 'Projects included solar-powered desalination units, earthquake-resistant modular homes, and autonomous agricultural monitoring drones.',
                    'ar' => 'شملت المشروعات محطات تحلية مياه بالطاقة الشمسية، ومباني سكنية مقاومة للزلازل، وطائرات درون ذكية لمراقبة المحاصيل الزراعية.',
                ],
                'body' => [
                    'en' => "Graduating seniors from Civil, Electrical, and Mechatronics Engineering presented their capstone projects before a panel of academic and industrial evaluators.\n\nThree exceptional inventions were selected for patent filing support and incubation under the University's Technology Transfer Office (TTO).",
                    'ar' => "استعرض طلاب السنوات النهائية بأقسام الهندسة المدنية والكهربائية والميكاترونكس مشاريع تخرجهم المبتكرة أمام لجنة تحكيم تضم خبراء أكاديميين ورواد الصناعة.\n\nوتم ترشيح 3 مشاريع متميزة لتسجيل براءات اختراع رسمية وتوفير التمويل الأولي لاحتضانها كشركات ناشئة بالجامعة.",
                ],
                'featured_image' => 'images/news/engineering-expo.jpg',
                'is_featured' => false,
                'published_at' => Carbon::now()->subDays(26),
                'views_count' => 1150,
            ],
        ];

        foreach ($newsArticlesData as $artData) {
            $catSlug = $artData['category_slug'];
            unset($artData['category_slug']);

            $cat = $categories[$catSlug] ?? null;
            $artData['category_id'] = $cat?->id;

            NewsArticle::create($artData);
        }

        // 3. Events
        $eventsData = [
            [
                'title' => [
                    'en' => 'International Summit on Artificial Intelligence & Deep Technologies 2026',
                    'ar' => 'القمة الدولية للذكاء الاصطناعي والتقنيات العميقة 2026',
                ],
                'slug' => 'international-ai-summit-2026',
                'location' => [
                    'en' => 'Main Auditorium & Grand Convention Center, Building C',
                    'ar' => 'القاعة الكبرى ومركز المؤتمرات الرئيسي، مبنى (ج)',
                ],
                'organizer' => [
                    'en' => 'Faculty of Computer Science & AI in partnership with IEEE',
                    'ar' => 'كلية علوم الحاسب والذكاء الاصطناعي بالتعاون مع منظمة IEEE',
                ],
                'description' => [
                    'en' => 'A premier 3-day conference featuring international keynotes, AI ethics workshops, paper tracks, and deep learning masterclasses with world-renowned scientists.',
                    'ar' => 'مؤتمر دولي يستمر لمدة 3 أيام يضم جلسات نقاشية رفيعة المستوى، وورش عمل حول أخلاقيات الذكاء الاصطناعي، ومسارات بحثية محكمة لكبار العلماء والباحثين.',
                ],
                'cover_image' => 'images/events/ai-summit-2026.jpg',
                'start_time' => Carbon::now()->addDays(15)->setTime(9, 30),
                'end_time' => Carbon::now()->addDays(17)->setTime(18, 00),
            ],
            [
                'title' => [
                    'en' => 'Annual University Career & Internship Fair 2025',
                    'ar' => 'يوم التوظيف والتدريب الصيفي السنوي 2025',
                ],
                'slug' => 'annual-career-internship-fair-2025',
                'location' => [
                    'en' => 'University Sports Complex & Open Exhibition Plaza',
                    'ar' => 'المجمع الرياضي والساحة المفتوحة للمعارض بالجامعة',
                ],
                'organizer' => [
                    'en' => 'Center for Career Advancement & Alumni Affairs',
                    'ar' => 'مركز التطوير المهني وشؤون الخريجين',
                ],
                'description' => [
                    'en' => 'Meet with hiring leads from over 90 enterprise employers across technology, civil contracting, banking, pharmaceuticals, and marketing.',
                    'ar' => 'فرصة التواصل المباشر مع مسؤولي التوظيف في أكثر من 90 شركة رائدة في مجالات تكنولوجيا المعلومات والمقاولات والقطاع المصرفي والصيدلي والتسويق.',
                ],
                'cover_image' => 'images/events/career-fair-2025.jpg',
                'start_time' => Carbon::now()->addDays(22)->setTime(10, 00),
                'end_time' => Carbon::now()->addDays(23)->setTime(17, 00),
            ],
            [
                'title' => [
                    'en' => 'International Symposium on Green Energy & Sustainable Construction',
                    'ar' => 'الندوة الدولية للطاقة الخضراء وهندسة التشييد المستدام',
                ],
                'slug' => 'green-energy-sustainable-construction-symposium',
                'location' => [
                    'en' => 'Engineering Hall 102 & Innovation Hallway',
                    'ar' => 'قاعة الهندسة 102 وممر الابتكار والمعارض',
                ],
                'organizer' => [
                    'en' => 'Faculty of Engineering & Technology',
                    'ar' => 'كلية الهندسة والتكنولوجيا',
                ],
                'description' => [
                    'en' => 'Exploring zero-emission building materials, grid storage solutions, smart wastewater reclamation, and decarbonization strategies.',
                    'ar' => 'استعراض أحدث تقنيات مواد البناء منعدمة الانبعاثات، وتخزين الطاقة، ومعالجة مياه الصرف الذكية، واستراتيجيات خفض البصمة الكربونية للمدن.',
                ],
                'cover_image' => 'images/events/green-energy-symposium.jpg',
                'start_time' => Carbon::now()->addDays(30)->setTime(11, 00),
                'end_time' => Carbon::now()->addDays(30)->setTime(16, 30),
            ],
            [
                'title' => [
                    'en' => 'Global Clinical Pharmacy Day & Patient Safety Forum',
                    'ar' => 'اليوم العالمي للصيدلة الإكلينيكية ومنتدى سلامة المرضى',
                ],
                'slug' => 'clinical-pharmacy-patient-safety-forum',
                'location' => [
                    'en' => 'Pharmacy Complex Main Hall',
                    'ar' => 'المدرج المركزي بمجمع الصيدلة والعلوم الصحية',
                ],
                'organizer' => [
                    'en' => 'Faculty of Pharmacy in collaboration with Ministry of Health Hospitals',
                    'ar' => 'كلية الصيدلة بالتعاون مع مستشفيات وزارة الصحة والتعليم العالي',
                ],
                'description' => [
                    'en' => 'Interactive case-study workshops on personalized oncology pharmacotherapy, pediatric antimicrobial protocols, and patient counseling simulations.',
                    'ar' => 'ورش عمل تفاعلية لدراسة الحالات السريرية المعقدة في علاج الأورام، والبروتوكولات الدوائية للأطفال، ومحاكاة الاستشارات الصيدلانية للمرضى.',
                ],
                'cover_image' => 'images/events/pharmacy-forum.jpg',
                'start_time' => Carbon::now()->addDays(40)->setTime(9, 00),
                'end_time' => Carbon::now()->addDays(40)->setTime(15, 00),
            ],
            [
                'title' => [
                    'en' => 'University Cultural Festival & Student Clubs Carnival',
                    'ar' => 'المهرجان الثقافي والكرنفال السنوي للأسر والأنشطة الطلابية',
                ],
                'slug' => 'university-cultural-festival-clubs-carnival',
                'location' => [
                    'en' => 'Central University Campus Courtyard',
                    'ar' => 'الساحة المركزية بالحرم الجامعي',
                ],
                'organizer' => [
                    'en' => 'Student Union & Youth Care Administration',
                    'ar' => 'اتحاد طلاب الجامعة والإدارة العامة لرعاية الشباب',
                ],
                'description' => [
                    'en' => 'A vibrant celebration showcasing student musical talent, theatrical performances, science club demonstrations, and international food stalls.',
                    'ar' => 'احتفالية كبرى تستعرض مواهب الطلاب الفنية والموسيقية، والعروض المسرحية، وتجارب النوادي العلمية، ومعارض الفنون التشكيلية.',
                ],
                'cover_image' => 'images/events/cultural-carnival.jpg',
                'start_time' => Carbon::now()->addDays(48)->setTime(12, 00),
                'end_time' => Carbon::now()->addDays(48)->setTime(21, 00),
            ],
        ];

        foreach ($eventsData as $eData) {
            Event::create($eData);
        }

        // 4. Announcements
        $announcementsData = [
            [
                'title' => [
                    'en' => 'URGENT: Fall 2025/2026 Academic Registration & Advising Window Opens',
                    'ar' => 'إعلان عاجل: فتح باب التسجيل الأكاديمي والإرشاد للفصل الدراسي خريف 2025 / 2026',
                ],
                'content' => [
                    'en' => "All undergraduate students must consult their designated academic advisors and finalize their course registrations via the Student Portal before the strict deadline of September 15. Late registrations will incur administrative penalty fees.",
                    'ar' => "نهيب بجميع طلاب الكليات سرعة مراجعة المرشدين الأكاديميين واعتماد تسجيل المقررات الدراسية عبر بوابة الطالب الإلكترونية في موعد أقصاه 15 سبتمبر. سيتم تطبيق غرامات تأخير للتسجيلات المتأخرة.",
                ],
                'target_audience' => 'students',
                'priority' => 'urgent',
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(20),
            ],
            [
                'title' => [
                    'en' => 'Faculty Research Grant Applications for Fiscal Year 2025/2026 Now Open',
                    'ar' => 'فتح باب التقدم للحصول على المنح البحثية الممولة لأعضاء هيئة التدريس 2025 / 2026',
                ],
                'content' => [
                    'en' => "The University Deanship of Scientific Research invites faculty principal investigators to submit funding proposals for interdisciplinary projects focusing on AI, clean energy, and public health. Maximum seed funding per project is 250,000 EGP.",
                    'ar' => "تعلن عمادة البحث العلمي عن فتح باب التقدم للمشروعات البحثية الممولة في مجالات الذكاء الاصطناعي، والطاقة النظيفة، والعلوم الصيدلية، بتمويل يصل إلى 250,000 جنيه مصري لكل مشروع بحثي معتمد.",
                ],
                'target_audience' => 'faculty',
                'priority' => 'pinned',
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(45),
            ],
            [
                'title' => [
                    'en' => 'Final Examination Schedules and Seating Distribution Maps Released',
                    'ar' => 'إعلان جداول الامتحانات النهائية وتوزيع مقار اللجان الامتحانية لكافة الكليات',
                ],
                'content' => [
                    'en' => "Detailed exam schedules, dates, and classroom hall numbers have been published in the Student Portal and Downloads section. Students are strictly advised to carry their University ID cards to all sessions.",
                    'ar' => "تم نشر الجداول التفصيلية ومواعيد الامتحانات وأرقام القاعات والمدرجات عبر بوابة الطالب وقسم الوثائق والتحميلات. يرجى من جميع الطلاب الالتزام بحمل بطاقة تحقيق الشخصية الجامعية.",
                ],
                'target_audience' => 'students',
                'priority' => 'normal',
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(30),
            ],
            [
                'title' => [
                    'en' => 'Admissions Open for Early Enrolment for the Academic Year 2025/2026',
                    'ar' => 'بدء تلقي طلبات التقديم والالتحاق المبكر للعام الجامعي الجديد 2025 / 2026',
                ],
                'content' => [
                    'en' => "Prospective students holding Thanaweya Amma, STEM, IGCSE, or equivalent credentials can now submit their online admission applications. Merit scholarships covering up to 50% tuition are available for top-tier scorers.",
                    'ar' => "تعلن إدارة القبول والتسجيل عن بدء استقبال طلبات الالتحاق الإلكترونية للطلاب الحاصلين على الثانوية العامة والشهادات المعادلة، مع تقديم منح تفوق دراسي تصل إلى 50% لأوائل الشهادات.",
                ],
                'target_audience' => 'public',
                'priority' => 'normal',
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(60),
            ],
            [
                'title' => [
                    'en' => 'Campus High-Speed Wi-Fi Upgrade & Digital Library Global Access',
                    'ar' => 'تحديث شبكة الإنترنت اللاسلكي وتوفير الوصول لقواعد بيانات المكتبة الرقمية العالمية',
                ],
                'content' => [
                    'en' => "The University IT Infrastructure Team has upgraded high-speed campus Wi-Fi coverage across all lecture halls and laboratories. Seamless single-sign-on access to IEEE Xplore, ScienceDirect, and Scopus is now live for all faculty and students.",
                    'ar' => "تم الانتهاء من تحديث شبكة الواي فاي بالحرم الجامعي لتغطية كافة المدرجات والمعامل، مع إتاحة الدخول المباشر للحسابات الجامعية على قواعد البيانات العالمية مثل IEEE Xplore وScienceDirect وScopus.",
                ],
                'target_audience' => 'all',
                'priority' => 'normal',
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(90),
            ],
            [
                'title' => [
                    'en' => 'University Transportation Shuttle Routes & Semester Subscription Info',
                    'ar' => 'تحديث خطوط حافلات نقل الطلاب واشتراكات الفصل الدراسي الجديد',
                ],
                'content' => [
                    'en' => "Student transportation bus routes covering Greater Cairo and neighboring areas have been updated for the upcoming semester. Subscriptions can be renewed online or via the student finance office.",
                    'ar' => "تم تحديث مسارات وأوقات حافلات النقل الجامعي لتغطية كافة مناطق القاهرة الكبرى والمحافظات المجاورة. يمكن تجديد الاشتراكات إلكترونياً أو عبر مكتب الخزينة.",
                ],
                'target_audience' => 'students',
                'priority' => 'normal',
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(35),
            ],
        ];

        foreach ($announcementsData as $annData) {
            Announcement::create($annData);
        }

        // 5. Download Documents
        $downloadDocumentsData = [
            [
                'category' => 'bylaws',
                'title' => [
                    'en' => 'General Academic Regulations & Credit Hour System Bylaws (2025 Edition)',
                    'ar' => 'اللائحة التنظيمية الموحدة لنظام الساعات المعتمدة وقواعد التخرج (إصدار 2025)',
                ],
                'file_path' => 'downloads/bylaws-academic-regulations-2025.pdf',
                'file_size' => '3.4 MB',
                'file_type' => 'PDF',
                'download_count' => 1420,
            ],
            [
                'category' => 'schedules',
                'title' => [
                    'en' => 'Final Examination Schedule & Timetable - All Colleges',
                    'ar' => 'جدول مواعيد الامتحانات النهائية المعتمد لكافة الكليات والأقسام',
                ],
                'file_path' => 'downloads/final-exams-timetable-all-colleges.pdf',
                'file_size' => '1.8 MB',
                'file_type' => 'PDF',
                'download_count' => 3890,
            ],
            [
                'category' => 'schedules',
                'title' => [
                    'en' => 'Weekly Lecture Schedules & Laboratory Rotations Guide',
                    'ar' => 'الجداول الدراسية الأسبوعية وتوزيع مجموعات المعامل والسكاشن',
                ],
                'file_path' => 'downloads/weekly-lecture-lab-schedules.pdf',
                'file_size' => '2.2 MB',
                'file_type' => 'PDF',
                'download_count' => 2750,
            ],
            [
                'category' => 'forms',
                'title' => [
                    'en' => 'Course Add / Drop and Overload Credit Request Form',
                    'ar' => 'نموذج طلب إضافة وحذف المقررات والعبء الدراسي الإضافي',
                ],
                'file_path' => 'downloads/form-course-add-drop-request.pdf',
                'file_size' => '450 KB',
                'file_type' => 'PDF',
                'download_count' => 1860,
            ],
            [
                'category' => 'forms',
                'title' => [
                    'en' => 'Official Academic Transcript & Enrollment Verification Request Form',
                    'ar' => 'استمارة طلب بيان الدرجات الرسمي وشهادات القيد الجامعي',
                ],
                'file_path' => 'downloads/form-transcript-enrollment-request.pdf',
                'file_size' => '380 KB',
                'file_type' => 'PDF',
                'download_count' => 920,
            ],
            [
                'category' => 'guides',
                'title' => [
                    'en' => 'Undergraduate Student Handbook & Code of Academic Conduct',
                    'ar' => 'دليل الطالب الجامعي وميثاق الشرف الأخلاقي والسلوك الطلابي',
                ],
                'file_path' => 'downloads/undergraduate-handbook-code-of-conduct.pdf',
                'file_size' => '5.6 MB',
                'file_type' => 'PDF',
                'download_count' => 2130,
            ],
            [
                'category' => 'guides',
                'title' => [
                    'en' => 'International Students Living & Academic Survival Guide',
                    'ar' => 'دليل الطلاب الوافدين للمعيشة والإجراءات الأكاديمية والخدمات الجامعية',
                ],
                'file_path' => 'downloads/international-students-guide.pdf',
                'file_size' => '4.2 MB',
                'file_type' => 'PDF',
                'download_count' => 640,
            ],
        ];

        foreach ($downloadDocumentsData as $docData) {
            DownloadDocument::create($docData);
        }

        // 6. Admission Cycles & Sample Applications
        $activeCycle = AdmissionCycle::create([
            'title' => [
                'en' => 'Fall 2025/2026 Undergraduate Admissions',
                'ar' => 'القبول الجامعي للعام الدراسي خريف 2025 / 2026',
            ],
            'academic_year' => '2025/2026',
            'term' => 'Fall 2025',
            'start_date' => Carbon::parse('2025-06-01'),
            'end_date' => Carbon::parse('2025-09-30'),
            'is_open' => true,
        ]);

        $pastCycle = AdmissionCycle::create([
            'title' => [
                'en' => 'Spring 2024/2025 Undergraduate Admissions',
                'ar' => 'القبول الجامعي للفصل الدراسي ربيع 2024 / 2025',
            ],
            'academic_year' => '2024/2025',
            'term' => 'Spring 2025',
            'start_date' => Carbon::parse('2024-11-01'),
            'end_date' => Carbon::parse('2025-01-31'),
            'is_open' => false,
        ]);

        // Get programs for application linking
        $aiProgram = Program::where('slug', 'bsc-artificial-intelligence-machine-learning')->first();
        $softwareProgram = Program::where('slug', 'bsc-software-engineering-cloud')->first();
        $civilProgram = Program::where('slug', 'bsc-civil-structural-engineering')->first();
        $pharmacyProgram = Program::where('slug', 'pharmd-clinical-pharmacy')->first();
        $marketingProgram = Program::where('slug', 'bsc-digital-marketing-ecommerce')->first();

        $sampleApplications = [
            [
                'application_number' => 'APP-2025-00101',
                'admission_cycle_id' => $activeCycle->id,
                'program_id' => $aiProgram?->id ?? 1,
                'first_name' => 'Ahmed',
                'last_name' => 'Mahmoud Zaki',
                'national_id' => '30405120101234',
                'email' => 'ahmed.m.zaki@gmail.com',
                'phone' => '+20 10 1234 5678',
                'high_school_score' => 94.50,
                'status' => 'accepted',
                'notes' => 'High scores in Advanced Math and Computer Science. Eligible for 25% Academic Excellence Scholarship.',
                'documents' => [
                    ['type' => 'high_school_certificate', 'path' => 'applications/docs/app1-highschool.pdf', 'status' => 'verified'],
                    ['type' => 'national_id', 'path' => 'applications/docs/app1-nationalid.pdf', 'status' => 'verified'],
                    ['type' => 'birth_certificate', 'path' => 'applications/docs/app1-birthcert.pdf', 'status' => 'verified'],
                ],
            ],
            [
                'application_number' => 'APP-2025-00102',
                'admission_cycle_id' => $activeCycle->id,
                'program_id' => $softwareProgram?->id ?? 1,
                'first_name' => 'Nourhan',
                'last_name' => 'Adel Mansour',
                'national_id' => '30509180105678',
                'email' => 'nourhan.mansour@outlook.com',
                'phone' => '+20 11 9876 5432',
                'high_school_score' => 91.20,
                'status' => 'under_review',
                'notes' => 'Awaiting secondary language equivalence certificate validation.',
                'documents' => [
                    ['type' => 'high_school_certificate', 'path' => 'applications/docs/app2-highschool.pdf', 'status' => 'verified'],
                    ['type' => 'national_id', 'path' => 'applications/docs/app2-nationalid.pdf', 'status' => 'verified'],
                    ['type' => 'english_proficiency_test', 'path' => 'applications/docs/app2-toefl.pdf', 'status' => 'pending'],
                ],
            ],
            [
                'application_number' => 'APP-2025-00103',
                'admission_cycle_id' => $activeCycle->id,
                'program_id' => $pharmacyProgram?->id ?? 1,
                'first_name' => 'Mariam',
                'last_name' => 'Youssef Hassan',
                'national_id' => '30411250109988',
                'email' => 'mariam.youssef.h@gmail.com',
                'phone' => '+20 12 3456 7890',
                'high_school_score' => 96.80,
                'status' => 'accepted',
                'notes' => 'Top tier candidate with exceptional Biology and Chemistry marks. Full acceptance packet issued.',
                'documents' => [
                    ['type' => 'high_school_certificate', 'path' => 'applications/docs/app3-highschool.pdf', 'status' => 'verified'],
                    ['type' => 'national_id', 'path' => 'applications/docs/app3-nationalid.pdf', 'status' => 'verified'],
                    ['type' => 'medical_fitness_report', 'path' => 'applications/docs/app3-medical.pdf', 'status' => 'verified'],
                ],
            ],
            [
                'application_number' => 'APP-2025-00104',
                'admission_cycle_id' => $activeCycle->id,
                'program_id' => $civilProgram?->id ?? 1,
                'first_name' => 'Omar',
                'last_name' => 'Khaled Tawfik',
                'national_id' => '30501030107766',
                'email' => 'omar.k.tawfik@gmail.com',
                'phone' => '+20 10 9988 7766',
                'high_school_score' => 88.00,
                'status' => 'submitted',
                'notes' => 'Application submitted online; awaiting original document submission during in-person interview.',
                'documents' => [
                    ['type' => 'high_school_certificate', 'path' => 'applications/docs/app4-highschool.pdf', 'status' => 'pending'],
                    ['type' => 'national_id', 'path' => 'applications/docs/app4-nationalid.pdf', 'status' => 'pending'],
                ],
            ],
            [
                'application_number' => 'APP-2025-00105',
                'admission_cycle_id' => $activeCycle->id,
                'program_id' => $marketingProgram?->id ?? 1,
                'first_name' => 'Karim',
                'last_name' => 'Tarek Mostafa',
                'national_id' => '30408190104433',
                'email' => 'karim.tarek.m@yahoo.com',
                'phone' => '+20 15 5544 3322',
                'high_school_score' => 83.40,
                'status' => 'under_review',
                'notes' => 'Transfer application from another private university; credit transfer evaluation currently in progress.',
                'documents' => [
                    ['type' => 'high_school_certificate', 'path' => 'applications/docs/app5-highschool.pdf', 'status' => 'verified'],
                    ['type' => 'transfer_transcript', 'path' => 'applications/docs/app5-transcript.pdf', 'status' => 'pending'],
                ],
            ],
        ];

        foreach ($sampleApplications as $appItem) {
            $docs = $appItem['documents'] ?? [];
            unset($appItem['documents']);

            $application = Application::create($appItem);

            foreach ($docs as $dItem) {
                ApplicationDocument::create([
                    'application_id' => $application->id,
                    'document_type' => $dItem['type'],
                    'file_path' => $dItem['path'],
                    'verification_status' => $dItem['status'],
                ]);
            }
        }

        // 6. Electronic Student Requests
        StudentServiceRequest::create([
            'request_number' => 'REQ-2025-0001',
            'student_id_number' => '20241001',
            'student_name' => 'Youssef Ahmed Hassan',
            'program_id' => $aiProgram?->id ?? 1,
            'service_type' => 'enrollment_cert',
            'purpose' => [
                'ar' => 'استخراج شهادة قيد رسمية موجهة إلى نقابة المهندسين والتجنيد',
                'en' => 'Official proof of enrollment for Syndicate & Military authorities',
            ],
            'status' => 'approved',
            'admin_notes' => 'تمت المراجعة والاعتماد وختم الشهادة بنسر الكلية.',
            'handled_by' => 'Dr. Admissions Director',
            'fee_amount' => 50.00,
            'is_fee_paid' => true,
            'completed_at' => now()->subDays(2),
        ]);

        StudentServiceRequest::create([
            'request_number' => 'REQ-2025-0002',
            'student_id_number' => '20242002',
            'student_name' => 'Nourhan Mahmoud Aly',
            'program_id' => $pharmaProgram?->id ?? 2,
            'service_type' => 'transcript',
            'purpose' => [
                'ar' => 'كشف درجات تفصيلي باللغة الإنجليزية للتقديم على منحة صيفية دولية',
                'en' => 'Official academic transcript in English for Summer Exchange Program',
            ],
            'status' => 'processing',
            'admin_notes' => 'قيد الترجمة والاعتماد من عميد الكلية.',
            'handled_by' => 'Registrar Officer',
            'fee_amount' => 100.00,
            'is_fee_paid' => true,
            'completed_at' => null,
        ]);

        // 7. Verifiable Official Statements & Certificates
        OfficialStatement::create([
            'certificate_code' => 'CERT-2025-EG892144',
            'student_id_number' => '20241001',
            'student_name' => 'Youssef Ahmed Hassan',
            'national_id' => '30405150102233',
            'program_id' => $aiProgram?->id ?? 1,
            'statement_type' => 'official_enrollment',
            'title' => [
                'ar' => 'إفادة قيد رسمية معتمدة لدرجة البكالوريوس',
                'en' => 'Official Certificate of Enrollment (B.Sc. Artificial Intelligence)',
            ],
            'recipient_entity' => [
                'ar' => 'إلى من يهمه الأمر / سفارة جمهورية مصر العربية',
                'en' => 'To Whom It May Concern / Egyptian Embassy',
            ],
            'verification_hash' => hash('sha256', 'CERT-2025-EG89214420241001'),
            'qr_payload' => url('/verify-certificate?code=CERT-2025-EG892144'),
            'signatory_name' => 'Prof. Dr. Ahmed Mansour',
            'signatory_title' => 'Dean of Faculty of Engineering & Technology',
            'issue_date' => now()->subDays(5),
            'valid_until' => now()->addMonths(6),
            'is_revoked' => false,
        ]);

        // 8. Exam Schedules & Proctors
        ExamSchedule::create([
            'program_id' => $aiProgram?->id ?? 1,
            'academic_term_id' => 1,
            'course_code' => 'CS301',
            'course_name' => [
                'ar' => 'الذكاء الاصطناعي وتعلم الآلة المتقدم',
                'en' => 'Artificial Intelligence & Advanced Machine Learning',
            ],
            'exam_type' => 'final',
            'exam_date' => Carbon::parse('2026-06-15'),
            'start_time' => '09:00:00',
            'end_time' => '12:00:00',
            'hall_location' => [
                'ar' => 'مدرج الدكتور مجدي يعقوب (مبنى أ - الدور الثاني)',
                'en' => 'Magdi Yacoub Auditorium (Hall A - 2nd Floor)',
            ],
            'chief_invigilator' => [
                'ar' => 'أ.د. عصام النجار',
                'en' => 'Prof. Dr. Essam El-Naggar',
            ],
            'proctors_list' => ['Eng. Omar Mostafa', 'Eng. Heba Salem', 'Eng. Ziad Farouk'],
            'seating_capacity' => 120,
        ]);

        ExamSchedule::create([
            'program_id' => $pharmaProgram?->id ?? 2,
            'academic_term_id' => 1,
            'course_code' => 'PH402',
            'course_name' => [
                'ar' => 'علم الأدوية الإكلينيكي والعلاجي',
                'en' => 'Clinical Pharmacology & Therapeutics',
            ],
            'exam_type' => 'final',
            'exam_date' => Carbon::parse('2026-06-18'),
            'start_time' => '10:00:00',
            'end_time' => '13:00:00',
            'hall_location' => [
                'ar' => 'مدرج ابن سينا المركزي (مبنى العلوم الصيدلية)',
                'en' => 'Ibn Sina Grand Hall (Pharmaceutical Sciences Complex)',
            ],
            'chief_invigilator' => [
                'ar' => 'أ.د. منى عبد الرحمن',
                'en' => 'Prof. Dr. Mona Abdel-Rahman',
            ],
            'proctors_list' => ['Dr. Sarah Nabil', 'Dr. Mohamed Rashed'],
            'seating_capacity' => 80,
        ]);
    }
}
