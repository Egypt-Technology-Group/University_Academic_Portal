<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\CourseResult;
use App\Models\StudentRecord;
use App\Models\User;
use App\Modules\AcademicStructure\Models\College;
use App\Modules\AcademicStructure\Models\Department;
use App\Modules\AcademicStructure\Models\FacultyProfile;
use App\Modules\AcademicStructure\Models\Program;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AcademicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Colleges, Departments, and Programs
        $collegesData = [
            [
                'name' => [
                    'en' => 'Faculty of Engineering & Technology',
                    'ar' => 'كلية الهندسة والتكنولوجيا',
                ],
                'slug' => 'faculty-of-engineering-and-technology',
                'dean_name' => [
                    'en' => 'Prof. Dr. Ahmed Mansour',
                    'ar' => 'أ.د. أحمد منصور',
                ],
                'about' => [
                    'en' => 'The Faculty of Engineering & Technology is dedicated to providing cutting-edge education in modern engineering disciplines with state-of-the-art laboratories and strong industry partnerships.',
                    'ar' => 'تلتزم كلية الهندسة والتكنولوجيا بتقديم تعليم متميز ومتقدم في التخصصات الهندسية الحديثة مع معامل متطورة وشراكات صناعية رائدة لتأهيل مهندسي المستقبل.',
                ],
                'vision' => [
                    'en' => 'To be a premier regional center of excellence in engineering education, sustainable technological innovation, and scientific research.',
                    'ar' => 'أن نكون مركزاً إقليمياً رائداً للتميز في التعليم الهندسي، والابتكار التكنولوجي المستدام، والبحث العلمي التطبيقي.',
                ],
                'mission' => [
                    'en' => 'Preparing highly qualified engineers equipped with innovative problem-solving skills, ethical values, and technological competence to serve the national and international industrial development.',
                    'ar' => 'إعداد مهندسين ذوي كفاءة عالية ومجهزين بمهارات حل المشكلات الابتكارية والقيم الأخلاقية والكفاءة التكنولوجية لخدمة التنمية الصناعية محلياً ودولياً.',
                ],
                'banner_image' => 'images/colleges/engineering.jpg',
                'sort_order' => 1,
                'is_active' => true,
                'departments' => [
                    [
                        'name' => [
                            'en' => 'Department of Civil & Structural Engineering',
                            'ar' => 'قسم الهندسة المدنية والإنشائية',
                        ],
                        'slug' => 'civil-and-structural-engineering',
                        'head_name' => [
                            'en' => 'Dr. Tarek El-Sayed',
                            'ar' => 'د. طارق السيد',
                        ],
                        'description' => [
                            'en' => 'Focuses on sustainable structural design, smart city infrastructure, geotechnical engineering, and advanced construction management.',
                            'ar' => 'يركز على التصميم الإنشائي المستدام، وبنية المدن الذكية، وميكانيكا التربة، والإدارة الهندسية المتقدمة للمشاريع.',
                        ],
                        'sort_order' => 1,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Civil & Structural Engineering',
                                    'ar' => 'بكالوريوس الهندسة المدنية والإنشائية',
                                ],
                                'slug' => 'bsc-civil-structural-engineering',
                                'degree_level' => 'bachelor',
                                'duration_years' => 5,
                                'credit_hours' => 165,
                                'curriculum' => [
                                    'en' => [
                                        'Year 1: Engineering Physics, Calculus, Mechanics, Engineering Drawing',
                                        'Year 2: Structural Analysis, Fluid Mechanics, Surveying, Material Testing',
                                        'Year 3: Reinforced Concrete I & II, Soil Mechanics, Steel Design I, Hydraulics',
                                        'Year 4: Foundation Engineering, Highway & Transportation, Steel Design II, Quantity Surveying',
                                        'Year 5: Advanced Concrete Structures, Construction Management, Graduation Capstone Project',
                                    ],
                                    'ar' => [
                                        'السنة الأولى: فيزياء هندسية، تفاضل وتكامل، ميكانيكا هندسية، رسم هندسي',
                                        'السنة الثانية: تحليل إنشائي، ميكانيكا الموائع، مساحة مستوية، خواص واختبار المواد',
                                        'السنة الثالثة: خرسانة مسلحة 1 و2، ميكانيكا التربة، منشآت معدنية 1، هيدروليكا',
                                        'السنة الرابعة: هندسة الأساسات، هندسة الطرق والمرور، منشآت معدنية 2، حساب كميات ومواصفات',
                                        'السنة الخامسة: منشآت خرسانية خاصة، إدارة مشروعات التشييد، مشروع التخرج الميداني',
                                    ],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Structural Design Engineer', 'Site Construction Manager', 'Geotechnical Consultant', 'Infrastructure Planning Engineer', 'Quantity Surveyor'],
                                    'ar' => ['مهندس تصميم إنشائي', 'مدير موقع ومشروعات تشييد', 'استشاري ميكانيكا تربة وأساسات', 'مهندس تخطيط بنية تحتية', 'مهندس حساب كميات وعقود'],
                                ],
                                'tuition_fees' => [
                                    'en' => '55,000 EGP per academic year (payable in 2 installments)',
                                    'ar' => '55,000 جنيه مصري للعام الجامعي (تسدد على قسطين متساويين)',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma (Mathematics branch) with minimum 75% or accredited equivalent international diplomas (IGCSE, American Diploma, STEM).',
                                    'ar' => 'شهادة الثانوية العامة المصرية (علمي رياضة) بحد أدنى 75% أو ما يعادلها من الشهادات المعادلة المعتمدة (IGCSE، الدبلومة الأمريكية، مدارس المتفوقين STEM).',
                                ],
                            ],
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Smart Infrastructure & Environmental Engineering',
                                    'ar' => 'بكالوريوس هندسة البنية التحتية الذكية والبيئة',
                                ],
                                'slug' => 'bsc-smart-infrastructure-environmental',
                                'degree_level' => 'bachelor',
                                'duration_years' => 5,
                                'credit_hours' => 165,
                                'curriculum' => [
                                    'en' => ['Smart Water Networks', 'Wastewater Treatment Systems', 'GIS for Urban Infrastructure', 'Environmental Impact Assessment', 'Smart Transportation Systems'],
                                    'ar' => ['شبكات المياه الذكية', 'محطات معالجة مياه الصرف', 'نظم المعلومات الجغرافية للمدن', 'تقييم الأثر البيئي للمشروعات', 'أنظمة النقل الذكية المستدامة'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Smart Cities Engineer', 'Environmental Consultant', 'Water Resources Engineer', 'Urban Infrastructure Modeler'],
                                    'ar' => ['مهندس بنية تحتية للمدن الذكية', 'استشاري هندسة بيئية', 'مهندس شبكات وموارد مائية', 'مصمم نماذج بنية تحتية رقمية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '58,000 EGP per academic year',
                                    'ar' => '58,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School Math Branch or equivalent with minimum 73%.',
                                    'ar' => 'الثانوية العامة شعبة رياضيات أو ما يعادلها بحد أدنى 73%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Electrical & Communications Engineering',
                            'ar' => 'قسم الهندسة الكهربائية والاتصالات',
                        ],
                        'slug' => 'electrical-and-communications-engineering',
                        'head_name' => [
                            'en' => 'Dr. Mohamed Ragab',
                            'ar' => 'د. محمد رجب',
                        ],
                        'description' => [
                            'en' => 'Covers modern power grids, renewable energy generation, wireless 5G/6G communication systems, and IoT hardware architectures.',
                            'ar' => 'يغطي شبكات القوى الحديثة، وتوليد الطاقة المتجددة، وأنظمة الاتصالات اللاسلكية، ومعمارية أجهزة إنترنت الأشياء.',
                        ],
                        'sort_order' => 2,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Electrical Power & Renewable Energy',
                                    'ar' => 'بكالوريوس هندسة القوى الكهربائية والطاقة المتجددة',
                                ],
                                'slug' => 'bsc-electrical-power-renewable-energy',
                                'degree_level' => 'bachelor',
                                'duration_years' => 5,
                                'credit_hours' => 165,
                                'curriculum' => [
                                    'en' => ['Electric Circuits', 'Electromagnetic Fields', 'Power Electronics', 'Solar & Wind Energy Systems', 'Smart Power Distribution', 'Power Grid Protection'],
                                    'ar' => ['دوائر كهربية', 'مجالات كهرومغناطيسية', 'إلكترونيات القوى', 'أنظمة الطاقة الشمسية وطاقة الرياح', 'توزيع القوى والشبكات الذكية', 'وقاية الشبكات الكهربائية'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Power Systems Engineer', 'Solar Plant Project Manager', 'Grid Automation Specialist', 'Electrical Maintenance Director'],
                                    'ar' => ['مهندس نظم ومحطات قوى كهربية', 'مدير مشاريع طاقة شمسية ومتجددة', 'أخصائي تحكم آلي بالشبكات', 'مدير صيانة كهربية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '56,000 EGP per academic year',
                                    'ar' => '56,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School Math Branch with minimum 75%.',
                                    'ar' => 'الثانوية العامة شعبة رياضيات بحد أدنى 75%.',
                                ],
                            ],
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Electronics & Communication Systems',
                                    'ar' => 'بكالوريوس هندسة الإلكترونيات وأنظمة الاتصالات',
                                ],
                                'slug' => 'bsc-electronics-communication-systems',
                                'degree_level' => 'bachelor',
                                'duration_years' => 5,
                                'credit_hours' => 165,
                                'curriculum' => [
                                    'en' => ['Digital Signal Processing', 'Wireless Communications', 'Optical Fiber Networks', 'Microwave & Antenna Design', 'Embedded Microcontrollers'],
                                    'ar' => ['معالجة الإشارات الرقمية', 'الاتصالات اللاسلكية', 'شبكات الألياف الضوئية', 'هندسة الميكروويف والهوائيات', 'الأنظمة الإلكترونية المدمجة'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Telecom Network Engineer', 'RF/Microwave Design Engineer', 'Embedded Systems Developer', 'Fiber Optic Specialist'],
                                    'ar' => ['مهندس شبكات اتصالات', 'مهندس تصميم ترددات وهوائيات', 'مطور أنظمة مدمجة', 'أخصائي شبكات ألياف ضوئية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '56,000 EGP per academic year',
                                    'ar' => '56,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School Math Branch with minimum 75%.',
                                    'ar' => 'الثانوية العامة شعبة رياضيات بحد أدنى 75%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Mechatronics & Robotics Engineering',
                            'ar' => 'قسم هندسة الميكاترونكس والروبوتات',
                        ],
                        'slug' => 'mechatronics-and-robotics-engineering',
                        'head_name' => [
                            'en' => 'Dr. Samer Abdelrahman',
                            'ar' => 'د. سامر عبدالرحمن',
                        ],
                        'description' => [
                            'en' => 'Blends mechanical systems, precision electronics, control engineering, and autonomous robotics programming.',
                            'ar' => 'يدمج بين الأنظمة الميكانيكية، والإلكترونيات الدقيقة، وهندسة التحكم الآلي، وبرمجة الروبوتات الذاتية.',
                        ],
                        'sort_order' => 3,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Mechatronics & Automation Systems',
                                    'ar' => 'بكالوريوس هندسة الميكاترونكس وأنظمة التحكم الآلي',
                                ],
                                'slug' => 'bsc-mechatronics-automation-systems',
                                'degree_level' => 'bachelor',
                                'duration_years' => 5,
                                'credit_hours' => 165,
                                'curriculum' => [
                                    'en' => ['Sensors & Actuators', 'Robotics Kinematics & Dynamics', 'Industrial PLC & SCADA', 'Machine Vision for Robotics', 'Microcontroller Programming'],
                                    'ar' => ['الحساسات والمشغلات الميكانيكية', 'حركيات وديناميكا الروبوتات', 'التحكم المنطقي المبرمج وشبكات SCADA', 'الرؤية الحاسوبية للروبوتات', 'برمجة المتحكمات الدقيقة'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Robotics Engineer', 'Industrial Automation Specialist', 'Mechatronics Systems Designer', 'Automotive Systems Engineer'],
                                    'ar' => ['مهندس روبوتات وأنظمة ذاتية', 'أخصائي أتمتة صناعية', 'مهندس تصميم أنظمة ميكاترونكس', 'مهندس أنظمة سيارات حديثة'],
                                ],
                                'tuition_fees' => [
                                    'en' => '58,000 EGP per academic year',
                                    'ar' => '58,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School Math Branch with minimum 76%.',
                                    'ar' => 'الثانوية العامة شعبة رياضيات بحد أدنى 76%.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Faculty of Computer Science & Artificial Intelligence',
                    'ar' => 'كلية علوم الحاسب والذكاء الاصطناعي',
                ],
                'slug' => 'faculty-of-computer-science-and-ai',
                'dean_name' => [
                    'en' => 'Prof. Dr. Khaled Mostafa',
                    'ar' => 'أ.د. خالد مصطفى',
                ],
                'about' => [
                    'en' => 'The Faculty of Computer Science & AI empowers students to pioneer future technologies in artificial intelligence, cybersecurity, software engineering, and large-scale data systems.',
                    'ar' => 'تعمل كلية علوم الحاسب والذكاء الاصطناعي على تمكين الطلاب من ريادة تقنيات المستقبل في الذكاء الاصطناعي، والأمن السيبراني، وهندسة البرمجيات، والبيانات الضخمة.',
                ],
                'vision' => [
                    'en' => 'To lead international scientific breakthroughs and produce world-class technologists driving the digital innovation economy.',
                    'ar' => 'ريادة الابتكار التكنولوجي وإعداد كوادر برمجية وتقنية عالمية تقود قاطرة التحول الرقمي واقتصاد المعرفة.',
                ],
                'mission' => [
                    'en' => 'Delivering high-caliber curricula combining rigorous theoretical foundations with transformative hands-on software development and AI engineering.',
                    'ar' => 'تقديم برامج دراسية متقدمة تدمج بين الأسس النظرية الرصينة لعلوم الحاسب والتطبيقات العملية الابتكارية في هندسة البرمجيات والذكاء الاصطناعي.',
                ],
                'banner_image' => 'images/colleges/cs-ai.jpg',
                'sort_order' => 2,
                'is_active' => true,
                'departments' => [
                    [
                        'name' => [
                            'en' => 'Department of Artificial Intelligence & Data Science',
                            'ar' => 'قسم الذكاء الاصطناعي وعلوم البيانات',
                        ],
                        'slug' => 'artificial-intelligence-and-data-science',
                        'head_name' => [
                            'en' => 'Dr. Mona El-Khatib',
                            'ar' => 'د. منى الخطيب',
                        ],
                        'description' => [
                            'en' => 'Specializes in machine learning, deep neural networks, computer vision, natural language processing, and big data architectures.',
                            'ar' => 'يتخصص في تعلم الآلة، والشبكات العصبية العميقة، والرؤية الحاسوبية، ومعالجة اللغات الطبيعية، وهندسة البيانات الضخمة.',
                        ],
                        'sort_order' => 1,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Artificial Intelligence & Machine Learning',
                                    'ar' => 'بكالوريوس الذكاء الاصطناعي وتعلم الآلة',
                                ],
                                'slug' => 'bsc-artificial-intelligence-machine-learning',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 136,
                                'curriculum' => [
                                    'en' => [
                                        'Year 1: Programming Fundamentals (Python/C++), Discrete Math, Linear Algebra, Probability & Statistics',
                                        'Year 2: Data Structures & Algorithms, Object-Oriented Design, Database Systems, Machine Learning Foundations',
                                        'Year 3: Deep Learning & Neural Networks, Computer Vision, Natural Language Processing, Reinforcement Learning',
                                        'Year 4: Generative AI & LLMs, AI Ethics & Governance, MLOps, Graduation Capstone Project',
                                    ],
                                    'ar' => [
                                        'السنة الأولى: أساسيات البرمجة (بايثون / سي++)، الرياضيات المتقطعة، الجبر الخطي، الاحتمالات والإحصاء',
                                        'السنة الثانية: هياكل البيانات والخوارزميات، البرمجة كائنية التوجه، قواعد البيانات، مبادئ تعلم الآلة',
                                        'السنة الثالثة: التعلم العميق والشبكات العصبية، الرؤية الحاسوبية، معالجة اللغات الطبيعية، التعلم التعزيزي',
                                        'السنة الرابعة: نماذج الذكاء الاصطناعي التوليدي، أخلاقيات وحوكمة الذكاء الاصطناعي، نشر نماذج MLOps، مشروع التخرج',
                                    ],
                                ],
                                'career_opportunities' => [
                                    'en' => ['AI/ML Engineer', 'Computer Vision Specialist', 'NLP Data Scientist', 'MLOps Engineer', 'AI Research Scientist'],
                                    'ar' => ['مهندس ذكاء اصطناعي وتعلم آلة', 'أخصائي رؤية حاسوبية', 'عالم بيانات ومعالجة لغات طبيعية', 'مهندس تشغيل نماذج الذكاء الاصطناعي MLOps', 'باحث في خوارزميات الذكاء الاصطناعي'],
                                ],
                                'tuition_fees' => [
                                    'en' => '62,000 EGP per academic year',
                                    'ar' => '62,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma (Math or Science branch) with minimum 78% or equivalent accredited certificates.',
                                    'ar' => 'الثانوية العامة المصرية (علمي رياضة أو علمي علوم) بحد أدنى 78% أو الشهادات المعادلة المعتمدة.',
                                ],
                            ],
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Big Data Analytics & Business Intelligence',
                                    'ar' => 'بكالوريوس تحليلات البيانات الضخمة وذكاء الأعمال',
                                ],
                                'slug' => 'bsc-big-data-analytics-bi',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 136,
                                'curriculum' => [
                                    'en' => ['Data Warehousing & ETL', 'Distributed Systems (Hadoop/Spark)', 'Predictive Analytics', 'Cloud Data Engineering', 'BI Dashboards & Visualization'],
                                    'ar' => ['مستودعات البيانات وعمليات ETL', 'الأنظمة الموزعة (Hadoop/Spark)', 'التحليلات التنبؤية المتقدمة', 'هندسة البيانات السحابية', 'لوحات معلومات ذكاء الأعمال والتمثيل المرئي'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Big Data Engineer', 'Business Intelligence Developer', 'Data Architect', 'Analytics Consultant'],
                                    'ar' => ['مهندس بيانات ضخمة', 'مطور ذكاء أعمال BI', 'معماري بيانات سحابية', 'استشاري تحليلات رقمية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '60,000 EGP per academic year',
                                    'ar' => '60,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma with minimum 75%.',
                                    'ar' => 'الثانوية العامة بحد أدنى 75%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Computer Science & Software Engineering',
                            'ar' => 'قسم علوم الحاسب وهندسة البرمجيات',
                        ],
                        'slug' => 'computer-science-and-software-engineering',
                        'head_name' => [
                            'en' => 'Dr. Hassan Youssef',
                            'ar' => 'د. حسن يوسف',
                        ],
                        'description' => [
                            'en' => 'Dedicated to robust software architecture, cloud microservices, full-stack systems development, and mobile computing.',
                            'ar' => 'مخصص لهندسة البرمجيات المتقدمة، والمعماريات السحابية المصغرة، وتطوير الأنظمة المتكاملة Full-Stack، وتطبيقات الهواتف الذكية.',
                        ],
                        'sort_order' => 2,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Software Engineering & Cloud Computing',
                                    'ar' => 'بكالوريوس هندسة البرمجيات والحوسبة السحابية',
                                ],
                                'slug' => 'bsc-software-engineering-cloud',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 136,
                                'curriculum' => [
                                    'en' => ['Software Architecture & Design Patterns', 'Cloud Native Development (AWS/GCP)', 'DevOps & CI/CD Pipelines', 'Microservices & Distributed Systems', 'Mobile App Development'],
                                    'ar' => ['معمارية البرمجيات وأنماط التصميم', 'تطوير التطبيقات السحابية (AWS/GCP)', 'منهجيات DevOps وخطوط الإنتاج المؤتمتة', 'الأنظمة الموزعة والخدمات المصغرة', 'تطوير تطبيقات الهواتف الذكية'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Full-Stack Software Engineer', 'Cloud Solutions Architect', 'DevOps Specialist', 'Mobile Applications Engineer'],
                                    'ar' => ['مهندس برمجيات متكامل Full-Stack', 'معماري حلول سحابية', 'أخصائي DevOps وأتمتة برمجية', 'مهندس تطبيقات هواتف ذكية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '60,000 EGP per academic year',
                                    'ar' => '60,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School Math or Science branch with minimum 76%.',
                                    'ar' => 'الثانوية العامة شعبة رياضيات أو علوم بحد أدنى 76%.',
                                ],
                            ],
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Computer Science',
                                    'ar' => 'بكالوريوس علوم الحاسب',
                                ],
                                'slug' => 'bsc-computer-science',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 136,
                                'curriculum' => [
                                    'en' => ['Operating Systems Concepts', 'Compiler Design', 'Advanced Algorithms & Complexity', 'Computer Graphics & Game Engine Dev', 'Database Management Internals'],
                                    'ar' => ['مفاهيم نظم التشغيل', 'تصميم وبناء المترجمات Compilers', 'الخوارزميات المتقدمة ونظرية التعقيد', 'رسوميات الحاسب وتطوير محركات الألعاب', 'بنية وقواعد البيانات المتقدمة'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Core Systems Developer', 'Algorithms Engineer', 'Game Developer', 'Backend Services Engineer'],
                                    'ar' => ['مطور أنظمة تشغيل ونوى برمجية', 'مهندس خوارزميات', 'مطور ألعاب ورسوميات', 'مهندس خدمات وأنظمة خلفية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '58,000 EGP per academic year',
                                    'ar' => '58,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School with minimum 75%.',
                                    'ar' => 'الثانوية العامة بحد أدنى 75%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Cybersecurity & Networks',
                            'ar' => 'قسم الأمن السيبراني والشبكات',
                        ],
                        'slug' => 'cybersecurity-and-networks',
                        'head_name' => [
                            'en' => 'Dr. Omar Farouk',
                            'ar' => 'د. عمر فاروق',
                        ],
                        'description' => [
                            'en' => 'Focuses on offensive security, ethical hacking, digital forensics, cloud infrastructure protection, and cryptography.',
                            'ar' => 'يركز على الأمن الهجومي، والاختراق الأخلاقي، والأدلة الجنائية الرقمية، وتأمين البنى السحابية، والتشفير الرياضي.',
                        ],
                        'sort_order' => 3,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Cybersecurity & Digital Forensics',
                                    'ar' => 'بكالوريوس الأمن السيبراني والأدلة الرقمية',
                                ],
                                'slug' => 'bsc-cybersecurity-digital-forensics',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 136,
                                'curriculum' => [
                                    'en' => ['Network Security Protocols', 'Ethical Hacking & Penetration Testing', 'Digital Forensics & Incident Response', 'Cryptography & Blockchain', 'Security Operations Center (SOC) Management'],
                                    'ar' => ['بروتوكولات أمن الشبكات', 'الاختراق الأخلاقي واختبار الاختراق', 'الأدلة الجنائية الرقمية والاستجابة للحوادث', 'علم التشفير والبلوك تشين', 'إدارة مراكز العمليات الأمنية SOC'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Cybersecurity Analyst', 'Penetration Tester / Red Teamer', 'SOC Incident Responder', 'Digital Forensics Investigator'],
                                    'ar' => ['محلل أمن سيبراني', 'مختبر اختراق أمني (Red Team)', 'أخصائي استجابة للحوادث الأمنية', 'محقق في الأدلة الجنائية الرقمية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '62,000 EGP per academic year',
                                    'ar' => '62,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'High School Math or Science branch with minimum 77%.',
                                    'ar' => 'الثانوية العامة شعبة رياضيات أو علوم بحد أدنى 77%.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Faculty of Business Administration',
                    'ar' => 'كلية إدارة الأعمال',
                ],
                'slug' => 'faculty-of-business-administration',
                'dean_name' => [
                    'en' => 'Prof. Dr. Noha Soliman',
                    'ar' => 'أ.د. نهى سليمان',
                ],
                'about' => [
                    'en' => 'The Faculty of Business Administration shapes future business leaders, financial analysts, and entrepreneurs through internationally accredited curricula and experiential corporate internships.',
                    'ar' => 'تُعِد كلية إدارة الأعمال قادة الأعمال والمحللين الماليين ورواد الأعمال المستقبليين من خلال مناهج معتمدة دولياً وبرامج تدريب مهني مع كبرى الشركات والمؤسسات المصرفية.',
                ],
                'vision' => [
                    'en' => 'To be a premier business school recognized for entrepreneurial leadership, financial technology excellence, and strategic management.',
                    'ar' => 'أن نكون كلية إدارة أعمال رائدة إقليمياً ومتميزة في القيادة الريادية، والتكنولوجيا المالية، والإدارة الاستراتيجية الحديثة.',
                ],
                'mission' => [
                    'en' => 'Fostering analytical thinkers, innovative managers, and ethical decision-makers capable of steering dynamic global markets and digital commerce.',
                    'ar' => 'تأهيل مدراء ومحللين مبتكرين يتمتعون بمهارات التفكير التحليلي والقدرة على اتخاذ القرارات الاستراتيجية في الأسواق العالمية والتجارة الرقمية.',
                ],
                'banner_image' => 'images/colleges/business.jpg',
                'sort_order' => 3,
                'is_active' => true,
                'departments' => [
                    [
                        'name' => [
                            'en' => 'Department of Accounting & Auditing',
                            'ar' => 'قسم المحاسبة والمراجعة',
                        ],
                        'slug' => 'accounting-and-auditing',
                        'head_name' => [
                            'en' => 'Dr. Sherif Allam',
                            'ar' => 'د. شريف علام',
                        ],
                        'description' => [
                            'en' => 'Equips students with comprehensive international accounting standards (IFRS), corporate auditing, tax management, and fintech tools.',
                            'ar' => 'يزود الطلاب بالمعايير المحاسبية الدولية (IFRS)، والمراجعة المالية للشركات، والتخطيط الضريبي، وحلول التكنولوجيا المالية.',
                        ],
                        'sort_order' => 1,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Accounting & Financial Auditing',
                                    'ar' => 'بكالوريوس المحاسبة والمراجعة المالية',
                                ],
                                'slug' => 'bsc-accounting-financial-auditing',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 132,
                                'curriculum' => [
                                    'en' => ['Financial Accounting I & II', 'Cost & Management Accounting', 'International Auditing Standards (ISA)', 'Corporate Taxation', 'Forensic Accounting'],
                                    'ar' => ['المحاسبة المالية 1 و2', 'محاسبة التكاليف والإدارة', 'معايير المراجعة الدولية ISA', 'محاسبة الضرائب والشركات', 'المحاسبة الجنائية والتدقيق'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Senior Financial Auditor', 'Corporate Controller', 'Tax Consultant', 'Management Accountant (CMA)'],
                                    'ar' => ['مراجع حسابات أول', 'مدير مالي للشركات', 'مستشار ضرائب معتمد', 'محاسب إداري CMA'],
                                ],
                                'tuition_fees' => [
                                    'en' => '42,000 EGP per academic year',
                                    'ar' => '42,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma (Literary, Math, or Science branch) with minimum 65%.',
                                    'ar' => 'الثانوية العامة المصرية (أدبي أو علمي) بحد أدنى 65%.',
                                ],
                            ],
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Digital Accounting & Financial Technology (FinTech)',
                                    'ar' => 'بكالوريوس المحاسبة الرقمية والتكنولوجيا المالية (فينتك)',
                                ],
                                'slug' => 'bsc-digital-accounting-fintech',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 132,
                                'curriculum' => [
                                    'en' => ['ERP & Cloud Accounting Systems', 'FinTech & Blockchain Ecosystems', 'Algorithmic Financial Modeling', 'Big Data in Banking', 'Risk & Compliance Tech'],
                                    'ar' => ['أنظمة المحاسبة السحابية وERP', 'منظومة التكنولوجيا المالية والبلوك تشين', 'النمذجة المالية الخوارزمية', 'البيانات الضخمة في القطاع المصرفي', 'تقنيات إدارة المخاطر والامتثال'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['FinTech Product Manager', 'ERP Implementation Consultant', 'Digital Banking Analyst', 'Financial Systems Specialist'],
                                    'ar' => ['مدير منتجات التكنولوجيا المالية', 'استشاري تطبيق أنظمة ERP المحاسبية', 'محلل خدمات مصرفية رقمية', 'أخصائي نظم مالية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '45,000 EGP per academic year',
                                    'ar' => '45,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma with minimum 68%.',
                                    'ar' => 'الثانوية العامة بحد أدنى 68%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Marketing & Digital Commerce',
                            'ar' => 'قسم التسويق والتجارة الرقمية',
                        ],
                        'slug' => 'marketing-and-digital-commerce',
                        'head_name' => [
                            'en' => 'Dr. Rania Mahmoud',
                            'ar' => 'د. رانيا محمود',
                        ],
                        'description' => [
                            'en' => 'Prepares innovative professionals in digital growth strategies, brand management, consumer psychology, and omnichannel e-commerce.',
                            'ar' => 'يؤهل متخصصين مبدعين في استراتيجيات النمو الرقمي، وإدارة العلامات التجارية، وسلوك المستهلك، ومنصات التجارة الإلكترونية المتكاملة.',
                        ],
                        'sort_order' => 2,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Digital Marketing & E-Commerce',
                                    'ar' => 'بكالوريوس التسويق الرقمي والتجارة الإلكترونية',
                                ],
                                'slug' => 'bsc-digital-marketing-ecommerce',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 132,
                                'curriculum' => [
                                    'en' => ['Search Engine Optimization & SEM', 'Social Media Marketing & Analytics', 'Content Strategy & Brand Storytelling', 'E-Commerce Platform Operations', 'Growth Hacking & Conversion Rate Optimization'],
                                    'ar' => ['تحسين محركات البحث والإعلانات الممولة SEO/SEM', 'تسويق وتحليلات وسائل التواصل الاجتماعي', 'استراتيجيات المحتوى وبناء العلامة التجارية', 'إدارة وتطوير منصات التجارة الإلكترونية', 'استراتيجيات النمو وزيادة معدل التحويل CRO'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Digital Marketing Strategist', 'E-Commerce Operations Manager', 'Performance Marketing Specialist', 'Brand Director'],
                                    'ar' => ['مخطط استراتيجي للتسويق الرقمي', 'مدير عمليات التجارة الإلكترونية', 'أخصائي إعلانات الأداء والنتائج', 'مدير هوية وعلامة تجارية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '44,000 EGP per academic year',
                                    'ar' => '44,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma with minimum 65%.',
                                    'ar' => 'الثانوية العامة بحد أدنى 65%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Business Analytics & Supply Chain',
                            'ar' => 'قسم تحليل الأعمال وسلاسل الإمداد',
                        ],
                        'slug' => 'business-analytics-and-supply-chain',
                        'head_name' => [
                            'en' => 'Dr. Karim Gamal',
                            'ar' => 'د. كريم جمال',
                        ],
                        'description' => [
                            'en' => 'Focuses on data-driven operations, inventory optimization, international maritime logistics, and strategic procurement.',
                            'ar' => 'يركز على إدارة العمليات المدفوعة بالبيانات، وتحسين المخزون، واللوجستيات والشحن البحري الدولي، والمشتريات الاستراتيجية.',
                        ],
                        'sort_order' => 3,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'B.Sc. in Supply Chain Management & Logistics',
                                    'ar' => 'بكالوريوس إدارة سلاسل الإمداد والخدمات اللوجستية',
                                ],
                                'slug' => 'bsc-supply-chain-logistics',
                                'degree_level' => 'bachelor',
                                'duration_years' => 4,
                                'credit_hours' => 132,
                                'curriculum' => [
                                    'en' => ['Global Logistics & Shipping', 'Warehouse Automation & Inventory', 'Procurement & Supplier Relations', 'Supply Chain Analytics & Simulation', 'Sustainable Green Supply Chains'],
                                    'ar' => ['اللوجستيات والشحن الدولي', 'أتمتة المستودعات وإدارة المخزون', 'المشتريات وإدارة علاقات الموردين', 'تحليلات ونمذجة سلاسل الإمداد', 'سلاسل الإمداد الخضراء المستدامة'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Supply Chain Planner', 'Logistics Operations Director', 'Global Sourcing Manager', 'Warehouse Solutions Architect'],
                                    'ar' => ['مخطط سلاسل إمداد', 'مدير عمليات لوجستية وشحن', 'مدير توريدات دولية', 'مهندس حلول ومستودعات ذكية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '43,000 EGP per academic year',
                                    'ar' => '43,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma with minimum 65%.',
                                    'ar' => 'الثانوية العامة بحد أدنى 65%.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'en' => 'Faculty of Pharmacy & Health Sciences',
                    'ar' => 'كلية الصيدلة والعلوم الصحية',
                ],
                'slug' => 'faculty-of-pharmacy-and-health-sciences',
                'dean_name' => [
                    'en' => 'Prof. Dr. Layla Hamdi',
                    'ar' => 'أ.د. ليلى حمدي',
                ],
                'about' => [
                    'en' => 'The Faculty of Pharmacy & Health Sciences equips future clinical pharmacists and pharmaceutical researchers with comprehensive pharmaceutical sciences education and intensive clinical hospital rotations.',
                    'ar' => 'تزود كلية الصيدلة والعلوم الصحية الصيادلة الإكلينيكيين وباحثي العلوم الصيدلية بتعليم صيدلاني شامل وتدريب سريري مكثف داخل المستشفيات والمراكز العلاجية المعتمدة.',
                ],
                'vision' => [
                    'en' => 'Achieving national and international prominence in pharmaceutical education, clinical research, drug discovery, and healthcare patient outcomes.',
                    'ar' => 'تحقيق التميز والريادة وطنياً ودولياً في التعليم الصيدلي، والبحث الإكلينيكي، واكتشاف الأدوية، والارتقاء بصحة المجتمع.',
                ],
                'mission' => [
                    'en' => 'Graduating compassionate, scientifically proficient healthcare leaders who advance pharmaceutical care, novel drug delivery, and patient safety standards.',
                    'ar' => 'تخريج قادة رعاية صحية يتمتعون بالكفاءة العلمية العالية للمساهمة في تطوير الرعاية الصيدلانية، وتصنيع الدواء، وتطبيق أعلى معايير سلامة المرضى.',
                ],
                'banner_image' => 'images/colleges/pharmacy.jpg',
                'sort_order' => 4,
                'is_active' => true,
                'departments' => [
                    [
                        'name' => [
                            'en' => 'Department of Clinical Pharmacy & Pharmacology',
                            'ar' => 'قسم الصيدلة الإكلينيكية وعلم الأدوية',
                        ],
                        'slug' => 'clinical-pharmacy-and-pharmacology',
                        'head_name' => [
                            'en' => 'Dr. Yasser Fathy',
                            'ar' => 'د. ياسر فتحي',
                        ],
                        'description' => [
                            'en' => 'Covers hospital pharmacotherapy, drug-drug interaction monitoring, personalized precision medicine, and clinical therapeutics.',
                            'ar' => 'يغطي العلاج الدوائي السريري بالمستشفيات، ومراقبة التفاعلات الدوائية، والطب الشخصي الدقيق، وعلم الأدوية الإكلينيكي.',
                        ],
                        'sort_order' => 1,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'PharmD (Doctor of Pharmacy) - Clinical Pharmacy',
                                    'ar' => 'بكالوريوس دكتور صيدلي (صيدلة إكلينيكية)',
                                ],
                                'slug' => 'pharmd-clinical-pharmacy',
                                'degree_level' => 'bachelor',
                                'duration_years' => 6,
                                'credit_hours' => 180,
                                'curriculum' => [
                                    'en' => [
                                        'Years 1-2: Organic & Medicinal Chemistry, Human Anatomy, Physiology, Biochemistry, Microbiology',
                                        'Years 3-4: Pharmacology I-III, Pharmacotherapy in Cardiology & Oncology, Toxicology, Biopharmaceutics',
                                        'Year 5: Advanced Clinical Pharmacokinetics, Hospital Pharmacy Practice, Drug Informatics',
                                        'Year 6: Full Clinical Internship Year (Advanced Pharmacy Practice Experiences in Teaching Hospitals)',
                                    ],
                                    'ar' => [
                                        'السنوات 1-2: الكيمياء العضوية والدوائية، تشريح ووظائف الأعضاء، كيمياء حيوية، علم الكائنات الدقيقة',
                                        'السنوات 3-4: علم الأدوية 1-3، العلاج الدوائي لأمراض القلب والأورام، علم السموم، الحركيات الدوائية',
                                        'السنة 5: الحركيات السريرية المتقدمة، ممارسة الصيدلة بالمستشفيات، المعلوماتية الدوائية',
                                        'السنة 6: سنة الامتياز والتدريب الإكلينيكي الميداني الشامل بالمستشفيات الجامعية',
                                    ],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Hospital Clinical Pharmacist', 'ICU/Oncology Clinical Specialist', 'Pharmaceutical Research Scientist', 'Medical Science Liaison (MSL)'],
                                    'ar' => ['صيدلي إكلينيكي بالمستشفيات', 'أخصائي صيدلة سريرية للرعاية المركزة والأورام', 'باحث في التجارب السريرية', 'مستشار شؤون طبية ودوائية MSL'],
                                ],
                                'tuition_fees' => [
                                    'en' => '70,000 EGP per academic year',
                                    'ar' => '70,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma (Science branch) with minimum 82% or accredited equivalent international certificates.',
                                    'ar' => 'الثانوية العامة المصرية (علمي علوم) بحد أدنى 82% أو الشهادات المعادلة المعتمدة.',
                                ],
                            ],
                            [
                                'name' => [
                                    'en' => 'PharmD (Doctor of Pharmacy) - General Pharmacy',
                                    'ar' => 'بكالوريوس دكتور صيدلي (صيدلة عامة)',
                                ],
                                'slug' => 'pharmd-general-pharmacy',
                                'degree_level' => 'bachelor',
                                'duration_years' => 6,
                                'credit_hours' => 175,
                                'curriculum' => [
                                    'en' => ['Pharmaceutical Technology', 'Community Pharmacy Management', 'Medicinal Plants & Phytotherapy', 'Quality Control & Drug Formulation', 'Regulatory Affairs'],
                                    'ar' => ['التكنولوجيا الصيدلية', 'إدارة الصيدليات المجتمعية', 'النباتات الطبية والعلاج بالأعشاب', 'رقابة الجودة وتصنيع المستحضرات الدوائية', 'التسجيل والشؤون التنظيمية للادوية'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Community Pharmacist', 'Pharmaceutical Manufacturing Specialist', 'Drug Regulatory Affairs Officer', 'Quality Assurance Analyst'],
                                    'ar' => ['صيدلي مجتمعي', 'أخصائي تصنيع وإنتاج دوائي', 'مسؤول شؤون تنظيمية وتسجيل أدوية', 'أخصائي توكيد الجودة الصيدلية'],
                                ],
                                'tuition_fees' => [
                                    'en' => '65,000 EGP per academic year',
                                    'ar' => '65,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Egyptian Thanaweya Amma (Science branch) with minimum 80%.',
                                    'ar' => 'الثانوية العامة (علمي علوم) بحد أدنى 80%.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'en' => 'Department of Pharmaceutical Chemistry & Drug Discovery',
                            'ar' => 'قسم الكيمياء الصيدلية وتطوير الدواء',
                        ],
                        'slug' => 'pharmaceutical-chemistry-and-drug-discovery',
                        'head_name' => [
                            'en' => 'Dr. Huda Shawky',
                            'ar' => 'د. هدى شوقي',
                        ],
                        'description' => [
                            'en' => 'Focuses on drug design, molecular modeling, synthetic medicinal chemistry, nanomedicine formulations, and analytical quality control.',
                            'ar' => 'يركز على التصميم الجزيئي للدواء، والكيمياء الدوائية التخليقية، وتطبيقات النانو تكنولوجي الصيدلانية، والتحليل الكيميائي الدقيق.',
                        ],
                        'sort_order' => 2,
                        'programs' => [
                            [
                                'name' => [
                                    'en' => 'M.Sc. in Pharmaceutical Sciences & Drug Discovery',
                                    'ar' => 'ماجستير العلوم الصيدلية واكتشاف الدواء',
                                ],
                                'slug' => 'msc-pharmaceutical-sciences-drug-discovery',
                                'degree_level' => 'master',
                                'duration_years' => 2,
                                'credit_hours' => 48,
                                'curriculum' => [
                                    'en' => ['Advanced Molecular Docking', 'Spectroscopic Structural Elucidation', 'Nanotechnology in Targeted Drug Delivery', 'Master Thesis Research'],
                                    'ar' => ['الالتحام الجزيئي وتصميم الأدوية بالحاسوب', 'التحليل الطيفي لتحديد البنية الكيميائية', 'النانو تكنولوجي في إيصال الدواء الموجه', 'أطروحة وبحث الماجستير'],
                                ],
                                'career_opportunities' => [
                                    'en' => ['Pharmaceutical R&D Scientist', 'Formulation Chemist', 'Academic University Lecturer', 'Analytical Development Lead'],
                                    'ar' => ['باحث تطوير وابتكار دوائي R&D', 'كيميائي تركيبات صيدلانية', 'مدرس أكاديمي بالجامعات', 'رئيس قسم التطوير التحليلي'],
                                ],
                                'tuition_fees' => [
                                    'en' => '40,000 EGP per academic year',
                                    'ar' => '40,000 جنيه مصري للعام الجامعي',
                                ],
                                'admission_requirements' => [
                                    'en' => 'Bachelor of Pharmacy (B.Pharm or PharmD) from an accredited university with minimum GPA 3.0 / Very Good.',
                                    'ar' => 'بكالوريوس الصيدلة أو دكتور صيدلي من جامعة معتمدة بتقدير عام جيد جداً على الأقل.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Track created departments for faculty linking
        $createdDepartments = [];
        $createdPrograms = [];

        foreach ($collegesData as $cData) {
            $departments = $cData['departments'] ?? [];
            unset($cData['departments']);

            $college = College::create($cData);

            foreach ($departments as $dData) {
                $programs = $dData['programs'] ?? [];
                unset($dData['programs']);

                $dData['college_id'] = $college->id;
                $department = Department::create($dData);
                $createdDepartments[$dData['slug']] = $department;

                foreach ($programs as $pData) {
                    $pData['department_id'] = $department->id;
                    $pData['is_active'] = true;
                    $program = Program::create($pData);
                    $createdPrograms[$pData['slug']] = $program;
                }
            }
        }

        // 2. Faculty Members & Profiles
        $facultyMembers = [
            [
                'name' => 'Prof. Dr. Ahmed Mansour',
                'email' => 'ahmed.mansour@university.edu.eg',
                'dept_slug' => 'civil-and-structural-engineering',
                'academic_title' => [
                    'en' => 'Professor of Structural Engineering & Dean',
                    'ar' => 'أستاذ الهندسة الإنشائية وعميد الكلية',
                ],
                'bio' => [
                    'en' => 'Prof. Ahmed Mansour has over 25 years of experience in structural earthquake engineering, rehabilitation of historic monuments, and mega-bridge design across the Middle East.',
                    'ar' => 'يمتلك أ.د. أحمد منصور خبرة تزيد عن 25 عاماً في هندسة الزلازل والمنشآت وتدعيم المباني الأثرية وتصميم الجسور العملاقة في الشرق الأوسط.',
                ],
                'research_interests' => [
                    'en' => ['Seismic Risk Mitigation', 'High-Performance Concrete', 'Structural Health Monitoring'],
                    'ar' => ['تقليل مخاطر الزلازل', 'الخرسانة فائقة الأداء', 'المراقبة الذكية لسلامة المنشآت'],
                ],
                'office_location' => [
                    'en' => 'Engineering Building A, Room 401',
                    'ar' => 'مبنى الهندسة (أ)، مكتب 401',
                ],
                'phone' => '+20 2 2789 1101',
                'avatar' => 'images/faculty/dr-ahmed-mansour.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Tarek El-Sayed',
                'email' => 'tarek.elsayed@university.edu.eg',
                'dept_slug' => 'civil-and-structural-engineering',
                'academic_title' => [
                    'en' => 'Associate Professor & Head of Civil Engineering',
                    'ar' => 'أستاذ مساعد ورئيس قسم الهندسة المدنية',
                ],
                'bio' => [
                    'en' => 'Dr. Tarek El-Sayed specializes in geotechnical foundation systems, deep tunneling, and smart sensor integration for civil infrastructure.',
                    'ar' => 'يتخصص د. طارق السيد في هندسة الأساسات العميقة والأنفاق ودمج الحساسات الذكية في مشاريع البنية التحتية.',
                ],
                'research_interests' => [
                    'en' => ['Deep Foundations', 'Soil-Structure Interaction', 'Tunneling Technologies'],
                    'ar' => ['الأساسات العميقة', 'تفاعل التربة والمنشأ', 'تكنولوجيا حفر الأنفاق'],
                ],
                'office_location' => [
                    'en' => 'Engineering Building A, Room 305',
                    'ar' => 'مبنى الهندسة (أ)، مكتب 305',
                ],
                'phone' => '+20 2 2789 1102',
                'avatar' => 'images/faculty/dr-tarek-elsayed.jpg',
                'is_featured' => false,
            ],
            [
                'name' => 'Dr. Mohamed Ragab',
                'email' => 'mohamed.ragab@university.edu.eg',
                'dept_slug' => 'electrical-and-communications-engineering',
                'academic_title' => [
                    'en' => 'Associate Professor & Head of Electrical Engineering',
                    'ar' => 'أستاذ مساعد ورئيس قسم الهندسة الكهربائية',
                ],
                'bio' => [
                    'en' => 'Expert in smart grid architectures, renewable energy integration, and high-voltage power transmission stability.',
                    'ar' => 'خبير في شبكات الطاقة الذكية ودمج محطات الطاقة المتجددة واستقرار خطوط نقل الجهد الفائق.',
                ],
                'research_interests' => [
                    'en' => ['Smart Microgrids', 'Photovoltaic Integration', 'Power System Protection'],
                    'ar' => ['الشبكات الكهربية المصغرة الذكية', 'تكامل الخلايا الكهروضوئية', 'وقاية شبكات القوى'],
                ],
                'office_location' => [
                    'en' => 'Engineering Building B, Room 210',
                    'ar' => 'مبنى الهندسة (ب)، مكتب 210',
                ],
                'phone' => '+20 2 2789 1103',
                'avatar' => 'images/faculty/dr-mohamed-ragab.jpg',
                'is_featured' => false,
            ],
            [
                'name' => 'Dr. Samer Abdelrahman',
                'email' => 'samer.abdelrahman@university.edu.eg',
                'dept_slug' => 'mechatronics-and-robotics-engineering',
                'academic_title' => [
                    'en' => 'Assistant Professor of Robotics & Mechatronics',
                    'ar' => 'مدرس هندسة الروبوتات والميكاترونكس',
                ],
                'bio' => [
                    'en' => 'Leading researcher in autonomous mobile robots, robotic arms for surgical automation, and computer vision guidance.',
                    'ar' => 'باحث رائد في الروبوتات المتحركة ذاتية القيادة، والأذرع الروبوتية الجراحية، والرؤية الآلية الموجهة.',
                ],
                'research_interests' => [
                    'en' => ['Autonomous Navigation', 'Bio-inspired Robotics', 'Embedded Real-time Control'],
                    'ar' => ['الملاحة الذاتية للروبوتات', 'الروبوتات المستوحاة حيوياً', 'أنظمة التحكم المدمجة الفورية'],
                ],
                'office_location' => [
                    'en' => 'Robotics Innovation Center, Lab 3',
                    'ar' => 'مركز أبحاث الروبوتات، معمل 3',
                ],
                'phone' => '+20 2 2789 1104',
                'avatar' => 'images/faculty/dr-samer-abdelrahman.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Prof. Dr. Khaled Mostafa',
                'email' => 'khaled.mostafa@university.edu.eg',
                'dept_slug' => 'computer-science-and-software-engineering',
                'academic_title' => [
                    'en' => 'Professor of Computer Science & Dean',
                    'ar' => 'أستاذ علوم الحاسب وعميد الكلية',
                ],
                'bio' => [
                    'en' => 'Pioneer in distributed parallel systems, quantum computing algorithms, and high-performance cloud clusters with over 70 published papers.',
                    'ar' => 'رائد في الأنظمة الموزعة المتوازية، وخوارزميات الحوسبة الكمية، وعناقيد الحوسبة السحابية عالية الأداء بأكثر من 70 بحثاً منشوراً.',
                ],
                'research_interests' => [
                    'en' => ['High-Performance Computing', 'Quantum Algorithms', 'Distributed Consensus'],
                    'ar' => ['الحوسبة عالية الأداء', 'الخوارزميات الكمية', 'بروتوكولات التوافق الموزعة'],
                ],
                'office_location' => [
                    'en' => 'CS & AI Complex, Dean Suite 501',
                    'ar' => 'مجمع الحاسبات والذكاء الاصطناعي، جناح العميد 501',
                ],
                'phone' => '+20 2 2789 1201',
                'avatar' => 'images/faculty/dr-khaled-mostafa.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Mona El-Khatib',
                'email' => 'mona.elkhatib@university.edu.eg',
                'dept_slug' => 'artificial-intelligence-and-data-science',
                'academic_title' => [
                    'en' => 'Professor & Head of AI Department',
                    'ar' => 'أستاذ ورئيس قسم الذكاء الاصطناعي',
                ],
                'bio' => [
                    'en' => 'Renowned scholar in deep generative models, Arabic natural language processing, and medical diagnostic AI imaging.',
                    'ar' => 'عالمة بارزة في النماذج التوليدية العميقة، ومعالجة اللغة العربية آلياً، والتشخيص الطبي المعتمد على الذكاء الاصطناعي.',
                ],
                'research_interests' => [
                    'en' => ['Large Language Models for Arabic', 'Medical Image Segmentation', 'Explainable AI (XAI)'],
                    'ar' => ['النماذج اللغوية الكبيرة للغة العربية', 'تحليل الصور الطبية بالذكاء الاصطناعي', 'الذكاء الاصطناعي القابل للتفسير'],
                ],
                'office_location' => [
                    'en' => 'CS & AI Complex, Room 412',
                    'ar' => 'مجمع الحاسبات والذكاء الاصطناعي، مكتب 412',
                ],
                'phone' => '+20 2 2789 1202',
                'avatar' => 'images/faculty/dr-mona-elkhatib.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Hassan Youssef',
                'email' => 'hassan.youssef@university.edu.eg',
                'dept_slug' => 'computer-science-and-software-engineering',
                'academic_title' => [
                    'en' => 'Associate Professor & Head of Software Engineering',
                    'ar' => 'أستاذ مساعد ورئيس قسم هندسة البرمجيات',
                ],
                'bio' => [
                    'en' => 'Specializes in microservices architecture, automated software testing, container orchestration, and serverless engineering.',
                    'ar' => 'متخصص في معمارية الخدمات المصغرة، واختبار البرمجيات الآلي، وإدارة الحاويات السحابية، والأنظمة الخادمة عديمة الخادم.',
                ],
                'research_interests' => [
                    'en' => ['Software Architecture Evolution', 'DevSecOps Automation', 'Cloud Cost Optimization'],
                    'ar' => ['تطور معمارية البرمجيات', 'أتمتة DevSecOps', 'تحسين تكاليف الموارد السحابية'],
                ],
                'office_location' => [
                    'en' => 'CS & AI Complex, Room 318',
                    'ar' => 'مجمع الحاسبات والذكاء الاصطناعي، مكتب 318',
                ],
                'phone' => '+20 2 2789 1203',
                'avatar' => 'images/faculty/dr-hassan-youssef.jpg',
                'is_featured' => false,
            ],
            [
                'name' => 'Dr. Omar Farouk',
                'email' => 'omar.farouk@university.edu.eg',
                'dept_slug' => 'cybersecurity-and-networks',
                'academic_title' => [
                    'en' => 'Associate Professor & Head of Cybersecurity',
                    'ar' => 'أستاذ مساعد ورئيس قسم الأمن السيبراني',
                ],
                'bio' => [
                    'en' => 'Certified Ethical Hacker (CEH) and researcher in zero-trust network architectures, post-quantum cryptography, and malware reverse engineering.',
                    'ar' => 'خبير اختراق أخلاقي معتمد وباحث في معماريات انعدام الثقة (Zero-Trust)، والتشفير ما بعد الكم، والهندسة العكسية للبرمجيات الخبيثة.',
                ],
                'research_interests' => [
                    'en' => ['Zero-Trust Architecture', 'Post-Quantum Cryptography', 'Threat Intelligence & Hunting'],
                    'ar' => ['بنية انعدام الثقة الأمنية', 'التشفير المقاوم للحواسيب الكمية', 'صيد واستخبارات التهديدات السيبرانية'],
                ],
                'office_location' => [
                    'en' => 'CS & AI Complex, Cybersecurity Lab 2',
                    'ar' => 'مجمع الحاسبات، معمل الأمن السيبراني 2',
                ],
                'phone' => '+20 2 2789 1204',
                'avatar' => 'images/faculty/dr-omar-farouk.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Prof. Dr. Noha Soliman',
                'email' => 'noha.soliman@university.edu.eg',
                'dept_slug' => 'accounting-and-auditing',
                'academic_title' => [
                    'en' => 'Professor of International Finance & Dean',
                    'ar' => 'أستاذ التمويل الدولي وعميد الكلية',
                ],
                'bio' => [
                    'en' => 'Renowned corporate finance consultant with international advisory experience across sovereign wealth funds and banking groups.',
                    'ar' => 'استشارية بارزة في تمويل الشركات تمتلك خبرات استشارية دولية مع صناديق سيادية ومجموعات مصرفية كبرى.',
                ],
                'research_interests' => [
                    'en' => ['Sustainable Green Finance', 'Capital Market Dynamics', 'Corporate Governance'],
                    'ar' => ['التمويل الأخضر المستدام', 'ديناميكيات أسواق المال', 'حوكمة الشركات والمؤسسات'],
                ],
                'office_location' => [
                    'en' => 'Business School Building, Dean Office 301',
                    'ar' => 'مبنى كلية إدارة الأعمال، مكتب العميد 301',
                ],
                'phone' => '+20 2 2789 1301',
                'avatar' => 'images/faculty/dr-noha-soliman.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Rania Mahmoud',
                'email' => 'rania.mahmoud@university.edu.eg',
                'dept_slug' => 'marketing-and-digital-commerce',
                'academic_title' => [
                    'en' => 'Associate Professor & Head of Marketing',
                    'ar' => 'أستاذ مساعد ورئيس قسم التسويق',
                ],
                'bio' => [
                    'en' => 'Specializes in behavioral analytics, AI-driven marketing campaigns, brand neuroscience, and digital customer journeys.',
                    'ar' => 'متخصصة في تحليلات السلوك الاستهلاكي، والحملات التسويقية المدعومة بالذكاء الاصطناعي، وعلم الأعصاب التسويقي.',
                ],
                'research_interests' => [
                    'en' => ['Consumer Neuromarketing', 'AI in Personalization', 'Cross-Border E-Commerce'],
                    'ar' => ['التسويق العصبي وسلوك المستهلك', 'الذكاء الاصطناعي في التخصيص', 'التجارة الإلكترونية الدولية'],
                ],
                'office_location' => [
                    'en' => 'Business School Building, Room 215',
                    'ar' => 'مبنى كلية إدارة الأعمال، مكتب 215',
                ],
                'phone' => '+20 2 2789 1302',
                'avatar' => 'images/faculty/dr-rania-mahmoud.jpg',
                'is_featured' => false,
            ],
            [
                'name' => 'Prof. Dr. Layla Hamdi',
                'email' => 'layla.hamdi@university.edu.eg',
                'dept_slug' => 'pharmaceutical-chemistry-and-drug-discovery',
                'academic_title' => [
                    'en' => 'Professor of Medicinal Chemistry & Dean',
                    'ar' => 'أستاذ الكيمياء الدوائية وعميد الكلية',
                ],
                'bio' => [
                    'en' => 'Internationally recognized pioneer in targeted anticancer drug synthesis, computer-aided molecular docking, and drug delivery nanocarriers.',
                    'ar' => 'رائدة معترف بها دولياً في تخليق الأدوية الموجهة للسرطان، وتصميم الأدوية بالحاسوب، وحوامل النانو الدوائية.',
                ],
                'research_interests' => [
                    'en' => ['Targeted Cancer Therapeutics', 'Kinase Inhibitors Design', 'Liposomal Nanocarriers'],
                    'ar' => ['العلاجات الموجهة للأورام السرطانية', 'مثبطات إنزيم الكيناز الدوائية', 'حوامل النانو الليبوزومية'],
                ],
                'office_location' => [
                    'en' => 'Pharmacy Complex, Dean Suite 101',
                    'ar' => 'مجمع الصيدلة، جناح العميد 101',
                ],
                'phone' => '+20 2 2789 1401',
                'avatar' => 'images/faculty/dr-layla-hamdi.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Dr. Yasser Fathy',
                'email' => 'yasser.fathy@university.edu.eg',
                'dept_slug' => 'clinical-pharmacy-and-pharmacology',
                'academic_title' => [
                    'en' => 'Associate Professor & Head of Clinical Pharmacy',
                    'ar' => 'أستاذ مساعد ورئيس قسم الصيدلة الإكلينيكية',
                ],
                'bio' => [
                    'en' => 'Senior clinical consultant specializing in pediatric oncology dosing, antimicrobial stewardship programs, and therapeutic drug monitoring.',
                    'ar' => 'استشاري سريري أول متخصص في جرعات أورام الأطفال، وبرامج ترشيد استخدام المضادات الحيوية، والمراقبة العلاجية للدواء.',
                ],
                'research_interests' => [
                    'en' => ['Antimicrobial Stewardship', 'Pediatric Pharmacotherapy', 'Therapeutic Drug Monitoring'],
                    'ar' => ['ترشيد استخدام المضادات الحيوية', 'العلاج الدوائي للأطفال', 'المراقبة العلاجية لتركيزات الدواء'],
                ],
                'office_location' => [
                    'en' => 'Pharmacy Complex, Room 220',
                    'ar' => 'مجمع الصيدلة، مكتب 220',
                ],
                'phone' => '+20 2 2789 1402',
                'avatar' => 'images/faculty/dr-yasser-fathy.jpg',
                'is_featured' => false,
            ],
        ];

        foreach ($facultyMembers as $fMember) {
            $user = User::firstOrCreate(
                ['email' => $fMember['email']],
                [
                    'name' => $fMember['name'],
                    'password' => Hash::make('Faculty@2025!'),
                    'email_verified_at' => now(),
                ]
            );

            $department = $createdDepartments[$fMember['dept_slug']] ?? null;

            if ($department) {
                FacultyProfile::create([
                    'user_id' => $user->id,
                    'department_id' => $department->id,
                    'academic_title' => $fMember['academic_title'],
                    'bio' => $fMember['bio'],
                    'research_interests' => $fMember['research_interests'],
                    'email' => $fMember['email'],
                    'phone' => $fMember['phone'],
                    'office_location' => $fMember['office_location'],
                    'avatar' => $fMember['avatar'],
                    'cv_path' => 'cvs/' . $fMember['dept_slug'] . '-profile.pdf',
                    'is_featured' => $fMember['is_featured'],
                ]);
            }
        }

        // 3. Academic Terms
        $termsData = [
            [
                'name' => [
                    'en' => 'Fall Semester 2024/2025',
                    'ar' => 'فصل الخريف 2024 / 2025',
                ],
                'academic_year' => '2024/2025',
                'semester' => 'Fall',
                'is_current' => false,
            ],
            [
                'name' => [
                    'en' => 'Spring Semester 2024/2025',
                    'ar' => 'فصل الربيع 2024 / 2025',
                ],
                'academic_year' => '2024/2025',
                'semester' => 'Spring',
                'is_current' => false,
            ],
            [
                'name' => [
                    'en' => 'Fall Semester 2025/2026',
                    'ar' => 'فصل الخريف 2025 / 2026',
                ],
                'academic_year' => '2025/2026',
                'semester' => 'Fall',
                'is_current' => true,
            ],
        ];

        $createdTerms = [];
        foreach ($termsData as $tData) {
            $createdTerms[] = AcademicTerm::create($tData);
        }

        $fall2024 = $createdTerms[0];
        $spring2025 = $createdTerms[1];

        // 4. Sample Students & Course Results
        $sampleStudents = [
            [
                'name' => 'Youssef Karim Al-Attar',
                'email' => 'student.youssef@student.university.edu.eg',
                'student_id' => '20241001',
                'program_slug' => 'bsc-artificial-intelligence-machine-learning',
                'current_level' => 2,
                'cumulative_gpa' => 3.88,
                'courses' => [
                    [
                        'term' => $fall2024,
                        'code' => 'CS101',
                        'name' => [
                            'en' => 'Introduction to Computer Science & Python',
                            'ar' => 'مقدمة في علوم الحاسب والبرمجة ببايثون',
                        ],
                        'credit_hours' => 3,
                        'grade' => 'A+',
                        'grade_points' => 4.00,
                    ],
                    [
                        'term' => $fall2024,
                        'code' => 'MATH101',
                        'name' => [
                            'en' => 'Calculus & Analytical Geometry I',
                            'ar' => 'التفاضل والتكامل والهندسة التحليلية 1',
                        ],
                        'credit_hours' => 3,
                        'grade' => 'A',
                        'grade_points' => 3.75,
                    ],
                    [
                        'term' => $fall2024,
                        'code' => 'PHYS101',
                        'name' => [
                            'en' => 'General Physics & Electromagnetism',
                            'ar' => 'الفيزياء العامة والكهرومغناطيسية',
                        ],
                        'credit_hours' => 3,
                        'grade' => 'A',
                        'grade_points' => 3.75,
                    ],
                    [
                        'term' => $spring2025,
                        'code' => 'AI201',
                        'name' => [
                            'en' => 'Foundations of Machine Learning & Statistics',
                            'ar' => 'أسس تعلم الآلة والإحصاء الرياضي',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'A+',
                        'grade_points' => 4.00,
                    ],
                    [
                        'term' => $spring2025,
                        'code' => 'CS202',
                        'name' => [
                            'en' => 'Data Structures & Algorithms',
                            'ar' => 'هياكل البيانات والخوارزميات',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'A',
                        'grade_points' => 3.90,
                    ],
                ],
            ],
            [
                'name' => 'Salma Mahmoud El-Shamy',
                'email' => 'student.salma@student.university.edu.eg',
                'student_id' => '20241002',
                'program_slug' => 'bsc-software-engineering-cloud',
                'current_level' => 3,
                'cumulative_gpa' => 3.72,
                'courses' => [
                    [
                        'term' => $fall2024,
                        'code' => 'SE301',
                        'name' => [
                            'en' => 'Software Design Patterns & Architecture',
                            'ar' => 'أنماط تصميم ومعمارية البرمجيات',
                        ],
                        'credit_hours' => 3,
                        'grade' => 'A',
                        'grade_points' => 3.75,
                    ],
                    [
                        'term' => $fall2024,
                        'code' => 'CLOUD201',
                        'name' => [
                            'en' => 'Cloud Infrastructure & AWS Services',
                            'ar' => 'البنية التحتية السحابية وخدمات AWS',
                        ],
                        'credit_hours' => 3,
                        'grade' => 'A-',
                        'grade_points' => 3.50,
                    ],
                    [
                        'term' => $spring2025,
                        'code' => 'DEVOPS302',
                        'name' => [
                            'en' => 'CI/CD Pipelines & Containerization',
                            'ar' => 'خطوط الإنتاج المؤتمتة وتقنيات الحاويات',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'A',
                        'grade_points' => 3.90,
                    ],
                ],
            ],
            [
                'name' => 'Ziad Ahmed Farag',
                'email' => 'student.ziad@student.university.edu.eg',
                'student_id' => '20241003',
                'program_slug' => 'bsc-civil-structural-engineering',
                'current_level' => 2,
                'cumulative_gpa' => 3.45,
                'courses' => [
                    [
                        'term' => $fall2024,
                        'code' => 'CIV101',
                        'name' => [
                            'en' => 'Structural Mechanics & Stress Analysis',
                            'ar' => 'ميكانيكا الإنشاءات وتحليل الإجهادات',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'B+',
                        'grade_points' => 3.30,
                    ],
                    [
                        'term' => $spring2025,
                        'code' => 'CIV202',
                        'name' => [
                            'en' => 'Reinforced Concrete Design I',
                            'ar' => 'تصميم المنشآت الخرسانية المسلحة 1',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'A-',
                        'grade_points' => 3.60,
                    ],
                ],
            ],
            [
                'name' => 'Fatima Ezzat El-Gendy',
                'email' => 'student.fatima@student.university.edu.eg',
                'student_id' => '20241004',
                'program_slug' => 'pharmd-clinical-pharmacy',
                'current_level' => 2,
                'cumulative_gpa' => 3.95,
                'courses' => [
                    [
                        'term' => $fall2024,
                        'code' => 'PHAR101',
                        'name' => [
                            'en' => 'Pharmaceutical Organic Chemistry',
                            'ar' => 'الكيمياء العضوية الصيدلانية',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'A+',
                        'grade_points' => 4.00,
                    ],
                    [
                        'term' => $spring2025,
                        'code' => 'PHAR202',
                        'name' => [
                            'en' => 'Human Pharmacology & Therapeutics I',
                            'ar' => 'علم الأدوية والعلاجيات 1',
                        ],
                        'credit_hours' => 4,
                        'grade' => 'A+',
                        'grade_points' => 4.00,
                    ],
                ],
            ],
            [
                'name' => 'Marwan Hisham Nabil',
                'email' => 'student.marwan@student.university.edu.eg',
                'student_id' => '20241005',
                'program_slug' => 'bsc-digital-marketing-ecommerce',
                'current_level' => 1,
                'cumulative_gpa' => 3.60,
                'courses' => [
                    [
                        'term' => $spring2025,
                        'code' => 'MKT101',
                        'name' => [
                            'en' => 'Principles of Modern Digital Marketing',
                            'ar' => 'مبادئ التسويق الرقمي الحديث',
                        ],
                        'credit_hours' => 3,
                        'grade' => 'A-',
                        'grade_points' => 3.60,
                    ],
                ],
            ],
        ];

        foreach ($sampleStudents as $sInfo) {
            $studentUser = User::firstOrCreate(
                ['email' => $sInfo['email']],
                [
                    'name' => $sInfo['name'],
                    'password' => Hash::make('Student@2025!'),
                    'email_verified_at' => now(),
                ]
            );

            $program = $createdPrograms[$sInfo['program_slug']] ?? null;

            if ($program) {
                $studentRecord = StudentRecord::create([
                    'student_id_number' => $sInfo['student_id'],
                    'user_id' => $studentUser->id,
                    'program_id' => $program->id,
                    'current_level' => $sInfo['current_level'],
                    'cumulative_gpa' => $sInfo['cumulative_gpa'],
                    'status' => 'enrolled',
                ]);

                foreach ($sInfo['courses'] as $cResult) {
                    CourseResult::create([
                        'student_record_id' => $studentRecord->id,
                        'academic_term_id' => $cResult['term']->id,
                        'course_code' => $cResult['code'],
                        'course_name' => $cResult['name'],
                        'credit_hours' => $cResult['credit_hours'],
                        'grade' => $cResult['grade'],
                        'grade_points' => $cResult['grade_points'],
                        'is_published' => true,
                    ]);
                }
            }
        }
    }
}
