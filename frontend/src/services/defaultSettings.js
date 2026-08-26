// Default factory settings for EgyiTech University portal

export const defaultSettings = {
  site_identity: {
    name: {
      ar: 'جامعة إيجي تك للتكنولوجيا والعلوم التطبيقية',
      en: 'EgyiTech University of Technology & Applied Sciences',
    },
    short_name: {
      ar: 'إيجي تك',
      en: 'EgyiTech',
    },
    slogan: {
      ar: 'منارة التعليم التكنولوجي والبحث العلمي والابتكار',
      en: 'Beacon of Technological Education, Scientific Research & Innovation',
    },
    motto: {
      ar: 'التميز الأكاديمي، المعرفة التطبيقية، وبناء قادة المستقبل',
      en: 'Academic Excellence, Applied Knowledge, and Empowering Future Leaders',
    },
    logo_url: '',
    favicon_url: '',
    established_year: '2024',
  },

  theme_colors: {
    primary_color: '#0A2540',
    primary_hover: '#0F3460',
    secondary_gold: '#C59B27',
    secondary_gold_light: '#D4AF37',
    accent_emerald: '#059669',
    background_slate: '#F8FAFC',
    dark_surface: '#091E33',
    font_family_ar: 'Cairo',
    font_family_en: 'Inter',
    header_style: 'classic',
  },

  president_message: {
    name: {
      ar: 'أ.د. عصام النجار',
      en: 'Prof. Dr. Essam El-Naggar',
    },
    title: {
      ar: 'رئيس جامعة إيجي تك للتكنولوجيا والعلوم التطبيقية',
      en: 'President of EgyiTech University',
    },
    avatar_url:
      'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
    quote: {
      ar: 'التعليم التكنولوجي ليس مجرد نقل للمعرفة، بل هو تمكين للأجيال القادمة لصناعة المستقبل وقيادة الابتكار الوطني.',
      en: 'Technological education is not merely knowledge transfer; it empowers next-generation leaders to shape the future.',
    },
    message: {
      ar: 'أهلاً بكم في رحاب جامعة إيجي تك، الصرح الأكاديمي الرائد الذي تأسس ليكون منارة تعليمية وبحثية تواكب أحدث معايير الثورة التكنولوجية وتلبي متطلبات التنمية الوطنية الشاملة.',
      en: 'Welcome to EgyiTech University, a leading academic sanctuary founded to pioneer applied tech education, artificial intelligence, and regional research transformation.',
    },
    signature_url: '',
  },

  hero_slider: {
    slides: [
      {
        id: 1,
        badge: {
          ar: 'الريادة في الذكاء الاصطناعي',
          en: 'AI Leadership',
        },
        title: {
          ar: 'الريادة في الذكاء الاصطناعي والتكنولوجيا الحديثة',
          en: 'Pioneering Artificial Intelligence & Advanced Engineering',
        },
        subtitle: {
          ar: 'برامج أكاديمية متطورة ومعتمدة دولياً تؤهلك لسوق العمل العالمي في أحدث التخصصات التكنولوجية.',
          en: 'Cutting-edge accredited academic programs preparing elite graduates for regional and international technology markets.',
        },
        cta_text: {
          ar: 'قدم طلب التحاق الآن',
          en: 'Apply for Admission',
        },
        cta_link: '/admissions',
        secondary_text: {
          ar: 'استكشف البرامج الأكاديمية',
          en: 'Explore Degree Programs',
        },
        secondary_link: '/programs',
        image_url:
          'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1000&q=80',
      },
      {
        id: 2,
        badge: {
          ar: 'باب القبول والتسجيل مفتوح',
          en: 'Admissions Open 2025/2026',
        },
        title: {
          ar: 'انضم إلى نخبة رواد التكنولوجيا والابتكار',
          en: 'Join the Elite Community of Innovators',
        },
        subtitle: {
          ar: 'منح دراسية للمتفوقين، تدريب عملي في كبرى الشركات، وشراكات أكاديمية مع جامعات عالمية.',
          en: 'Merit scholarships, hands-on enterprise internships, and dual-degree pathways with prestigious international institutions.',
        },
        cta_text: {
          ar: 'سجل في اختبارات القبول',
          en: 'Register for Placement Test',
        },
        cta_link: '/admissions',
        secondary_text: {
          ar: 'متابعة الطلب',
          en: 'Track Application',
        },
        secondary_link: '/admissions/track',
        image_url:
          'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1000&q=80',
      },
      {
        id: 3,
        badge: {
          ar: 'أبحاث ومعامل متطورة',
          en: 'Cutting-edge Research',
        },
        title: {
          ar: 'بيئة بحثية متقدمة وشراكات صناعية رائدة',
          en: 'Cutting-Edge Research Hub & Industrial Partnerships',
        },
        subtitle: {
          ar: 'معامل متخصصة فائقة التطور، حاضنات أعمال تكنولوجية، ومشاريع تخرج مرتبطة بالصناعة.',
          en: 'State-of-the-art specialized laboratories, tech incubators, and direct enterprise-aligned graduation projects.',
        },
        cta_text: {
          ar: 'تعرف على كلياتنا',
          en: 'Explore Our Colleges',
        },
        cta_link: '/colleges',
        secondary_text: {
          ar: 'هيئة التدريس',
          en: 'Meet Our Faculty',
        },
        secondary_link: '/faculty',
        image_url:
          'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1000&q=80',
      },
    ],
  },

  contact_info: {
    hotline: '19850',
    phone: '+20 2 2456 7890',
    phone_secondary: '+20 2 2456 7891',
    email: 'info@egyitech.edu.eg',
    admissions_email: 'admissions@university.edu.eg',
    support_email: 'support@egyitech.edu.eg',
    address: {
      ar: 'طريق القاهرة - الإسماعيلية الصحراوي، مدينة المعرفة والتكنولوجيا، مصر',
      en: 'Cairo - Ismailia Desert Road, Knowledge & Tech City, Egypt',
    },
    working_hours: {
      ar: 'الأحد إلى الخميس: 8:30 ص - 4:00 م',
      en: 'Sunday to Thursday: 8:30 AM - 4:00 PM',
    },
    google_maps_embed_url: 'https://maps.google.com/?q=Cairo,Egypt',
  },

  social_links: {
    facebook: 'https://facebook.com/egyitech.univ',
    twitter: 'https://x.com/egyitech_univ',
    linkedin: 'https://linkedin.com/school/egyitech-university',
    youtube: 'https://youtube.com/@egyitech_univ',
    instagram: 'https://instagram.com/egyitech.univ',
    telegram: 'https://t.me/egyitech_channel',
  },

  footer_info: {
    about_text: {
      ar: 'جامعة إيجي تك هي مؤسسة تعليمية وتكنولوجية متقدمة معتمدة من المجلس الأعلى للجامعات ووزارة التعليم العالي، تهدف إلى إعداد كوادر علمية ومهنية رائدة في مجالات التكنولوجيا الحديثة والهندسة والإدارة الرقمية.',
      en: 'EgyiTech University of Technology and Applied Sciences is a leading academic institution accredited by the Supreme Council of Universities, dedicated to graduating elite professionals in engineering, artificial intelligence, and digital management.',
    },
    accreditation_text: {
      ar: 'معتمدة من الهيئة القومية لضمان جودة التعليم والاعتماد (NAQAAE)',
      en: 'Accredited by the National Authority for Quality Assurance and Accreditation of Education (NAQAAE)',
    },
    iso_text: {
      ar: 'حاصلة على شهادة الجودة العالمية ISO 9001:2015',
      en: 'ISO 9001:2015 Certified Quality Education Standards',
    },
    copyright_text: {
      ar: 'جميع الحقوق محفوظة © 2025 جامعة إيجي تك للتكنولوجيا والعلوم التطبيقية.',
      en: 'All Rights Reserved © 2025 EgyiTech University of Technology & Applied Sciences.',
    },
  },

  top_announcement_bar: {
    is_enabled: true,
    text: {
      ar: 'بدء تلقي طلبات الالتحاق للعام الأكاديمي 2025 / 2026 لجميع الكليات عبر بوابة القبول الإلكتروني',
      en: 'Admissions for Academic Year 2025/2026 are now open across all faculties via the online portal',
    },
    link_url: '/admissions',
    badge: {
      ar: 'إعلان هام',
      en: 'Announcement',
    },
  },

  site_statistics: {
    title: {
      ar: 'جامعة إيجي تك في أرقام',
      en: 'EgyiTech at a Glance',
    },
    subtitle: {
      ar: 'إنجازات تبرز التميز الأكاديمي والريادة الوطنية والبحثية',
      en: 'Milestones demonstrating academic prestige, research excellence, and national impact',
    },
    items: [
      {
        id: 'students',
        label: {
          ar: 'طالب وطالبة مقيدين',
          en: 'Enrolled Students',
        },
        value: '15,400+',
        prefix: '',
        suffix: '+',
        icon: 'Users',
        color: 'gold',
        is_active: true,
        order: 1,
      },
      {
        id: 'faculty',
        label: {
          ar: 'عضو هيئة تدريس وباحث',
          en: 'Faculty & Researchers',
        },
        value: '480+',
        prefix: '',
        suffix: '+',
        icon: 'GraduationCap',
        color: 'emerald',
        is_active: true,
        order: 2,
      },
      {
        id: 'programs',
        label: {
          ar: 'برنامج أكاديمي معتمد',
          en: 'Accredited Programs',
        },
        value: '28',
        prefix: '',
        suffix: '',
        icon: 'BookOpen',
        color: 'gold',
        is_active: true,
        order: 3,
      },
      {
        id: 'employment',
        label: {
          ar: 'نسبة توظيف الخريجين',
          en: 'Graduate Employment Rate',
        },
        value: '96.8%',
        prefix: '',
        suffix: '%',
        icon: 'Award',
        color: 'emerald',
        is_active: true,
        order: 4,
      },
      {
        id: 'research',
        label: {
          ar: 'بحث علمي منشور دولياً',
          en: 'Global Indexed Publications',
        },
        value: '1,350+',
        prefix: '',
        suffix: '+',
        icon: 'FileText',
        color: 'gold',
        is_active: true,
        order: 5,
      },
      {
        id: 'partners',
        label: {
          ar: 'شريك صناعي وتكنولوجي',
          en: 'Industrial & Tech Partners',
        },
        value: '65+',
        prefix: '',
        suffix: '+',
        icon: 'Building2',
        color: 'emerald',
        is_active: true,
        order: 6,
      },
    ],
  },
}
