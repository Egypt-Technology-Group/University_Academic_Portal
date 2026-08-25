// Realistic fallback mock data for EgyiTech University portal

export const mockColleges = [
  {
    id: 1,
    name: {
      ar: 'كلية الحاسبات والذكاء الاصطناعي',
      en: 'Faculty of Computers & Artificial Intelligence'
    },
    slug: 'faculty-of-computers-and-ai',
    dean_name: {
      ar: 'أ.د. طارق محمود الشناوي',
      en: 'Prof. Dr. Tarek Mahmoud El-Shennawy'
    },
    about: {
      ar: 'صرح أكاديمي رائد يعنى بتعليم وتطوير أحدث تقنيات الحوسبة المتقدمة والذكاء الاصطناعي والأمن السيبراني، مجهز بأحدث معامل الحوسبة السحابية ومختبرات الروبوتات.',
      en: 'A premier faculty dedicated to advancing modern computing, AI architectures, and cybersecurity, equipped with high-performance cloud labs and robotics testing centers.'
    },
    vision: {
      ar: 'الريادة الإقليمية والدولية في التعليم والبحث العلمي في مجالات الذكاء الاصطناعي والتحول الرقمي.',
      en: 'Regional and global leadership in artificial intelligence research, software engineering, and digital transformation.'
    },
    mission: {
      ar: 'إعداد كوادر تقنية مبتكرة قادرة على المنافسة في أسواق العمل المحلية والعالمية وحل مشكلات المجتمع التكنولوجية المعقدة.',
      en: 'Graduating highly innovative engineers and computer scientists capable of competing globally and solving complex technological challenges.'
    },
    banner_image: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=1200&q=80',
    departments_count: 4,
    programs_count: 6,
    departments: [
      {
        id: 101,
        college_id: 1,
        name: { ar: 'علوم الحاسب والذكاء الاصطناعي', en: 'Computer Science & AI' },
        slug: 'computer-science-ai',
        programs: [
          { id: 1, slug: 'artificial-intelligence-data-science', name: { ar: 'الذكاء الاصطناعي وعلم البيانات', en: 'AI & Data Science' }, degree_level: 'bachelor', credit_hours: 144, duration_years: 4 },
          { id: 2, slug: 'software-engineering', name: { ar: 'هندسة البرمجيات والأنظمة الذكية', en: 'Software Engineering' }, degree_level: 'bachelor', credit_hours: 140, duration_years: 4 }
        ]
      },
      {
        id: 102,
        college_id: 1,
        name: { ar: 'الأمن السيبراني والشبكات', en: 'Cybersecurity & Networks' },
        slug: 'cybersecurity-networks',
        programs: [
          { id: 3, slug: 'cybersecurity-defense', name: { ar: 'الأمن السيبراني والدفاع الرقمي', en: 'Cybersecurity & Digital Defense' }, degree_level: 'bachelor', credit_hours: 142, duration_years: 4 }
        ]
      },
      {
        id: 103,
        college_id: 1,
        name: { ar: 'نظم المعلومات الحيوية', en: 'Bioinformatics' },
        slug: 'bioinformatics-dept',
        programs: [
          { id: 4, slug: 'bioinformatics-systems', name: { ar: 'المعلوماتية الحيوية الطبية', en: 'Medical Bioinformatics' }, degree_level: 'bachelor', credit_hours: 138, duration_years: 4 }
        ]
      }
    ],
    faculty_profiles: [
      {
        id: 1,
        name: 'أ.د. طارق محمود الشناوي',
        academic_title: { ar: 'أستاذ الذكاء الاصطناعي وعميد الكلية', en: 'Professor of AI & Dean' },
        bio: { ar: 'أستاذ خبير بالتعلم العميق ورؤية الحاسوب، له أكثر من 60 بحثاً منشوراً في دوريات IEEE وACM.', en: 'Expert in Deep Learning and Computer Vision with 60+ publications in IEEE and ACM transactions.' },
        email: 'tarek.shennawy@egyitech.edu.eg',
        office_location: { ar: 'مبنى الحاسبات - الطابق الرابع - مكتب 402', en: 'CS Building - 4th Floor - Office 402' },
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
        is_featured: true
      },
      {
        id: 2,
        name: 'د. ياسمين خالد عبد الفتاح',
        academic_title: { ar: 'أستاذ مشارك - الأمن السيبراني', en: 'Associate Professor - Cybersecurity' },
        bio: { ar: 'رئيسة قسم الأمن السيبراني، مستشارة معتمدة في تقييم الثغرات وحماية البنية التحتية الحرجة.', en: 'Head of Cybersecurity Department, certified consultant in vulnerability assessment and critical infra security.' },
        email: 'yasmin.khaled@egyitech.edu.eg',
        office_location: { ar: 'مبنى الحاسبات - الطابق الثالث - مكتب 315', en: 'CS Building - 3rd Floor - Office 315' },
        avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80',
        is_featured: true
      }
    ]
  },
  {
    id: 2,
    name: {
      ar: 'كلية الهندسة والتكنولوجيا المتقدمة',
      en: 'Faculty of Engineering & Advanced Technology'
    },
    slug: 'faculty-of-engineering-tech',
    dean_name: {
      ar: 'أ.د. حسام الدين عبد الرحمن',
      en: 'Prof. Dr. Hossam El-Din Abdelrahman'
    },
    about: {
      ar: 'كلية رائدة في مجالات الطاقة المتجددة، الميكاترونكس، هندسة الطيران، والأنظمة الروبوتية المستقلة، بالتعاون مع كبرى الشركات الهندسية والصناعية.',
      en: 'A premier engineering school specializing in Mechatronics, Renewable Energy, Aerospace, and Autonomous Systems in partnership with industry leaders.'
    },
    vision: {
      ar: 'تقديم تعليم هندسي تطبيقي متطور يواكب الثورة الصناعية الرابعة والخامسة.',
      en: 'Delivering world-class engineering education aligned with Industry 4.0 and 5.0 revolutions.'
    },
    mission: {
      ar: 'تخريج مهندسين مبدعين يمتلكون المهارات الفنية والأخلاقية لبناء مشاريع البنية التحتية التكنولوجية المستدامة.',
      en: 'Fostering innovative engineers equipped with technical prowess to build sustainable technological infrastructure.'
    },
    banner_image: 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80',
    departments_count: 5,
    programs_count: 8,
    departments: [
      {
        id: 201,
        college_id: 2,
        name: { ar: 'هندسة الميكاترونكس والروبوتات', en: 'Mechatronics & Robotics' },
        slug: 'mechatronics-robotics',
        programs: [
          { id: 5, slug: 'mechatronics-engineering', name: { ar: 'هندسة الميكاترونكس والأنظمة الذكية', en: 'Mechatronics & Smart Systems' }, degree_level: 'bachelor', credit_hours: 160, duration_years: 5 }
        ]
      },
      {
        id: 202,
        college_id: 2,
        name: { ar: 'هندسة الطاقة المتجددة والمستدامة', en: 'Renewable Energy Engineering' },
        slug: 'renewable-energy',
        programs: [
          { id: 6, slug: 'renewable-energy-systems', name: { ar: 'هندسة الطاقة الخضراء والكهربائية', en: 'Green Energy & Power Systems' }, degree_level: 'bachelor', credit_hours: 160, duration_years: 5 }
        ]
      }
    ],
    faculty_profiles: [
      {
        id: 3,
        name: 'أ.د. حسام الدين عبد الرحمن',
        academic_title: { ar: 'أستاذ الميكاترونكس وعميد الكلية', en: 'Professor of Mechatronics & Dean' },
        bio: { ar: 'رائد في بحوث الروبوتات الصناعية والأنظمة المؤتمتة، مستشار لدى العديد من المصانع وشركات تصنيع السيارات.', en: 'Pioneer in industrial robotics and automated systems, consultant to leading automotive enterprises.' },
        email: 'hossam.abdelrahman@egyitech.edu.eg',
        office_location: { ar: 'مبنى الهندسة المركزي - مكتب 101', en: 'Main Engineering Bldg - Office 101' },
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
        is_featured: true
      }
    ]
  },
  {
    id: 3,
    name: {
      ar: 'كلية تكنولوجيا إدارة الأعمال والاقتصاد الرقمي',
      en: 'Faculty of Business Technology & Digital Economy'
    },
    slug: 'faculty-of-business-tech',
    dean_name: {
      ar: 'أ.د. رانيا نبيل السعدني',
      en: 'Prof. Dr. Rania Nabil El-Saadani'
    },
    about: {
      ar: 'تجمع الكلية بين الفكر الإداري الحديث والتحليل المالي الرقمي وتقنيات سلاسل الإمداد والتسويق الإلكتروني المبني على البيانات الضخمة.',
      en: 'Blending contemporary managerial science with FinTech, data-driven supply chain management, and digital marketing analytics.'
    },
    vision: {
      ar: 'تأهيل رواد أعمال ومديرين رقميين يقودون المؤسسات نحو الاقتصاد المعرفي.',
      en: 'Empowering digital leaders and entrepreneurs to spearhead the knowledge-based economy.'
    },
    mission: {
      ar: 'توفير بيئة تعليمية تجمع بين النظريات الاقتصادية وأدوات التكنولوجيا المالية الحديثة.',
      en: 'Providing an integrated curriculum that bridges business acumen with state-of-the-art FinTech systems.'
    },
    banner_image: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80',
    departments_count: 3,
    programs_count: 5,
    departments: [
      {
        id: 301,
        college_id: 3,
        name: { ar: 'التكنولوجيا المالية (FinTech)', en: 'Financial Technology (FinTech)' },
        slug: 'fintech-department',
        programs: [
          { id: 7, slug: 'fintech-digital-banking', name: { ar: 'التكنولوجيا المالية والخدمات المصرفية الرقمية', en: 'FinTech & Digital Banking' }, degree_level: 'bachelor', credit_hours: 132, duration_years: 4 }
        ]
      }
    ],
    faculty_profiles: []
  },
  {
    id: 4,
    name: {
      ar: 'كلية تكنولوجيا العلوم الصحية التطبيقية',
      en: 'Faculty of Applied Health Sciences Technology'
    },
    slug: 'faculty-of-health-sciences',
    dean_name: {
      ar: 'أ.د. خالد سليم الباز',
      en: 'Prof. Dr. Khaled Selim El-Baz'
    },
    about: {
      ar: 'تهتم بتخريج أخصائيين تقنيين مؤهلين في المختبرات الطبية، الأجهزة الطبية الحيوية، وتكنولوجيا التصوير الإشعاعي والرعاية المركزة.',
      en: 'Specializing in training certified medical laboratory technologists, biomedical imaging technicians, and respiratory therapy experts.'
    },
    vision: {
      ar: 'تحقيق أعلى معايير الجودة في التكنولوجيا الطبية والرعاية الصحية التطبيقية.',
      en: 'Achieving the highest standard in applied medical and healthcare technologies.'
    },
    mission: {
      ar: 'رفد القطاع الصحي بكفاءات تقنية متخصصة تساهم في تحسين جودة الخدمات الطبية.',
      en: 'Supplying healthcare infrastructure with highly skilled clinical technologists.'
    },
    banner_image: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=80',
    departments_count: 3,
    programs_count: 4,
    departments: [],
    faculty_profiles: []
  }
]

export const mockPrograms = [
  {
    id: 1,
    name: {
      ar: 'بكالوريوس الذكاء الاصطناعي وعلم البيانات',
      en: 'B.Sc. in Artificial Intelligence & Data Science'
    },
    slug: 'artificial-intelligence-data-science',
    degree_level: 'bachelor',
    credit_hours: 144,
    duration_years: 4,
    department_name: { ar: 'علوم الحاسب والذكاء الاصطناعي', en: 'Computer Science & AI' },
    college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' },
    overview: {
      ar: 'برنامج متميز يؤهل الطالب لامتلاك أسس التعلم الآلي، الشبكات العصبية العميقة، معالجة اللغات الطبيعية (NLP)، وتحليل البيانات الضخمة باستخدام أحدث الخوارزميات والبيئات السحابية.',
      en: 'An advanced curriculum providing in-depth mastery of Machine Learning, Deep Neural Networks, Natural Language Processing (NLP), and Big Data analytics using modern cloud frameworks.'
    },
    curriculum: {
      ar: 'السنة الأولى: مقدمة في البرمجة، الجبر الخطي، هياكل البيانات.\nالسنة الثانية: خوارزميات التعلم الآلي، قواعد البيانات المتقدمة، الإحصاء التطبيقي.\nالسنة الثالثة: التعلم العميق، الرؤية الحاسوبية، معالجة اللغات الطبيعية.\nالسنة الرابعة: مشروع التخرج، الحوسبة السحابية، نماذج الذكاء الاصطناعي التوليدي.',
      en: 'Year 1: Programming Fundamentals, Linear Algebra, Discrete Math.\nYear 2: Machine Learning Algorithms, Advanced Databases, Applied Statistics.\nYear 3: Deep Learning, Computer Vision, Natural Language Processing.\nYear 4: Capstone Graduation Project, Cloud Computing, Generative AI Systems.'
    },
    admission_requirements: {
      ar: 'شهادة الثانوية العامة المصرية (شعبة علمي رياضة أو علمي علوم بحد أدنى 75%) أو ما يعادلها من شهادات STEM، IGCSE، أو الدبلومة الأمريكية.',
      en: 'Egyptian Secondary School Certificate (Math or Science branch with min 75%) or equivalent STEM, IGCSE, or American High School Diploma.'
    },
    tuition_fees: {
      ar: '45,000 جنيه مصري لكل فصل دراسي (إمكانية التقسيط على دفعات بدون فوائد). بالإضافة لمنح تفوق تصل إلى 50% للطلاب المتميزين.',
      en: '45,000 EGP per semester (installment plans available). Merit-based scholarships up to 50% for high achievers.'
    },
    career_opportunities: {
      ar: 'مهندس ذكاء اصطناعي (AI Engineer)، عالم بيانات (Data Scientist)، مهندس تعلم آلي (MLOps Specialist)، محلل ذكاء أعمال (BI Consultant).',
      en: 'AI Engineer, Data Scientist, Machine Learning Operations (MLOps) Engineer, NLP Specialist, Business Intelligence Consultant.'
    },
    is_active: true
  },
  {
    id: 2,
    name: {
      ar: 'بكالوريوس هندسة البرمجيات والأنظمة الذكية',
      en: 'B.Sc. in Software Engineering & Smart Systems'
    },
    slug: 'software-engineering',
    degree_level: 'bachelor',
    credit_hours: 140,
    duration_years: 4,
    department_name: { ar: 'علوم الحاسب والذكاء الاصطناعي', en: 'Computer Science & AI' },
    college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' },
    overview: {
      ar: 'يركز البرنامج على تصميم وبناء الأنظمة البرمجية العملاقة والموزعة، إدارة دورة حياة البرمجيات، اختبار الجودة والأمان، وتطبيقات الحوسبة السحابية.',
      en: 'Focuses on designing and deploying large-scale distributed software architectures, full lifecycle software management, DevOps, and cloud-native engineering.'
    },
    curriculum: {
      ar: 'تطوير تطبيقات الويب، هندسة البرمجيات المتقدمة، الحوسبة السحابية وDevOps، أمن التطبيقات، إدارة المشروعات التكنولوجية.',
      en: 'Web & Mobile Systems, Software Architecture, Cloud Infrastructure & DevOps, Secure Coding, Agile Project Management.'
    },
    admission_requirements: {
      ar: 'شهادة الثانوية العامة شعبة علمي (رياضة/علوم) بنسبة 72% فأكثر أو الشهادات المعادلة.',
      en: 'High school certificate (Math/Science) with minimum 72% or equivalent international diploma.'
    },
    tuition_fees: {
      ar: '42,000 جنيه مصري لكل فصل دراسي.',
      en: '42,000 EGP per semester.'
    },
    career_opportunities: {
      ar: 'مهندس برمجيات أول، مهندس معماري سحابي (Cloud Architect)، مهندس DevOps، مدير مشاريع برمجية.',
      en: 'Senior Software Engineer, Cloud Architect, DevOps Engineer, Technical Product Manager.'
    },
    is_active: true
  },
  {
    id: 3,
    name: {
      ar: 'بكالوريوس الأمن السيبراني والدفاع الرقمي',
      en: 'B.Sc. in Cybersecurity & Digital Defense'
    },
    slug: 'cybersecurity-defense',
    degree_level: 'bachelor',
    credit_hours: 142,
    duration_years: 4,
    department_name: { ar: 'الأمن السيبراني والشبكات', en: 'Cybersecurity & Networks' },
    college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' },
    overview: {
      ar: 'تأهيل متخصصين لحماية الشبكات والبنى التحتية الحيوية، التحقيق الجنائي الرقمي، اختبار الاختراق الأخلاقي، وإدارة مراكز العمليات الأمنية (SOC).',
      en: 'Equips students with hands-on skills in SOC operations, penetration testing, digital forensics, cryptography, and network defense.'
    },
    curriculum: {
      ar: 'أمن الشبكات، اختبار الاختراق، التشفير والبروتوكولات، التحقيق الجنائي الرقمي، هندسة الحماية السحابية.',
      en: 'Network Security, Ethical Hacking, Cryptographic Protocols, Incident Response, Cloud Defense.'
    },
    admission_requirements: {
      ar: 'شهادة الثانوية العامة شعبة علمي بمجموع لا يقل عن 74%.',
      en: 'Secondary school certificate (Science/Math) with min 74%.'
    },
    tuition_fees: {
      ar: '46,000 جنيه مصري لكل فصل دراسي.',
      en: '46,000 EGP per semester.'
    },
    career_opportunities: {
      ar: 'محلل أمن سيبراني (SOC Analyst)، مختبر اختراق (Penetration Tester)، محقق جنائي رقمي، مهندس أمن الشبكات.',
      en: 'Cybersecurity Analyst, SOC Engineer, Ethical Hacker, Digital Forensics Specialist, Security Architect.'
    },
    is_active: true
  },
  {
    id: 5,
    name: {
      ar: 'بكالوريوس هندسة الميكاترونكس والأنظمة الذكية',
      en: 'B.Sc. in Mechatronics & Smart Systems Engineering'
    },
    slug: 'mechatronics-engineering',
    degree_level: 'bachelor',
    credit_hours: 160,
    duration_years: 5,
    department_name: { ar: 'هندسة الميكاترونكس والروبوتات', en: 'Mechatronics & Robotics' },
    college_name: { ar: 'كلية الهندسة والتكنولوجيا المتقدمة', en: 'Faculty of Engineering & Tech' },
    overview: {
      ar: 'تخصص تكاملي يجمع بين الهندسة الميكانيكية، الإلكترونيات، أنظمة التحكم الآلي، والبرمجة لتصميم الروبوتات والسيارات الكهربائية والمصانع الذكية.',
      en: 'An integrative discipline merging mechanical, electronic, and control engineering with software to innovate robots, EVs, and automated manufacturing.'
    },
    curriculum: {
      ar: 'الدوائر الكهربائية، الميكانيكا الهندسية، معالجات التحكم الدقيقة، الروبوتات الصناعية، التصميم بمساعدة الحاسوب (CAD/CAM).',
      en: 'Circuit Analysis, Dynamics, Embedded Microcontrollers, Industrial Robotics, Smart Automation Systems.'
    },
    admission_requirements: {
      ar: 'ثانوية عامة شعبة علمي رياضة أو شهادات معادلة بحد أدنى 76%.',
      en: 'High school Math branch with minimum 76%.'
    },
    tuition_fees: {
      ar: '48,000 جنيه مصري لكل فصل دراسي.',
      en: '48,000 EGP per semester.'
    },
    career_opportunities: {
      ar: 'مهندس ميكاترونكس، مهندس روبوتات وأتمتة، مهندس صيانة أنظمة صناعية، مهندس سيارات ذاتية القيادة.',
      en: 'Mechatronics Engineer, Robotics Specialist, Automation Consultant, Autonomous Vehicles Systems Engineer.'
    },
    is_active: true
  },
  {
    id: 7,
    name: {
      ar: 'بكالوريوس التكنولوجيا المالية والخدمات المصرفية الرقمية',
      en: 'B.Sc. in FinTech & Digital Banking'
    },
    slug: 'fintech-digital-banking',
    degree_level: 'bachelor',
    credit_hours: 132,
    duration_years: 4,
    department_name: { ar: 'التكنولوجيا المالية (FinTech)', en: 'Financial Technology' },
    college_name: { ar: 'كلية تكنولوجيا إدارة الأعمال والاقتصاد الرقمي', en: 'Faculty of Business Tech' },
    overview: {
      ar: 'برنامج حديث يواكب التحول الرقمي المصرفي، تقنيات سلاسل الكتل (Blockchain)، أنظمة الدفع الإلكتروني، وتحليلات المخاطر المالية.',
      en: 'A cutting-edge degree addressing digital banking transformations, Blockchain assets, payment gateway architectures, and quantitative risk modeling.'
    },
    curriculum: {
      ar: 'مبادئ التمويل والاستثمار، البرمجة المالية بلغة بايثون، تكنولوجيا البلوك تشين، التحليل المالي الكمي، التشريعات المصرفية الرقمية.',
      en: 'Financial Markets, Python for Finance, Blockchain Architecture, Quantitative Risk, FinTech Regulatory Compliance.'
    },
    admission_requirements: {
      ar: 'الثانوية العامة (علمي أو أدبي) بحد أدنى 68%.',
      en: 'High School (Science or Arts) with minimum 68%.'
    },
    tuition_fees: {
      ar: '38,000 جنيه مصري لكل فصل دراسي.',
      en: '38,000 EGP per semester.'
    },
    career_opportunities: {
      ar: 'محلل تكنولوجيا مالية، مدير منتجات مصرفية رقمية، أخصائي استثمار رقمي، مستشار نظم دفع.',
      en: 'FinTech Analyst, Digital Banking Product Manager, Quantitative Risk Analyst, Payment Solutions Consultant.'
    },
    is_active: true
  },
  {
    id: 8,
    name: {
      ar: 'ماجستير الذكاء الاصطناعي والرؤية الحاسوبية',
      en: 'M.Sc. in Artificial Intelligence & Computer Vision'
    },
    slug: 'msc-ai-computer-vision',
    degree_level: 'master',
    credit_hours: 36,
    duration_years: 2,
    department_name: { ar: 'علوم الحاسب والذكاء الاصطناعي', en: 'Computer Science & AI' },
    college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' },
    overview: {
      ar: 'برنامج دراسات عليا متقدم يهدف إلى تمكين الباحثين والمهندسين من تطوير أبحاث أصيلة في مجالات الرؤية الحاسوبية والتعلم المعزز.',
      en: 'Postgraduate research program focusing on advanced computer vision, generative adversarial networks, and reinforcement learning.'
    },
    curriculum: {
      ar: 'مناهج البحث العلمي، الشبكات العصبية المتقدمة، معالجة الصور الطبية، أطروحة الماجستير البحثية.',
      en: 'Research Methodology, Advanced Deep Models, Medical Image Processing, Masters Research Thesis.'
    },
    admission_requirements: {
      ar: 'حاصل على بكالوريوس في علوم الحاسب أو الهندسة بتقدير جيد جداً على الأقل.',
      en: 'Bachelor degree in CS or Engineering with minimum GPA of 3.0 (Very Good).'
    },
    tuition_fees: {
      ar: '30,000 جنيه مصري للسنة الأكاديمية.',
      en: '30,000 EGP per academic year.'
    },
    career_opportunities: {
      ar: 'باحث رئيسي في الذكاء الاصطناعي، خبير رؤية حاسوبية، أستاذ جامعي ومحاضر.',
      en: 'Principal AI Researcher, Senior Computer Vision Scientist, Academic Lecturer.'
    },
    is_active: true
  }
]

export const mockNews = [
  {
    id: 1,
    title: {
      ar: 'جامعة إيجي تك تفوز بالمركز الأول في هاكاثون الابتكار والذكاء الاصطناعي للجامعات المصرية',
      en: 'EgyiTech University Clinches 1st Place in National University AI Innovation Hackathon'
    },
    slug: 'egyitech-wins-national-ai-hackathon',
    excerpt: {
      ar: 'حصد فريق من طلاب كلية الحاسبات والذكاء الاصطناعي المركز الأول بمشروع نظام التشخيص الطبي الذكي المبكر باستخدام الرؤية الحاسوبية.',
      en: 'A team of EgyiTech undergraduate students secured first place with an AI-powered clinical early diagnosis platform.'
    },
    body: {
      ar: 'في إنجاز أكاديمي جديد، فاز فريق طلاب كلية الحاسبات والذكاء الاصطناعي بجامعة إيجي تك بالمركز الأول في الهاكاثون الوطني للذكاء الاصطناعي والتحول الرقمي الذي نظمته وزارة الاتصالات وتكنولوجيا المعلومات بمشاركة 40 جامعة مصرية حكومية وخاصة.\n\nوقد قدم الفريق مشروعاً متكاملاً يعتمد على تقنيات التعلم العميق والرؤية الحاسوبية لتحليل صور الأشعة المقطعية والرنين المغناطيسي بدقة فائقة تتجاوز 98.4%، مما يساهم في الكشف المبكر عن الأورام والأمراض النادرة.\n\nوأعرب رئيس الجامعة عن فخره الكبير بهذا الإنجاز، مؤكداً استمرار دعم الجامعة لحاضنات الابتكار ومشاريع الطلاب التكنولوجية الواعدة.',
      en: 'In a prominent academic milestone, an undergraduate research team from EgyiTech Faculty of Computers & AI secured first place at the National AI & Digital Transformation Hackathon, organized with over 40 university contenders.\n\nThe team presented an end-to-end Deep Learning and Computer Vision diagnostic suite capable of detecting radiological anomalies with over 98.4% diagnostic precision.\n\nThe University President congratulated the students, reaffirming EgyiTech dedication to fostering incubator spaces and hands-on technological projects.'
    },
    featured_image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1200&q=80',
    is_featured: true,
    published_at: '2025-05-18T10:00:00Z',
    views_count: 1420,
    category: {
      id: 1,
      name: { ar: 'الابتكار والبحث العلمي', en: 'Research & Innovation' },
      slug: 'research-innovation'
    }
  },
  {
    id: 2,
    title: {
      ar: 'توقيع اتفاقية تعاون أكاديمي وصناعي مع كبرى الشركات الألمانية للطاقة المتجددة',
      en: 'Academic & Industrial Partnership Signed with Leading German Green Energy Conglomerate'
    },
    slug: 'partnership-german-renewable-energy',
    excerpt: {
      ar: 'الاتفاقية تشمل برامج تدريب صيفي، منحاً دراسية، وتمويل 10 مشاريع تخرج سنوياً لطلاب كلية الهندسة.',
      en: 'The strategic alliance encompasses overseas summer internships, student scholarships, and funding 10 capstone engineering projects.'
    },
    body: {
      ar: 'وقعت جامعة إيجي تك اتفاقية تعاون استراتيجي مشترك مع كبرى المؤسسات الألمانية المتخصصة في الطاقة الخضراء والأنظمة الهيدروجينية، تهدف إلى تدريب الطلاب وتوفير فرص توظيف دولية متميزة للخريجين.',
      en: 'EgyiTech University executed a bilateral cooperation protocol with premier German renewable energy developers to sponsor joint research labs and global career paths.'
    },
    featured_image: 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1200&q=80',
    is_featured: true,
    published_at: '2025-05-12T14:30:00Z',
    views_count: 980,
    category: {
      id: 2,
      name: { ar: 'الشراكات والمؤتمرات', en: 'Partnerships & Events' },
      slug: 'partnerships-events'
    }
  },
  {
    id: 3,
    title: {
      ar: 'مجلس الجامعة يعتمد خطة التحول الرقمي الشامل وإطلاق المنصة التعليمية السحابية الذكية',
      en: 'University Board Approves Comprehensive Digital Transformation Plan and Smart Cloud Portal'
    },
    slug: 'board-approves-digital-transformation',
    excerpt: {
      ar: 'تحديث كافة المعامل التكنولوجية وتوفير خوادم فائقة السرعة للتدريب العملي لجميع طلاب الكليات.',
      en: 'Modernization of all computing labs and provisioning high-performance cloud clusters for practical research.'
    },
    body: {
      ar: 'اعتمد مجلس جامعة إيجي تك في اجتماعه الدوري خطة التطوير المؤسسي للعام الأكاديمي الجديد، متضمنة تدشين السحابة الأكاديمية وربط قواعد البيانات العلمية مع كبريات دور النشر العالمية.',
      en: 'The University Supreme Council officially sanctioned the digital campus blueprint, rolling out federated cloud storage and indexing integrations with IEEE and Springer repositories.'
    },
    featured_image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
    is_featured: false,
    published_at: '2025-05-05T09:15:00Z',
    views_count: 750,
    category: {
      id: 3,
      name: { ar: 'الشؤون الأكاديمية', en: 'Academic Affairs' },
      slug: 'academic-affairs'
    }
  }
]

export const mockEvents = [
  {
    id: 1,
    title: {
      ar: 'المؤتمر الدولي الأول للذكاء الاصطناعي والهندسة المستدامة (EgyiTech-AI 2025)',
      en: '1st International Conference on AI & Sustainable Engineering (EgyiTech-AI 2025)'
    },
    slug: 'egyitech-ai-conference-2025',
    description: {
      ar: 'مؤتمر دولي يستضيف نخبة من الباحثين والخبراء من أكثر من 20 دولة لمناقشة أحدث تقنيات الروبوتات والذكاء التوليدي والطاقة النظيفة.',
      en: 'An international conference gathering scholars and industry leaders from 20+ countries to deliberate on GenAI, autonomous systems, and green tech.'
    },
    location: {
      ar: 'قاعة المؤتمرات الكبرى - الحرم الجامعي الرئيسي',
      en: 'Grand Auditorium - Main University Campus'
    },
    organizer: {
      ar: 'قطاع الدراسات العليا والبحوث',
      en: 'Postgraduate Studies & Research Sector'
    },
    cover_image: 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1000&q=80',
    start_time: '2025-09-15T09:00:00Z',
    end_time: '2025-09-17T17:00:00Z'
  },
  {
    id: 2,
    title: {
      ar: 'الملتقى التوظيفي السنوي ومعرض مشاريع التخرج التكنولوجية 2025',
      en: 'Annual Career Fair & Tech Graduation Capstone Showcase 2025'
    },
    slug: 'career-fair-capstone-showcase-2025',
    description: {
      ar: 'مشاركة أكثر من 60 شركة ومؤسسة تكنولوجية لعرض فرص العمل والتدريب واستقطاب خريجي وطلاب الجامعة المتميزين.',
      en: 'Featuring over 60 premier enterprise technology firms offering internships, employment contracts, and venture seed funding.'
    },
    location: {
      ar: 'المبنى الرياضي والمعارض - مجمع الكليات',
      en: 'Exhibition & Sports Arena - Faculty Complex'
    },
    organizer: {
      ar: 'مركز رعاية وتوظيف الخريجين',
      en: 'Career Development & Alumni Center'
    },
    cover_image: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1000&q=80',
    start_time: '2025-10-02T10:00:00Z',
    end_time: '2025-10-03T18:00:00Z'
  },
  {
    id: 3,
    title: {
      ar: 'ورشة عمل متقدمة: الأمن السيبراني واختبار اختراق البنى السحابية',
      en: 'Hands-on Workshop: Advanced Cloud Security & Pen-Testing'
    },
    slug: 'cloud-security-workshop',
    description: {
      ar: 'ورشة عمل تطبيقية عملية مكثفة مخصصة لطلاب المستوى الثالث والرابع في كليات الحاسبات والهندسة.',
      en: 'Intensive practical hands-on workshop tailored for junior and senior engineering & computing undergraduates.'
    },
    location: {
      ar: 'مختبر الأمن الرقمي - مبنى B - معمل 204',
      en: 'Cyber Defense Lab - Building B - Lab 204'
    },
    organizer: {
      ar: 'قسم الأمن السيبراني',
      en: 'Cybersecurity Department'
    },
    cover_image: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1000&q=80',
    start_time: '2025-10-20T11:00:00Z',
    end_time: '2025-10-20T16:00:00Z'
  }
]

export const mockAnnouncements = [
  {
    id: 1,
    title: {
      ar: 'فتح باب التقديم المبكر للعام الأكاديمي 2025/2026 بخصم 15% على المصروفات الدراسية',
      en: 'Early Bird Admissions Open for 2025/2026 with a 15% Tuition Discount Incentive'
    },
    content: {
      ar: 'تعلن إدارة القبول والتسجيل عن فتح باب التقدم للشهادات الثانوية العامة والشهادات المعادلة والتحويلات للعام الدراسي الجديد.',
      en: 'Admissions & Registration announces early enrollment for High School & transfer applicants for the upcoming academic cycle.'
    },
    priority: 'urgent',
    target_audience: 'all',
    is_active: true,
    created_at: '2025-05-20T08:00:00Z'
  },
  {
    id: 2,
    title: {
      ar: 'إعلان جدول امتحانات منتصف الفصل الدراسي (Midterm) لجميع كليات الجامعة',
      en: 'Midterm Examination Schedule Released for All Academic Faculties'
    },
    content: {
      ar: 'يرجى من جميع الطلاب مراجعة مركز الوثائق لتحميل الجداول الدراسية وأماكن اللجان.',
      en: 'All enrolled students are advised to download their respective timetables and hall assignments from the Document Center.'
    },
    priority: 'pinned',
    target_audience: 'students',
    is_active: true,
    created_at: '2025-05-15T08:00:00Z'
  }
]

export const mockDocuments = [
  {
    id: 1,
    category: 'bylaws',
    title: {
      ar: 'اللائحة التنفيذية الموحدة لنظام الساعات المعتمدة (بكالوريوس)',
      en: 'Credit Hours Unified Bylaws & Academic Regulations (Undergraduate)'
    },
    file_path: 'bylaws/undergraduate_credit_hours_bylaw_2025.pdf',
    file_size: '2.8 MB',
    file_type: 'PDF',
    download_count: 3420
  },
  {
    id: 2,
    category: 'schedules',
    title: {
      ar: 'جدول المحاضرات والتدريبات العملية - الفصل الدراسي الأول 2025/2026',
      en: 'Lecture & Practical Laboratory Timetable - Fall Semester 2025/2026'
    },
    file_path: 'schedules/fall_2025_lectures_schedule.pdf',
    file_size: '1.4 MB',
    file_type: 'PDF',
    download_count: 4890
  },
  {
    id: 3,
    category: 'forms',
    title: {
      ar: 'استمارة طلب التحويل الداخلي بين البرامج الأكاديمية والكليات',
      en: 'Internal Program & College Transfer Request Form'
    },
    file_path: 'forms/internal_transfer_form.pdf',
    file_size: '520 KB',
    file_type: 'PDF',
    download_count: 1150
  },
  {
    id: 4,
    category: 'guides',
    title: {
      ar: 'الدليل الإرشادي الشامل للطالب المستجد - جامعة إيجي تك',
      en: 'Comprehensive Freshman Student Orientation Handbook'
    },
    file_path: 'guides/freshman_student_handbook_2025.pdf',
    file_size: '6.1 MB',
    file_type: 'PDF',
    download_count: 5670
  },
  {
    id: 5,
    category: 'calendar',
    title: {
      ar: 'التقويم الزمني الأكاديمي المعتمد للعام الجامعي 2025/2026',
      en: 'Approved University Academic Calendar 2025/2026'
    },
    file_path: 'calendar/academic_calendar_2025_2026.pdf',
    file_size: '880 KB',
    file_type: 'PDF',
    download_count: 2780
  }
]

export const mockFaculty = [
  {
    id: 1,
    name: 'أ.د. طارق محمود الشناوي',
    academic_title: { ar: 'أستاذ الذكاء الاصطناعي وعميد كلية الحاسبات', en: 'Professor of AI & Dean' },
    rank: 'prof',
    department_id: 101,
    department: { name: { ar: 'علوم الحاسب والذكاء الاصطناعي', en: 'Computer Science & AI' } },
    college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' },
    bio: {
      ar: 'أستاذ الذكاء الاصطناعي، حاصل على الدكتوراه من جامعة مانشستر، نشر أكثر من 65 بحثاً دولياً، وأشرف على أكثر من 30 رسالة ماجستير ودكتوراه في مجالات الرؤية الحاسوبية والروبوتات الذكية.',
      en: 'Professor of Artificial Intelligence with Ph.D. from University of Manchester. Authored 65+ peer-reviewed papers in Deep Learning, Computer Vision, and Robotics.'
    },
    research_interests: {
      ar: 'التعلم العميق، الرؤية الحاسوبية، المركبات ذاتية القيادة، معالجة الصور الطبية.',
      en: 'Deep Learning, Computer Vision, Autonomous Navigation, Medical Imaging Diagnostics.'
    },
    email: 'tarek.shennawy@egyitech.edu.eg',
    phone: '+20 2 2456 7891 (Ext. 402)',
    office_location: { ar: 'مبنى الحاسبات - الطابق الرابع - مكتب 402', en: 'CS Building - 4th Floor - Room 402' },
    avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    cv_path: '/files/cv_tarek_shennawy.pdf',
    is_featured: true
  },
  {
    id: 2,
    name: 'د. ياسمين خالد عبد الفتاح',
    academic_title: { ar: 'أستاذ مشارك - أمن المعلومات والشبكات', en: 'Associate Professor - Cybersecurity' },
    rank: 'assocProf',
    department_id: 102,
    department: { name: { ar: 'الأمن السيبراني والشبكات', en: 'Cybersecurity & Networks' } },
    college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' },
    bio: {
      ar: 'أستاذ مشارك ورئيس قسم الأمن السيبراني، خبيرة معتمدة في التحقيق الجنائي الرقمي ومستشارة لعدة جهات بنكية وحكومية في تأمين البنى التحتية الحرجة.',
      en: 'Associate Professor and Head of Cybersecurity Dept. Certified Digital Forensics investigator and cyber-defense advisor.'
    },
    research_interests: {
      ar: 'حماية السحابة، التشفير الكمي، التحقيق الجنائي الرقمي، اختبار الاختراق المؤسسي.',
      en: 'Cloud Security, Post-Quantum Cryptography, Digital Forensics, Enterprise Pen-Testing.'
    },
    email: 'yasmin.khaled@egyitech.edu.eg',
    phone: '+20 2 2456 7892 (Ext. 315)',
    office_location: { ar: 'مبنى الحاسبات - الطابق الثالث - مكتب 315', en: 'CS Building - 3rd Floor - Room 315' },
    avatar: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80',
    cv_path: '/files/cv_yasmin_khaled.pdf',
    is_featured: true
  },
  {
    id: 3,
    name: 'أ.د. حسام الدين عبد الرحمن',
    academic_title: { ar: 'أستاذ هندسة الميكاترونكس وعميد كلية الهندسة', en: 'Professor of Mechatronics & Dean' },
    rank: 'prof',
    department_id: 201,
    department: { name: { ar: 'هندسة الميكاترونكس والروبوتات', en: 'Mechatronics & Robotics' } },
    college_name: { ar: 'كلية الهندسة والتكنولوجيا المتقدمة', en: 'Faculty of Engineering & Tech' },
    bio: {
      ar: 'أستاذ التحكم الميكاتروني والأتمتة الصناعية، حاصل على الدكتوراه من جامعة ميونخ التقنية، يمتلك 4 براءات اختراع في مجال الأذرع الروبوتية الصناعية.',
      en: 'Professor of Mechatronic Control & Robotics with Ph.D. from TU Munich. Holds 4 patents in robotic manipulators.'
    },
    research_interests: {
      ar: 'الروبوتات الصناعية، الأتمتة الذكية، المركبات الكهربائية، إنترنت الأشياء الصناعي (IIoT).',
      en: 'Industrial Robotics, Smart Automation, Electric Vehicle Powertrains, Industrial IoT.'
    },
    email: 'hossam.abdelrahman@egyitech.edu.eg',
    phone: '+20 2 2456 7893 (Ext. 101)',
    office_location: { ar: 'مبنى الهندسة المركزي - الطابق الأول - مكتب 101', en: 'Engineering Bldg - 1st Floor - Room 101' },
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
    cv_path: '/files/cv_hossam_abdelrahman.pdf',
    is_featured: true
  },
  {
    id: 4,
    name: 'د. كريم أحمد عثمان',
    academic_title: { ar: 'أستاذ مساعد - هندسة الطاقة المتجددة', en: 'Assistant Professor - Green Energy' },
    rank: 'assistProf',
    department_id: 202,
    department: { name: { ar: 'هندسة الطاقة المتجددة والمستدامة', en: 'Renewable Energy' } },
    college_name: { ar: 'كلية الهندسة والتكنولوجيا المتقدمة', en: 'Faculty of Engineering & Tech' },
    bio: {
      ar: 'باحث ومحاضر في تقنيات الخلايا الشمسية وتوليد طاقة الرياح وتخزين الطاقة الكهروكيميائية للشبكات الذكية.',
      en: 'Researcher and lecturer in Photovoltaic Systems, Wind Turbine aerodynamics, and Grid-scale Battery Storage.'
    },
    research_interests: {
      ar: 'الشبكات الكهربائية الذكية (Smart Grids)، بطاريات الليثيوم المتقدمة، الهيدروجين الأخضر.',
      en: 'Smart Grids, Advanced Solid-State Batteries, Green Hydrogen Production.'
    },
    email: 'karim.osman@egyitech.edu.eg',
    phone: '+20 2 2456 7894 (Ext. 210)',
    office_location: { ar: 'مبنى الهندسة - الطابق الثاني - مكتب 210', en: 'Engineering Bldg - 2nd Floor - Room 210' },
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
    cv_path: '/files/cv_karim_osman.pdf',
    is_featured: false
  }
]

export const mockStudentResults = {
  '20241001': {
    student: {
      id: 1,
      student_id_number: '20241001',
      student_name: 'أحمد محمود السيد الشريف (Ahmed Mahmoud El-Sherif)',
      email: 'ahmed.elsherif.2024@egyitech.edu.eg',
      program: 'بكالوريوس الذكاء الاصطناعي وعلم البيانات (AI & Data Science)',
      current_level: 2,
      status: 'active'
    },
    cumulative_gpa: 3.84,
    term_gpa: 3.92,
    academic_term: 'فصل الربيع 2024 / 2025 (Spring 2025)',
    course_results: [
      { id: 101, course_code: 'CS201', course_name: { ar: 'هياكل البيانات والخوارزميات', en: 'Data Structures & Algorithms' }, credit_hours: 3, grade: 'A+', grade_points: 4.0, is_published: true },
      { id: 102, course_code: 'AI202', course_name: { ar: 'مبادئ الذكاء الاصطناعي', en: 'Foundations of Artificial Intelligence' }, credit_hours: 3, grade: 'A', grade_points: 3.7, is_published: true },
      { id: 103, course_code: 'MATH204', course_name: { ar: 'الجبر الخطي والاحتمالات التطبيقية', en: 'Linear Algebra & Applied Probability' }, credit_hours: 3, grade: 'A+', grade_points: 4.0, is_published: true },
      { id: 104, course_code: 'SWE203', course_name: { ar: 'هندسة البرمجيات المتقدمة', en: 'Advanced Software Engineering' }, credit_hours: 3, grade: 'A', grade_points: 3.7, is_published: true },
      { id: 105, course_code: 'DB205', course_name: { ar: 'قواعد البيانات العلائقية وNoSQL', en: 'Relational & NoSQL Database Systems' }, credit_hours: 3, grade: 'A+', grade_points: 4.0, is_published: true }
    ]
  },
  '20241002': {
    student: {
      id: 2,
      student_id_number: '20241002',
      student_name: 'سارة خالد المنشاوي (Sara Khaled El-Minshawi)',
      email: 'sara.elminshawi.2024@egyitech.edu.eg',
      program: 'بكالوريوس الأمن السيبراني والدفاع الرقمي (Cybersecurity)',
      current_level: 2,
      status: 'active'
    },
    cumulative_gpa: 3.75,
    term_gpa: 3.80,
    academic_term: 'فصل الربيع 2024 / 2025 (Spring 2025)',
    course_results: [
      { id: 201, course_code: 'SEC201', course_name: { ar: 'أسس التشفير وحماية البيانات', en: 'Cryptography & Data Protection' }, credit_hours: 3, grade: 'A', grade_points: 3.7, is_published: true },
      { id: 202, course_code: 'NET202', course_name: { ar: 'بروتوكولات الشبكات المتقدمة', en: 'Advanced Network Protocols' }, credit_hours: 3, grade: 'A+', grade_points: 4.0, is_published: true },
      { id: 203, course_code: 'SEC203', course_name: { ar: 'التحقيق الجنائي الرقمي', en: 'Digital Forensics & Evidence Analysis' }, credit_hours: 3, grade: 'B+', grade_points: 3.3, is_published: true },
      { id: 204, course_code: 'OS204', course_name: { ar: 'أمن نظم التشغيل (Linux/Windows)', en: 'Operating Systems Security' }, credit_hours: 3, grade: 'A', grade_points: 3.7, is_published: true }
    ]
  },
  '20242001': {
    student: {
      id: 3,
      student_id_number: '20242001',
      student_name: 'عمر إبراهيم حسن (Omar Ibrahim Hassan)',
      email: 'omar.hassan.2024@egyitech.edu.eg',
      program: 'بكالوريوس هندسة الميكاترونكس والأنظمة الذكية',
      current_level: 3,
      status: 'active'
    },
    cumulative_gpa: 3.65,
    term_gpa: 3.70,
    academic_term: 'فصل الربيع 2024 / 2025 (Spring 2025)',
    course_results: [
      { id: 301, course_code: 'MEC301', course_name: { ar: 'الروبوتات والأذرع المؤتمتة', en: 'Robotics & Industrial Manipulators' }, credit_hours: 4, grade: 'A', grade_points: 3.7, is_published: true },
      { id: 302, course_code: 'MEC302', course_name: { ar: 'أنظمة التحكم الرقمي والمتحكمات', en: 'Digital Control Systems & Microcontrollers' }, credit_hours: 3, grade: 'B+', grade_points: 3.3, is_published: true },
      { id: 303, course_code: 'MEC303', course_name: { ar: 'التصميم الميكانيكي بمساعدة الحاسب', en: 'Computer-Aided Mechanical Design' }, credit_hours: 3, grade: 'A+', grade_points: 4.0, is_published: true }
    ]
  }
}

export const mockApplications = {
  'APP-2025-A1B2C': {
    id: 1,
    application_number: 'APP-2025-A1B2C',
    cycle: 'الفصل الدراسي الأول 2025/2026',
    first_name: 'محمد',
    last_name: 'علي حسن',
    national_id: '30205120104567',
    email: 'mohamed.ali@gmail.com',
    phone: '+201012345678',
    high_school_score: 89.5,
    status: 'approved',
    notes: 'تم استيفاء كافة الشروط ومطابقة أصول الشهادات بنجاح، مرحباً بك في جامعة إيجي تك.',
    created_at: '2025-05-10T11:20:00Z',
    program: {
      name: { ar: 'بكالوريوس الذكاء الاصطناعي وعلم البيانات', en: 'B.Sc. in AI & Data Science' },
      degree_level: 'bachelor',
      college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' }
    },
    documents: [
      { id: 1, document_type: 'high_school_certificate', verification_status: 'verified' },
      { id: 2, document_type: 'national_id_card', verification_status: 'verified' },
      { id: 3, document_type: 'passport_photo', verification_status: 'verified' },
      { id: 4, document_type: 'birth_certificate', verification_status: 'verified' }
    ]
  },
  'APP-2025-X7K9P': {
    id: 2,
    application_number: 'APP-2025-X7K9P',
    cycle: 'الفصل الدراسي الأول 2025/2026',
    first_name: 'مريم',
    last_name: 'أحمد زكي',
    national_id: '30308220108912',
    email: 'mariam.zaki@gmail.com',
    phone: '+201123456789',
    high_school_score: 84.0,
    status: 'under_review',
    notes: 'الطلب قيد المراجعة والتدقيق بواسطة لجنة شؤون الطلاب.',
    created_at: '2025-05-18T14:45:00Z',
    program: {
      name: { ar: 'بكالوريوس الأمن السيبراني والدفاع الرقمي', en: 'B.Sc. in Cybersecurity' },
      degree_level: 'bachelor',
      college_name: { ar: 'كلية الحاسبات والذكاء الاصطناعي', en: 'Faculty of Computers & AI' }
    },
    documents: [
      { id: 5, document_type: 'high_school_certificate', verification_status: 'verified' },
      { id: 6, document_type: 'national_id_card', verification_status: 'pending' },
      { id: 7, document_type: 'passport_photo', verification_status: 'verified' }
    ]
  }
}
